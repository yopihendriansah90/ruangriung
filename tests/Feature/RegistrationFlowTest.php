<?php

namespace Tests\Feature;

use App\Mail\EventInvitationMail;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_participant_can_register_and_see_whatsapp_invitation(): void
    {
        $event = $this->event([
            'name' => 'Pertemuan<br>Ruang Riung',
            'whatsapp_group_url' => 'https://chat.whatsapp.com/example',
        ]);

        $response = $this->post(route('registrations.store', $event), [
            'name' => 'Budi MHC',
            'email' => 'BUDI@example.com',
            'phone' => '08123456789',
            'gender' => 'male',
            'data_consent' => '1', 'transfer_proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $registration = Registration::firstOrFail();

        $response->assertRedirect(route('registrations.success', $registration));
        $this->get(route('registrations.success', $registration))
            ->assertOk()
            ->assertSee('Sampai bertemu, Budi MHC!')
            ->assertSee('Pertemuan Ruang Riung')
            ->assertDontSee('Pertemuan<br>Ruang Riung')
            ->assertSee('https://chat.whatsapp.com/example');
        $this->assertSame('budi@example.com', $registration->email);
        Storage::disk('local')->assertExists($registration->transfer_proof_path);
        $url = route('exports.transfer-proof', $registration);
        $this->get($url)->assertRedirect();
        $this->actingAs(User::factory()->create())->get($url)
            ->assertOk()->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_transfer_proof_rejects_missing_non_image_disguised_and_oversized_files(): void
    {
        $event = $this->event();
        $payload = [
            'name' => 'Sari', 'email' => 'sari@example.com', 'phone' => '08123456789',
            'gender' => 'female', 'data_consent' => '1',
        ];

        $disguised = UploadedFile::fake()->createWithContent('proof.jpg', 'not an image');

        foreach ([null, UploadedFile::fake()->create('proof.pdf', 10, 'application/pdf'),
            new UploadedFile($disguised->getPathname(), 'proof.jpg', 'image/jpeg', null, true),
            UploadedFile::fake()->image('proof.jpg')->size(2049),
            UploadedFile::fake()->image('proof.gif'),
        ] as $file) {
            $this->post(route('registrations.store', $event), [...$payload, 'transfer_proof' => $file])
                ->assertSessionHasErrors('transfer_proof');
        }

        $this->assertDatabaseCount('registrations', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_transfer_proof_accepts_exactly_two_mb_and_failed_registration_leaves_no_extra_file(): void
    {
        $event = $this->event();
        $payload = [
            'name' => 'Sari', 'email' => 'sari@example.com', 'phone' => '08123456789',
            'gender' => 'female', 'data_consent' => '1',
        ];
        $this->post(route('registrations.store', $event), [
            ...$payload, 'transfer_proof' => UploadedFile::fake()->image('proof.png')->size(2048),
        ])->assertSessionHasNoErrors();
        $this->post(route('registrations.store', $event), [
            ...$payload, 'transfer_proof' => UploadedFile::fake()->image('duplicate.png'),
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('registrations', 1);
        $this->assertCount(1, Storage::disk('local')->allFiles());
    }

    public function test_email_can_only_register_once_per_event_but_can_register_again_on_other_event(): void
    {
        $event = $this->event();
        $payload = [
            'name' => 'Sari',
            'email' => 'sari@example.com',
            'phone' => '08123456789',
            'gender' => 'female',
            'data_consent' => '1', 'transfer_proof' => UploadedFile::fake()->image('proof.jpg'),
        ];

        $this->post(route('registrations.store', $event), $payload)->assertRedirect();
        $this->from(route('events.show', $event))
            ->post(route('registrations.store', $event), $payload)
            ->assertSessionHasErrors('email');

        $otherEvent = $this->event(['slug' => 'other-event', 'name' => 'Other Event']);
        $this->post(route('registrations.store', $otherEvent), $payload)->assertRedirect();
        $this->assertDatabaseCount('registrations', 2);
    }

    public function test_limited_event_rejects_registration_when_full(): void
    {
        $event = $this->event(['capacity_type' => Event::CAPACITY_LIMITED, 'capacity' => 1]);
        $payload = [
            'name' => 'Peserta Pertama', 'email' => 'first@example.com', 'phone' => '08123456789',
            'gender' => 'male', 'data_consent' => '1', 'transfer_proof' => UploadedFile::fake()->image('proof.jpg'),
        ];
        $this->post(route('registrations.store', $event), $payload)->assertRedirect();

        $this->post(route('registrations.store', $event), [
            ...$payload, 'name' => 'Peserta Kedua', 'email' => 'second@example.com',
        ])->assertSessionHasErrors('event');
    }

    public function test_admin_can_send_invitation_and_check_in_once(): void
    {
        Mail::fake();
        $admin = User::factory()->create();
        $event = $this->event();
        $registration = Registration::create([
            'event_id' => $event->id,
            'registration_code' => 'MHC-TESTCODE01',
            'name' => 'Dina',
            'email' => 'dina@example.com',
            'phone' => '0812',
            'gender' => 'female',
            'data_consent_at' => now(),
        ]);

        app(InvitationService::class)->send($registration);
        Mail::assertSent(EventInvitationMail::class);
        $this->assertSame(Registration::INVITATION_SENT, $registration->fresh()->invitation_status);

        $this->actingAs($admin)
            ->postJson(route('check-in.store'), ['code' => $registration->registration_code])
            ->assertOk()
            ->assertJsonPath('participant', 'Dina');
        $this->assertDatabaseCount('attendances', 1);

        $this->actingAs($admin)
            ->postJson(route('check-in.store'), ['code' => $registration->registration_code])
            ->assertStatus(422);
        $this->assertInstanceOf(Attendance::class, $registration->fresh()->attendance);
    }

    public function test_invitation_email_embeds_qr_code_as_inline_image(): void
    {
        $registration = Registration::create([
            'event_id' => $this->event()->id,
            'registration_code' => 'MHC-INLINEQR01',
            'name' => 'Dina',
            'email' => 'dina@example.com',
            'phone' => '0812',
            'gender' => 'female',
            'data_consent_at' => now(),
        ]);

        $mail = new EventInvitationMail($registration->load('event'));
        $html = $mail->render();

        $this->assertStringContainsString('cid:'.$mail->qrCodeCid, $html);
        $this->assertStringContainsString('cid:'.$mail->logoCid, $html);
        $this->assertStringNotContainsString('data:image/png;base64,', $html);

        $mail->build();
        $message = new Email;
        foreach ($mail->callbacks as $callback) {
            $callback($message);
        }

        $this->assertSame($mail->qrCodeCid, $message->getAttachments()[0]->getContentId());
        $this->assertSame($mail->logoCid, $message->getAttachments()[1]->getContentId());
    }

    private function event(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'name' => 'MHC Test Event',
            'slug' => 'mhc-test-event-'.fake()->unique()->slug(),
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Test event.',
            'location_name' => 'Test location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ], $overrides));
    }
}
