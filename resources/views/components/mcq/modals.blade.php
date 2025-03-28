<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4">Confirm Logout</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to logout? This will end your exam session and all progress will be lost.</p>
        <div class="flex justify-end space-x-3">
            <button id="cancel-logout" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Cancel
            </button>
            <button id="confirm-logout" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Logout
            </button>
        </div>
    </div>
</div>

<!-- Security Warning Modal -->
<div id="security-warning-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4 text-red-600">Security Warning</h3>
        <p class="text-gray-600 mb-4" id="security-warning-message"></p>
        <div class="flex justify-end">
            <button id="acknowledge-warning" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Acknowledge
            </button>
        </div>
    </div>
</div> 