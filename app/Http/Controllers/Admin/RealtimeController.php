<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Document;
use App\Models\NotificationLog;
use App\Models\TaxReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RealtimeController extends Controller
{
    /**
     * Kirim event SSE saat data dashboard berubah.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $response = new StreamedResponse(static function () use ($request): void {
            $lastSignature = (string) $request->query('since');
            $lastPing = time();

            echo "retry: 3000\n\n";
            flush();

            while (true) {
                if (connection_aborted()) {
                    break;
                }

                $current = self::signature();

                if ($current !== $lastSignature) {
                    $lastSignature = $current;
                    echo "id: {$current}\nevent: refresh\ndata: {}\n\n";
                    flush();
                } elseif (time() - $lastPing >= 15) {
                    echo ": ping\n\n";
                    $lastPing = time();
                    flush();
                }

                usleep(3_000_000);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * Signature perubahan: gabungan timestamp update terbaru setiap tabel inti.
     */
    private static function signature(): string
    {
        $latest = fn (string $table) => DB::table($table)->max('updated_at') ?? '0';

        return implode('|', [
            $latest((new ClientProfile)->getTable()),
            $latest((new TaxReport)->getTable()),
            $latest((new Document)->getTable()),
            $latest((new NotificationLog)->getTable()),
        ]);
    }
}
