<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramLog;
use Illuminate\Http\Request;

class TelegramLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TelegramLog::query();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhere('chat_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);

        $stats = [
            'total' => TelegramLog::count(),
            'sent' => TelegramLog::where('status', 'sent')->count(),
            'failed' => TelegramLog::where('status', 'failed')->count(),
        ];

        return view('admin.telegram-logs', compact('logs', 'stats'));
    }

    public function show($id)
    {
        $log = TelegramLog::findOrFail($id);
        return response()->json($log);
    }

    public function destroy($id)
    {
        $log = TelegramLog::findOrFail($id);
        $log->delete();

        return response()->json(['message' => 'Log deleted successfully']);
    }
}
