@props(['activities'])

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <h2 class="text-lg font-semibold text-gray-800">Activity Log</h2>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                        {{ $activities->total() }} Total
                    </span>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                        {{ $activities->where('created_at', '>=', now()->startOfDay())->count() }} Today
                    </span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative w-full sm:w-64">
                    <input type="text" 
                           placeholder="Search activities..." 
                           class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           id="activity-search">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="toggleGrouping()" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </button>
                    <button onclick="exportActivities()" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity List -->
    <div class="divide-y divide-gray-200" id="activity-list">
        @forelse($activities->groupBy(function($activity) {
            return $activity->created_at->format('Y-m-d');
        }) as $date => $groupedActivities)
            <div class="activity-group">
                <div class="sticky top-0 bg-gray-50 px-6 py-2 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h3>
                </div>
                @foreach($groupedActivities as $activity)
                    <div class="activity-item p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-start space-x-4">
                            <!-- Activity Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                    {{ $activity->action === 'login' ? 'bg-green-100 text-green-600' : 
                                       ($activity->action === 'logout' ? 'bg-red-100 text-red-600' : 
                                       ($activity->action === 'exam_started' ? 'bg-blue-100 text-blue-600' :
                                       ($activity->action === 'exam_submitted' ? 'bg-purple-100 text-purple-600' :
                                       'bg-gray-100 text-gray-600'))) }}">
                                    @switch($activity->action)
                                        @case('login')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                            </svg>
                                            @break
                                        @case('logout')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            @break
                                        @case('exam_started')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            @break
                                        @case('exam_submitted')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            @break
                                        @default
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                    @endswitch
                                </div>
                            </div>

                            <!-- Activity Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ ucfirst($activity->log_name) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $activity->description }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-500">
                                            {{ $activity->created_at->format('h:i A') }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            IP: {{ $activity->properties['ip'] ?? "N/A" }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p><span class="font-medium">User Agent:</span> {{ $activity->properties['user_agent'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No activity logs</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by performing some actions in the system.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} results
            </div>
            <div class="flex space-x-2">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isGrouped = true;
    const searchInput = document.getElementById('activity-search');
    const activityList = document.getElementById('activity-list');
    const activityItems = document.querySelectorAll('.activity-item');
    const activityGroups = document.querySelectorAll('.activity-group');

    // Search functionality with debounce
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase();
                let hasVisibleItems = false;

                activityItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const isVisible = text.includes(searchTerm);
                    item.style.display = isVisible ? 'block' : 'none';
                    if (isVisible) hasVisibleItems = true;
                });

                // Show/hide group headers based on visible items
                activityGroups.forEach(group => {
                    const visibleItems = group.querySelectorAll('.activity-item[style="display: block"]');
                    group.style.display = visibleItems.length > 0 ? 'block' : 'none';
                });

                // Show no results message if needed
                const noResultsMessage = document.querySelector('.text-center');
                if (noResultsMessage) {
                    noResultsMessage.style.display = hasVisibleItems ? 'none' : 'block';
                }
            }, 300);
        });
    }

    // Toggle grouping function
    window.toggleGrouping = function() {
        isGrouped = !isGrouped;
        activityGroups.forEach(group => {
            if (isGrouped) {
                group.style.display = 'block';
                group.querySelector('.sticky').style.display = 'block';
            } else {
                group.style.display = 'block';
                group.querySelector('.sticky').style.display = 'none';
            }
        });
    };

    // Export functionality
    window.exportActivities = function() {
        // Create CSV content
        const headers = ['Action', 'Details', 'Date', 'Time', 'IP Address'];
        const rows = Array.from(activityItems).map(item => {
            const action = item.querySelector('.text-gray-900').textContent;
            const details = item.querySelector('.text-gray-500').textContent;
            const dateTime = item.querySelector('.text-sm.text-gray-500').textContent;
            const ip = item.querySelector('.text-sm.text-gray-500:last-child').textContent.replace('IP: ', '');
            return [action, details, dateTime, ip];
        });

        const csvContent = [
            headers.join(','),
            ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
        ].join('\n');

        // Create and trigger download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `activity-log-${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
    };

    // Add smooth scroll to top when clicking on activity items
    activityItems.forEach(item => {
        item.addEventListener('click', () => {
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
});
</script> 