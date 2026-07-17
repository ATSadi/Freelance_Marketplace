<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $item = Auth::user()->notifications()->where('id', $notification)->firstOrFail();
        $item->markAsRead();

        $url = $item->data['url'] ?? route('dashboard');

        return redirect($url);
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('status', 'notifications-read');
    }
}
