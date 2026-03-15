<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(ReportService $reportService)
    {
        if (request()->ajax()) {
            $stats = $reportService->getDashboardStats();
            $stats['reminders'] = Reminder::where('user_id', auth()->id())
                ->orderBy('is_completed')
                ->orderBy('due_date')
                ->take(10)
                ->get();
            return response()->json($stats);
        }
        return view('dashboard');
    }

    public function storeReminder(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date',
        ]);

        $reminder = Reminder::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return response()->json(['success' => true, 'data' => $reminder]);
    }

    public function toggleReminder(Reminder $reminder)
    {
        if ($reminder->user_id !== auth()->id()) abort(403);
        $reminder->update(['is_completed' => !$reminder->is_completed]);
        return response()->json(['success' => true, 'data' => $reminder]);
    }

    public function deleteReminder(Reminder $reminder)
    {
        if ($reminder->user_id !== auth()->id()) abort(403);
        $reminder->delete();
        return response()->json(['success' => true]);
    }
}
