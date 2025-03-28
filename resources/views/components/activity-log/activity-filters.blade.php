@props(['users', 'actions'])

<div class="bg-white shadow rounded-lg p-4 mb-6">
    <form method="GET" action="{{ route('activity-log.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- User Filter -->
            <div>
                <label for="user" class="block text-sm font-medium text-gray-700">User</label>
                <select name="user" id="user" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                <select name="action" id="action" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range Filter -->
            <div>
                <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                <select name="date_range" id="date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
        </div>

        <!-- Search Input -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                    placeholder="Search in details...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('activity-log.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Reset
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Apply Filters
            </button>
        </div>
    </form>
</div> 