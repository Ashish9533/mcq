<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;


class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search in details
        if ($request->filled('search')) { 
            $query->where('details', 'like', '%' . $request->search . '%');
        }

        // Get unique actions for filter dropdown
        $actions = ActivityLog::distinct()->pluck('action');

        // Get all users for filter dropdown
        $users = User::orderBy('name')->get();

        // Paginate results with 10 items per page
        $activities = $query->latest()->paginate(10)->withQueryString();

        return view('activity-log.index', compact('activities', 'users', 'actions'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('last_active_at', '>=', now()->subMinutes(5));
            } else {
                $query->where(function($q) {
                    $q->whereNull('last_active_at')
                      ->orWhere('last_active_at', '<', now()->subMinutes(5));
                });
            }
        }

        // Get users with pagination
        $users = $query->latest()->paginate(2)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('activity-log.users', compact('users'))->render(),
                'pagination' => view('components.pagination', ['paginator' => $users])->render()
            ]);
        }

        return view('activity-log.users', compact('users'));
    }


    public function userActivities(Request $request ,$user=null){
  


        $query = Activity::query();

        // Filter by user_id
        if (isset($user)) {
            $query->where('causer_id', $user);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('log_name', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search in details
        if ($request->filled('search')) { 
            $query->where('details', 'like', '%' . $request->search . '%');
        }

        // Get unique actions for filter dropdown
        $actions = Activity::distinct()->pluck('log_name');

     

        // Paginate results with 10 items per page
        $activities = $query->latest()->paginate(10)->withQueryString();

        return view('activity-log.index', compact('activities', 'actions'));











    }



    public function logActivity(Request $request)
    {
        // Ensure the user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get message from the request
        $message = $request->input('message', 'No message provided');

        // Log the activity 
        activity('Suspicious Activity') // Use the default log name
            ->causedBy(auth()->user()) // Associate the activity with the logged-in user
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ])
            ->log($message);

        return response()->json(['success' => true, 'message' => 'Activity logged']);
    }









} 