<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        try {
            $settings = \App\Models\Setting::pluck('value', 'key');

            if ($settings->isNotEmpty()) {
                $name = $settings->get('site_name', 'CRM Eucerin');
                config([
                    'adminlte.logo'            => '<b>' . e($name) . '</b>',
                    'adminlte.classes_sidebar' => $settings->get('sidebar_theme', 'sidebar-dark-primary elevation-4'),
                    'adminlte.classes_topnav'  => $settings->get('navbar_theme', 'navbar-white navbar-light'),
                    'adminlte.classes_auth_btn'=> $settings->get('auth_btn_class', 'btn-flat btn-danger'),
                ]);

                View::share('appSettings', $settings);
            }
        } catch (\Exception $e) {
            // DB not ready (first migration, etc.)
        }
    }
}
