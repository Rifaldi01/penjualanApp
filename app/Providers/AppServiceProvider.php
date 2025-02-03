<?php

namespace App\Providers;

use App\Models\Permintaan;
use App\Models\PermintaanItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $notif = 0;

            if (Auth::check()) { // Pastikan pengguna sudah login
                $notif = Permintaan::where('status', 'pending')
                    ->where('divisi_id_asal', Auth::user()->divisi_id)
                    ->count();
            }

            $view->with('notif', $notif);
        });
        View::composer('*', function ($view) {
            $minta = 0;

            if (Auth::check()) { // Pastikan pengguna sudah login
                $minta = Permintaan::where('status', 'disetujui')
                    ->where('divisi_id_tujuan', Auth::user()->divisi_id)
                    ->count();
            }

            $view->with('minta', $minta);
        });
        View::composer('*', function ($view) {
            $notiff = 0;

            if (Auth::check()) { // Pastikan pengguna sudah login
                $notiff = Permintaan::where('status', 'pending')
                    ->orWhere('status', 'disetujui')
                    ->where('divisi_id_tujuan', Auth::user()->divisi_id)
                    ->count();
            }

            $view->with('notiff', $notiff);
        });

        View::composer('*', function ($view) {
            $notifitem = 0;

            if (Auth::check()) { // Pastikan pengguna sudah login
                $notifitem = PermintaanItem::where('status', 'pending')
                    ->where('divisi_id_asal', Auth::user()->divisi_id)
                    ->count();
            }

            $view->with('notifitem', $notifitem);
        });
        View::composer('*', function ($view) {
            $mintaitem = 0;

            if (Auth::check()) { // Pastikan pengguna sudah login
                $mintaitem = PermintaanItem::where('status', 'disetujui')
                    ->where('divisi_id_tujuan', Auth::user()->divisi_id)
                    ->count();
            }

            $view->with('mintaitem', $mintaitem);
        });
        View::composer('*', function ($view) {
            $notiffitem = 0;

            if (Auth::check()) { // Pastikan pengguna sudah login
                $notiffitem = PermintaanItem::where('status', 'pending')
                    ->orWhere('status', 'disetujui')
                    ->where('divisi_id_tujuan', Auth::user()->divisi_id)
                    ->count();
            }

            $view->with('notiffitem', $notiffitem);
        });
    }
}
