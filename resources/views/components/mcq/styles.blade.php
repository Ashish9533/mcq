<style>
.scrollbar-hide::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-hide::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.scrollbar-hide::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.scrollbar-hide::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.question-item {
    @apply transition-all duration-300 ease-in-out border-2;
}

.question-item.answered {
    @apply bg-green-50 border-green-500 shadow-md;
}

.question-item.reviewed {
    @apply bg-yellow-50 border-yellow-500 shadow-md;
}

.question-item.answered-review {
    @apply bg-red-50 border-red-500 shadow-md;
}

.question-status {
    @apply w-4 h-4 rounded-full transition-all duration-300;
}

.question-status.answered {
    @apply bg-green-500;
}

.question-status.review {
    @apply bg-yellow-500;
}

.question-status.answered.review {
    @apply bg-red-500;
}

.option-item {
    @apply p-4 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200;
}

.option-item:hover {
    @apply border-blue-300 bg-blue-50;
}

.option-item.selected {
    @apply border-blue-500 bg-blue-50;
    animation: selectPulse 0.3s ease-in-out;
}

@keyframes statusPulse {
    0% { transform: scale(0.8); opacity: 0.5; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes statusBlink {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

@keyframes selectPulse {
    0% { transform: scale(0.98); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile Optimizations */
@media (max-width: 640px) {
    .container {
        @apply px-3;
    }
    
    .question-item {
        @apply p-2;
    }
    
    .option-item {
        @apply p-3;
    }
}

* {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-touch-callout: none;
}

input, textarea {
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
    user-select: text;
}

.question-item.current {
    @apply ring-2 ring-blue-500;
}

/* Update existing hover state for better visibility */
.option-item:hover:not(.selected) {
    @apply border-blue-300 bg-blue-50/50;
}

.question-item {
    @apply transition-all duration-300 ease-in-out;
}

.question-item.ring-2 {
    @apply transform transition-transform duration-300;
}

.question-item:hover {
    @apply shadow-md;
}

/* Update the current question highlight */
.question-item.current {
    @apply ring-2 ring-blue-500 shadow-lg scale-105;
}
</style> 