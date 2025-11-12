<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add([
                'text' => 'Dashboard',
                'url'  => route('dashboard'),
                'icon' => 'fas fa-tachometer-alt',
            ]);

            if (auth()->user()->can('user-management')) {
                $event->menu->add([
                    'text' => 'User Management',
                    'icon' => 'fas fa-users-cog',
                    'submenu' => [
                        ['text' => 'Users', 'url' => route('admin.users.index'), 'icon' => 'fas fa-user'],
                        ['text' => 'Permissions', 'url' => route('admin.permissions.index'), 'icon' => 'fas fa-lock'],
                    ],
                ]);
            }

            if (auth()->user()->can('data-import')) {
                $event->menu->add([
                    'text' => 'Data Import',
                    'url'  => route('admin.data-import.index'),
                    'icon' => 'fas fa-tachometer-alt',
                ]);

                $user = auth()->user();
                if (!$user) return;

                $importedSubmenu = [];
                $allImportConfigs = config('imports', []);

                foreach ($allImportConfigs as $importKey => $cfg) {
                    $perm = $cfg['permission_required'] ?? null;

                    // Skip if user does not have permission
                    if ($perm && !$user->can($perm)) continue;

                    $parentLabel = $cfg['label'] ?? ucfirst($importKey);

                    if (!empty($cfg['files']) && is_array($cfg['files'])) {
                        foreach ($cfg['files'] as $fileKey => $fileCfg) {
                            $fileLabel = $fileCfg['label'] ?? ucfirst($fileKey);

                            $importedSubmenu[] = [
                                'text' => "{$parentLabel} - {$fileLabel}",
                                'url'  => route('admin.imported.show', [$importKey, $fileKey]),
                                'icon' => 'fas fa-file-alt',
                            ];
                        }
                    } else {
                        // If no files, just show parent label
                        $importedSubmenu[] = [
                            'text' => $parentLabel,
                            'url'  => route('admin.imported.show', [$importKey]),
                            'icon' => 'fas fa-file-alt',
                        ];
                    }
                }

                // Placeholder if no submenu items available
                if (empty($importedSubmenu)) {
                    $importedSubmenu = [
                        ['text' => 'Loading...', 'url' => '#', 'icon' => 'fas fa-circle-notch'],
                    ];
                }

                $event->menu->add([
                    'text' => 'Imported Data',
                    'url'  => route('admin.imported.index'),
                    'icon' => 'fas fa-tachometer-alt',
                    'submenu' => $importedSubmenu,
                ]);

                $event->menu->add([
                    'text' => 'Imports',
                    'url'  => route('admin.imports.index'),
                    'icon' => 'fas fa-tachometer-alt',
                ]);
            }

            $event->menu->add([
                'text' => 'Notifications',
                'key' => 'notifications',
                'icon' => 'fas fa-bell',
                'topnav_right' => true,
                'label' => auth()->user()->unreadNotifications()->count(),
                'label_color' => 'danger',
                'url' => route('notifications.index'),
            ]);
        });
    }
}
