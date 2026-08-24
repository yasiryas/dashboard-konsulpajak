<?php

namespace App\Console\Commands;

use App\Enums\NotifikasiStatus;
use App\Enums\NotifikasiTipe;
use App\Models\NotificationLog;
use App\Models\TaxReport;
use App\Services\WhatsappNotifierService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendDeadlineReminders extends Command
{
    protected $signature = 'send:deadline-reminders';

    protected $description = 'Kirim reminder WA H-7 dan H-1 untuk laporan pajak yang belum selesai';

    public function handle(WhatsappNotifierService $notifier): int
    {
        $total = 0;

        foreach ([7, 1] as $offset) {
            $targetDate = Carbon::today()->addDays($offset);
            $label = $offset === 7 ? '7 hari' : '1 hari (besok)';

            $reports = TaxReport::query()
                ->whereDate('deadline_tanggal', $targetDate)
                ->where('status', '!=', 'selesai')
                ->with(['client.user', 'client:id,nama_entitas'])
                ->get();

            foreach ($reports as $report) {
                if ($this->alreadySentToday($report)) {
                    continue;
                }

                $phone = $report->client->user->phone ?? null;

                if (blank($phone)) {
                    $this->warn("Lewati {$report->client->nama_entitas}: nomor HP klien kosong.");

                    continue;
                }

                $message = sprintf(
                    "Halo %s, mengingatkan laporan %s periode %s berdeadline %s (%s lagi). Mohon siapkan dokumennya ya.",
                    $report->client->nama_entitas,
                    $report->jenis_laporan->label(),
                    $report->periode,
                    $report->deadline_tanggal?->format('d M Y'),
                    $label,
                );

                $log = $notifier->send($phone, $message, NotifikasiTipe::ReminderDeadline->value, $report->client_id);

                $total += $log->status === NotifikasiStatus::Terkirim ? 1 : 0;
                $this->line("{$report->client->nama_entitas}: {$log->status->label()}");
            }
        }

        $this->info("Reminder terkirim: {$total}.");

        return self::SUCCESS;
    }

    protected function alreadySentToday(TaxReport $report): bool
    {
        return NotificationLog::query()
            ->where('client_id', $report->client_id)
            ->where('tipe', NotifikasiTipe::ReminderDeadline)
            ->where('status', NotifikasiStatus::Terkirim)
            ->whereDate('sent_at', today())
            ->exists();
    }
}
