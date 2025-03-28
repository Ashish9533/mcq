<div id="warningModal" class="fixed inset-0 flex items-center justify-center hidden z-50">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-lg p-6 z-50 max-w-sm mx-auto">
      <h2 class="text-xl font-bold mb-4">Warning</h2>
      <p class="mb-4">
        You have exited full-screen mode. Please return to full-screen immediately.
        You have <span id="warningsLeft" class="font-semibold"></span> warning(s) remaining before logout.
      </p>
      <button id="restoreFullScreen" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
        Restore Full-Screen
      </button>
    </div>
  </div>