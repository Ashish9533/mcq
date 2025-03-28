@extends('layouts.app')

@section('title', 'Software Development MCQ')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Top Bar with Timer and Progress -->
    <x-mcq.top-bar :totalQuestions="$totalQuestions" />

    <!-- Modals -->
    <x-mcq.modals />
    <x-mcq.warning />

    <!-- Full-Screen Prompt Modal -->
    <div id="fullscreenPrompt" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg">
            <h2 class="text-2xl mb-4">Enter Full-Screen Mode</h2>
            <p class="mb-4">
                For security, the exam must be taken in full-screen mode.
                Please click the button below to enter full-screen mode.
            </p>
            <button id="startExam" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Start Exam
            </button>
        </div>
    </div>

    <!-- Warning Modal (used for both full-screen exit, tab switching, or multiple tabs) -->
    <div id="warningModal" class="fixed inset-0 flex items-center justify-center hidden z-50">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black bg-opacity-50"></div>
      <!-- Modal Content -->
      <div class="bg-white rounded-lg shadow-lg p-6 z-50 max-w-sm mx-auto">
        <h2 class="text-xl font-bold mb-4 text-red-600">Warning!</h2>
        <p class="mb-4" id="warningMessage">
          <!-- This message will be updated dynamically -->
        </p>
        <p class="mb-4">
          You have <span id="warningsLeft" class="font-semibold"></span> warning(s) remaining before logout.
        </p>
        <button id="restoreFullScreen" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
          Restore Full-Screen
        </button>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-12rem)]">
        <!-- Left Section - Questions List -->
        <x-mcq.questions-list :categories="$categories" />

        <!-- Right Section - Current Question -->
        <x-mcq.current-question :categories="$categories" :totalQuestions="$totalQuestions" />
    </div>
</div>

<!-- Styles -->
<x-mcq.styles />

<!-- Scripts -->
<x-mcq.scripts />
@endsection
