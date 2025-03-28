@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto px-4 py-8">
   
    <!-- Search and Filter Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Search by name or email..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           value="{{ request('search') }}">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex gap-4">
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button id="resetFilters" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $users->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $users->where('last_active_at', '>=', now()->subMinutes(5))->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Admin Users</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $users->where('role', 'admin')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Users Today -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">New Today</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $users->where('created_at', '>=', now()->startOfDay())->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->last_active_at && $user->last_active_at->diffInMinutes(now()) < 5 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->last_active_at && $user->last_active_at->diffInMinutes(now()) < 5 ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($user->last_active_at)
                                {{ $user->last_active_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-3">
                            <a class="text-blue-600 hover:text-blue-900" href="{{route('users.activities', $user->id)}}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
        </div>
        <div class="flex space-x-2">
            @if($users->hasPages())
                <!-- Previous Page Link -->
                @if($users->onFirstPage())
                    <button disabled class="px-3 py-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                        Previous
                    </button>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1 rounded-md bg-blue-500 text-white hover:bg-blue-600">
                        Previous
                    </a>
                @endif

                <!-- Page Numbers -->
                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="px-3 py-1 rounded-md bg-blue-500 text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                <!-- Next Page Link -->
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1 rounded-md bg-blue-500 text-white hover:bg-blue-600">
                        Next
                    </a>
                @else
                    <button disabled class="px-3 py-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                        Next
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetFilters = document.getElementById('resetFilters');
    let searchTimeout;

    function updateFilters() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;
        
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (status) params.append('status', status);
        
        window.location.href = `{{ route('users.index') }}?${params.toString()}`;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(updateFilters, 500);
    });

    statusFilter.addEventListener('change', updateFilters);

    resetFilters.addEventListener('click', function() {
        window.location.href = '{{ route('users.index') }}';
    });
});




</script>
@endpush 