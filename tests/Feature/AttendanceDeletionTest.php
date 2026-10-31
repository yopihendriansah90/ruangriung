<?php

namespace Tests\Feature;

use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Services\CheckInService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_confirm_attendance_deletion_and_participant_can_check_in_again(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);
        $attendance = $this->attendance($admin);
        $registration = $attendance->registration;

        $page = Livewire::test(ListAttendances::class)
            ->assertCanSeeTableRecords([$attendance])
            ->mountTableAction('delete', $attendance);

        $this->assertStringContainsString(
            'Data registrasi peserta tetap tersimpan',
            $page->instance()->getMountedTableAction()->getModalDescription(),
        );

        $this->assertModelExists($attendance);

        $page->callMountedTableAction()
            ->assertHasNoTableActionErrors()
            ->assertNotified('Data kehadiran berhasil dihapus.')
            ->assertCanNotSeeTableRecords([$attendance]);

        $this->assertModelMissing($attendance);
        $this->assertModelExists($registration);
        $this->assertNull($registration->fresh()->attendance);

        $this->postJson(route('check-in.store'), [
            'code' => $registration->registration_code,
            'method' => Attendance::METHOD_QR_CODE,
        ])->assertSuccessful();

        $this->assertNotNull($registration->fresh()->attendance);
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_bulk_delete_only_removes_selected_attendances_and_keeps_registrations(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);
        $attendances = collect([
            $this->attendance($admin, 1),
            $this->attendance($admin, 2),
            $this->attendance($admin, 3),
        ]);
        $selected = $attendances->take(2);

        $page = Livewire::test(ListAttendances::class)
            ->mountTableBulkAction('delete', $selected);

        $this->assertDatabaseCount('attendances', 3);

        $page->callMountedTableBulkAction()
            ->assertHasNoTableBulkActionErrors()
            ->assertNotified('Data kehadiran terpilih berhasil dihapus.')
            ->assertCanNotSeeTableRecords($selected)
            ->assertCanSeeTableRecords([$attendances->last()]);

        foreach ($selected as $attendance) {
            $this->assertModelMissing($attendance);
        }
        $this->assertModelExists($attendances->last());
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseCount('registrations', 3);
    }

    private function attendance(User $admin, int $number = 1): Attendance
    {
        $event = Event::create([
            'name' => "Event {$number}",
            'slug' => "event-{$number}",
            'event_date' => now()->addWeek(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Event pengujian kehadiran.',
            'location_name' => 'Aula Komunitas',
            'location_address' => 'Tasikmalaya',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);
        $registration = Registration::create([
            'event_id' => $event->id,
            'registration_code' => "RR-DELETE-{$number}",
            'name' => "Peserta {$number}",
            'email' => "peserta{$number}@example.com",
            'phone' => '081234567890',
            'gender' => 'female',
            'data_consent_at' => now(),
            'status' => Registration::STATUS_REGISTERED,
            'invitation_status' => Registration::INVITATION_PENDING,
        ]);

        return app(CheckInService::class)->checkIn($registration->registration_code, $admin);
    }
}
