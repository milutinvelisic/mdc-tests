<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
//        $menu = config('adminlte.menu');
//        foreach ($menu as &$item) {
//            if ($item['text'] === 'Notifications') {
//                $item['label'] = auth()->user()->unreadNotifications()->count();
//            }
//        }
//        config(['adminlte.menu' => $menu]);

        return view('dashboard');
    }
}
