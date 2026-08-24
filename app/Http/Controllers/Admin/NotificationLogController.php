<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotifikasiStatus;
use App\Enums\NotifikasiTipe;
use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = NotificationLog::query()
            ->with('client:id,nama_entitas');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($tipe = $request->string('tipe')->toString()) {
            $query->where('tipe', $tipe);
        }

        $logs = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Notifications/Index', [
            'logs' => [
                'data' => $logs->through(fn (NotificationLog $log) => [
                    'id' => $log->id,
                    'klien' => $log->client?->nama_entitas,
                    'tipe' => $log->tipe->label(),
                    'channel' => $log->channel->label(),
                    'status' => $log->status->value,
                    'statusLabel' => $log->status->label(),
                    'sentAt' => $log->sent_at?->format('d M Y H:i'),
                    'createdAt' => $log->created_at->format('d M Y H:i'),
                ])->all(),
                'meta' => [
                    'currentPage' => $logs->currentPage(),
                    'lastPage' => $logs->lastPage(),
                    'total' => $logs->total(),
                ],
            ],
            'filters' => [
                'status' => $request->string('status')->toString(),
                'tipe' => $request->string('tipe')->toString(),
            ],
        ]);
    }
}
