<?php

namespace App\Services;

use App\Events\SidebarNotificationUpdated;
use App\Models\Permintaan;
use App\Models\PermintaanItem;

class SidebarNotificationService
{
    public static function broadcast($divisiId)
    {
        $data = [

            'divisi_id' => $divisiId,

            // Accessories
            'notif' => Permintaan::where('status', 'pending')
                ->where('divisi_id_asal', $divisiId)
                ->count(),

            'minta' => Permintaan::where('status', 'disetujui')
                ->where('divisi_id_tujuan', $divisiId)
                ->count(),

            'notiff' => Permintaan::where('status', 'pending')
                ->orWhere('status', 'retur pending')
                ->where('divisi_id_asal', $divisiId)
                ->count(),

            'notifretur' => Permintaan::where('status', 'retur pending')
                ->where('divisi_id_asal', $divisiId)
                ->count(),

            // Item
            'notifitem' => PermintaanItem::where('status', 'pending')
                ->where('divisi_id_asal', $divisiId)
                ->count(),

            'mintaitem' => PermintaanItem::where('status', 'disetujui')
                ->where('divisi_id_tujuan', $divisiId)
                ->count(),

            'notiffitem' => PermintaanItem::where('status', 'pending')
                ->orWhere('status', 'retur pending')
                ->where('divisi_id_asal', $divisiId)
                ->count(),

            'notifreturitem' => PermintaanItem::where('status', 'retur pending')
                ->where('divisi_id_asal', $divisiId)
                ->count(),
        ];

        broadcast(new SidebarNotificationUpdated($data))->toOthers();
    }
}
