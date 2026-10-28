<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function store(Request $request, Event $event, RegistrationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'city' => ['nullable', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'referral_source' => ['nullable', 'string', 'max:100'],
            'data_consent' => ['accepted'],
            'transfer_proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'data_consent.accepted' => 'Persetujuan penggunaan data wajib dicentang.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'transfer_proof.required' => 'Bukti transfer wajib diunggah.',
            'transfer_proof.image' => 'Bukti transfer harus berupa gambar JPG, JPEG, PNG, atau WebP.',
            'transfer_proof.mimes' => 'Bukti transfer harus berupa gambar JPG, JPEG, PNG, atau WebP.',
            'transfer_proof.max' => 'Ukuran bukti transfer maksimal 2 MB.',
            'transfer_proof.uploaded' => 'Bukti transfer gagal diunggah. Pastikan ukuran maksimal 2 MB.',
        ]);

        unset($validated['data_consent']);

        $path = $validated['transfer_proof']->store('transfer-proofs', 'local');
        abort_unless($path, 500, 'Bukti transfer gagal disimpan. Silakan coba lagi.');
        unset($validated['transfer_proof']);
        $validated['transfer_proof_path'] = $path;

        try {
            $registration = $service->register($event, $validated);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return to_route('registrations.success', $registration);
    }

    public function success(Registration $registration)
    {
        $registration->load('event');

        return view('registrations.success', [
            'registration' => $registration,
            'title' => 'Pendaftaran Berhasil — Ruang Riung',
        ]);
    }

    public function transferProof(Registration $registration)
    {
        abort_unless($registration->transfer_proof_path && Storage::disk('local')->exists($registration->transfer_proof_path), 404);

        return Storage::disk('local')->response($registration->transfer_proof_path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
