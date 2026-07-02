<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $userId   = auth()->id();
        $type     = $request->input('type');       // 'in', 'out', or null
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $incomings = collect();
        $outgoings = collect();

        // ── Penerimaan Barang ────────────────────────────────
        if (!$type || $type === 'in') {
            $q = IncomingGood::with(['item', 'location'])
                ->where('user_id', $userId);

            if ($dateFrom) {
                $q->whereDate('created_at', '>=', Carbon::parse($dateFrom));
            }
            if ($dateTo) {
                $q->whereDate('created_at', '<=', Carbon::parse($dateTo));
            }

            $incomings = $q->latest()->get()->map(function ($inc) {
                return [
                    'id'         => 'IN-' . $inc->id,
                    'type'       => 'in',
                    'title'      => 'Penerimaan Barang',
                    'item'       => $inc->item->name ?? '-',
                    'quantity'   => $inc->quantity,
                    'location'   => $inc->location->code ?? '-',
                    'status'     => 'Selesai',
                    'status_color' => 'green',
                    'created_at' => $inc->created_at,
                ];
            });
        }

        // ── Pengeluaran Barang ───────────────────────────────
        if (!$type || $type === 'out') {
            $q = OutgoingGood::with(['item', 'location'])
                ->where('requested_by', $userId);

            if ($dateFrom) {
                $q->whereDate('created_at', '>=', Carbon::parse($dateFrom));
            }
            if ($dateTo) {
                $q->whereDate('created_at', '<=', Carbon::parse($dateTo));
            }

            $outgoings = $q->latest()->get()->map(function ($out) {
                $statusColor = match($out->status) {
                    'approved' => 'green',
                    'rejected' => 'red',
                    default    => 'yellow',
                };
                $statusText = match($out->status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default    => 'Menunggu Persetujuan',
                };
                return [
                    'id'           => 'OUT-' . $out->id,
                    'type'         => 'out',
                    'title'        => 'Pengeluaran Barang',
                    'item'         => $out->item->name ?? '-',
                    'quantity'     => $out->quantity,
                    'location'     => $out->location->code ?? '-',
                    'status'       => $statusText,
                    'status_color' => $statusColor,
                    'created_at'   => $out->created_at,
                ];
            });
        }

        // ── Gabung & Urutkan ─────────────────────────────────
        $all = $incomings->concat($outgoings)->sortByDesc('created_at')->values();

        // ── Pagination Manual ────────────────────────────────
        $perPage  = 15;
        $page     = $request->input('page', 1);
        $total    = $all->count();
        $items    = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $activities = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]);

        return view('petugas.activities.index', compact('activities'));
    }
}
