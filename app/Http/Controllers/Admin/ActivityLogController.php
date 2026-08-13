<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function($u) use ($request) {
                      $u->where('name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $logs = $query->latest()->paginate(20)->appends($request->all());

        // Get modules for filter
        $modules = ActivityLog::select('module')->distinct()->pluck('module');

        // Get actions for filter
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activity.index', compact('logs', 'modules', 'actions'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('admin.activity.show', compact('log'));
    }

    public function clear()
    {
        ActivityLog::truncate();
        return redirect()->route('admin.activity.index')
            ->with('success', 'Activity logs cleared successfully!');
    }

    public function clearOld()
    {
        // Delete logs older than 30 days
        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        return redirect()->route('admin.activity.index')
            ->with('success', 'Old activity logs cleared successfully!');
    }
}