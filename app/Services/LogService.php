<?php

namespace App\Services;

use App\Models\LogEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function log(Request $request, string $action, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        LogEntry::create([
            'user_id'    => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'ip'         => $request->ip(),
            'route'      => $request->path(),
            'method'     => $request->method(),
            'action'     => $action,
            'payload'    => $request->except(['password', 'password_confirmation']),
            'created_at' => now(),
        ]);
    }
}
