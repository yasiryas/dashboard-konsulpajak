<?php

namespace App\Services;

use App\Enums\NotifikasiChannel;
use App\Enums\NotifikasiStatus;
use App\Enums\NotifikasiTipe;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsappNotifierService
{
    /**
     * Kirim pesan WhatsApp via Fonnte dan catat hasilnya ke notifications_log.
     */
    public function send(string $phone, string $message, string $tipe, int $clientId): NotificationLog
    {
        $tipeEnum = NotifikasiTipe::tryFrom($tipe) ?? NotifikasiTipe::StatusUpdate;
        $token = config('services.fonnte.token');

        if (blank($token)) {
            return $this->log($clientId, $tipeEnum, NotifikasiStatus::Pending, null);
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->asForm()
                ->timeout(15)
                ->post(config('services.fonnte.url'), [
                    'target' => $this->normalizePhone($phone),
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $sent = $response->successful();

            return $this->log(
                $clientId,
                $tipeEnum,
                $sent ? NotifikasiStatus::Terkirim : NotifikasiStatus::Gagal,
                $sent ? now() : null,
            );
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim WhatsApp', ['client_id' => $clientId, 'error' => $e->getMessage()]);

            return $this->log($clientId, $tipeEnum, NotifikasiStatus::Gagal, null);
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }

    protected function log(int $clientId, NotifikasiTipe $tipe, NotifikasiStatus $status, ?object $sentAt): NotificationLog
    {
        return NotificationLog::create([
            'client_id' => $clientId,
            'tipe' => $tipe,
            'channel' => NotifikasiChannel::Whatsapp,
            'status' => $status,
            'sent_at' => $sentAt,
        ]);
    }
}
