<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Requests;
use App\Observers\RideObserver;
use App\Models\Review;
use App\Models\Settings;
use App\Observers\ReviewObserver;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Nothing reads these: the eight Firebase values were written into
        // cookies for a front end that never consumed them, and with the keys
        // unset bin2hex(null) warned on every single request.
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Requests::observe(RideObserver::class);
        Review::observe(ReviewObserver::class);

        View::composer('*', function ($view) {
            $app_setting = Settings::first();

            setcookie('admin_panel_color', $app_setting->adminpanel_color, time() + 3600, "/");
            setcookie('admin_panel_sec_color', $app_setting->adminpanel_sec_color, time() + 3600, "/");

            // Read straight off the env for the layout's Pusher client. This used to be
            // followed by $app_setting->save(), which wrote the settings row on every
            // single view render just to persist values env already holds.
            $app_setting->pusher_settings = [
                'pusher_app_id' => env('PUSHER_APP_ID'),
                'pusher_key'    => env('PUSHER_APP_KEY'),
                'pusher_secret' => env('PUSHER_APP_SECRET'),
                'pusher_cluster'=> env('PUSHER_APP_CLUSTER'),
            ];

            $view->with([
                'app_setting' => $app_setting,
                'admin_panel_color' => $app_setting->adminpanel_color ? $app_setting->adminpanel_color : '#facc15',
                'admin_panel_sec_color' => $app_setting->adminpanel_sec_color ? $app_setting->adminpanel_sec_color : '#6365f1',
            ]);
        });
    }
}
