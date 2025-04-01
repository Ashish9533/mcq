<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add these variables at the top of your script
        let currentQuestionIndex = 0;
        const questionItems = document.querySelectorAll('.question-item');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const currentQuestionSpan = document.getElementById('current-question');
        const currentCategorySpan = document.getElementById('current-category');
        const questionText = document.getElementById('question-text');
        const totalQuestionsSpan = document.getElementById('total-questions');
        const timerElement = document.getElementById('timer');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const markReviewBtn = document.getElementById('mark-review');
        const markAnsweredBtn = document.getElementById('mark-answered');
        const clearAnswerBtn = document.getElementById('clear-answer');
        const submitExamBtn = document.getElementById('submit-exam');
        const optionItems = document.querySelectorAll('.option-item');
        const reviewCount = document.getElementById('review-count');
        const filterToggle = document.getElementById('filter-toggle');
        const filterOptions = document.getElementById('filter-options');
        const showReviewed = document.getElementById('show-reviewed');
        const showAnswered = document.getElementById('show-answered');
        const answeredCount = document.getElementById('answered-count');
        let authUserEmail = "{{ auth()->user()->email }}";
        const submitModal = document.getElementById('submit-modal');
        const modalClose = document.getElementById('modal-close');
        const modalConfirm = document.getElementById('modal-confirm');
        const verificationEmail = document.getElementById('verification-email');
        const emailError = document.getElementById('email-error');
        const modalProgress = document.getElementById('modal-progress');
        const totalQuestionsModal = document.getElementById('total-questions-modal');

        // Add filter state variables
        let isFilterVisible = false;
        let showReviewedOnly = false;
        let showAnsweredOnly = false;

        let currentQuestion = null;
        const totalQuestions = questionItems.length;
        totalQuestionsSpan.textContent = totalQuestions;
        let answeredQuestions = new Set();
        let reviewedQuestions = new Set();
        // 2 hours in seconds



        // Enhanced security variables


        let lastWarningTime = Date.now();
        // const WARNING_COOLDOWN = 60000; // 1 minute cooldown between warnings
        let lastActivity = Date.now();
        let isTabVisible = true;
        let warningShown = false;
        let submissionAttempted = false;
        let devToolsOpen = false;
        // const INACTIVITY_TIMEOUT = 5 * 60 * 1000;
        // const WARNING_TIME = 5 * 60;
        // const MIN_ANSWERS_REQUIRED = 5;
        // const DEVTOOLS_THRESHOLD = 160;
        let lastSecurityCheck = 0;
        let remainingTime = @json($remainingTime);
        let examDuration = @json($examDuration);
        let timeLeft = examDuration;
        // const SECURITY_CHECK_INTERVAL = 5000; // Check every 5 seconds instead of every second

        // Add this at the top of your script with other variables
        let questionAnswers = new Map(); // Store answers for each question
        let warningCount = @json($warningCount);

        const maxWarnings = 3;
        let warningMessages = [];



        function isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }



        // Show warning message
        function showWarning(message) {
            if (warningShown) return;

            const warningDiv = document.createElement('div');
            warningDiv.className =
                'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
            warningDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
            document.body.appendChild(warningDiv);
            warningShown = true;

            setTimeout(() => {
                warningDiv.remove();
                warningShown = false;
            }, 5000);
        }

        // Validate answer selection
        function validateAnswer(questionNumber) {
            const questionItem = document.querySelector(`.question-item[data-question="${questionNumber}"]`);
            if (!questionItem) return false;

            const selectedOption = document.querySelector(`input[name="answer"]:checked`);
            return selectedOption !== null;
        }

        // Add event listeners for mark review and mark answered buttons
        markReviewBtn.addEventListener('click', () => {
            const currentQuestionItem = questionItems[currentQuestionIndex];
            if (currentQuestionItem) {
                const questionId = currentQuestionItem.dataset.question;
                toggleQuestionReview(questionId);
            }
        });

        markAnsweredBtn.addEventListener('click', () => {
            const currentQuestionItem = questionItems[currentQuestionIndex];
            if (currentQuestionItem) {
                const questionId = currentQuestionItem.dataset.question;
                toggleQuestionAnswered(questionId);
            }
        });

        // Function to toggle question review status
        function toggleQuestionReview(questionId) {
            if (reviewedQuestions.has(questionId)) {
                reviewedQuestions.delete(questionId);
                markReviewBtn.classList.remove('bg-yellow-200');
                markReviewBtn.classList.add('bg-yellow-100');
            } else {
                reviewedQuestions.add(questionId);
                markReviewBtn.classList.remove('bg-yellow-100');
                markReviewBtn.classList.add('bg-yellow-200');
            }

            // Update review count
            reviewCount.textContent = reviewedQuestions.size;

            // Update question status
            highlightQuestionStatus(questionId);

            // Apply filters if they are active
            if (isFilterVisible) {
                applyFilters();
            }
        }

        // Function to toggle question answered status
        function toggleQuestionAnswered(questionId) {
            if (answeredQuestions.has(questionId)) {
                answeredQuestions.delete(questionId);
                markAnsweredBtn.classList.remove('bg-green-200');
                markAnsweredBtn.classList.add('bg-green-100');
            } else {
                answeredQuestions.add(questionId);
                markAnsweredBtn.classList.remove('bg-green-100');
                markAnsweredBtn.classList.add('bg-green-200');
            }

            // Update answered count
            answeredCount.textContent = answeredQuestions.size;

            // Update progress bar
            updateProgress();

            // Update question status
            highlightQuestionStatus(questionId);

            // Apply filters if they are active
            if (isFilterVisible) {
                applyFilters();
            }
        }

        // Update the showQuestion function to handle selected options and button states
        function showQuestion(index) {
            // Hide all questions
            document.querySelectorAll('.question-content').forEach(q => q.classList.add('hidden'));

            // Show the selected question
            const questions = document.querySelectorAll('.question-content');
            if (questions[index]) {
                questions[index].classList.remove('hidden');

                // Update current question number
                currentQuestionSpan.textContent = index + 1;

                // Update category
                const questionItem = questionItems[index];
                if (questionItem) {
                    const category = questionItem.dataset.category;
                    currentCategorySpan.textContent = category;

                    // Remove highlight from all questions
                    questionItems.forEach(item => {
                        item.classList.remove('current', 'ring-2', 'ring-blue-500', 'shadow-lg',
                            'scale-105');
                    });

                    // Add highlight to current question
                    questionItem.classList.add('current', 'ring-2', 'ring-blue-500', 'shadow-lg', 'scale-105');

                    // Update button states based on current question status
                    const questionId = questionItem.dataset.question;
                    updateButtonStates(questionId);

                    // Update selected option styling
                    const selectedRadio = document.querySelector(`input[name="answer-${questionId}"]:checked`);
                    if (selectedRadio) {
                        const optionContainer = selectedRadio.closest('.option-item');
                        optionContainer.classList.add('selected');
                    }

                    // Scroll the current question into view
                    questionItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }

            // Update navigation buttons
            updateNavigationButtons();
        }

        // Function to update button states
        function updateButtonStates(questionId) {
            // Update mark review button
            if (reviewedQuestions.has(questionId)) {
                markReviewBtn.classList.remove('bg-yellow-100');
                markReviewBtn.classList.add('bg-yellow-200');
            } else {
                markReviewBtn.classList.remove('bg-yellow-200');
                markReviewBtn.classList.add('bg-yellow-100');
            }

            // Update mark answered button
            if (answeredQuestions.has(questionId)) {
                markAnsweredBtn.classList.remove('bg-green-100');
                markAnsweredBtn.classList.add('bg-green-200');
            } else {
                markAnsweredBtn.classList.remove('bg-green-200');
                markAnsweredBtn.classList.add('bg-green-100');
            }
        }

        // Update the highlightQuestionStatus function
        function highlightQuestionStatus(questionNumber) {
            const questionItem = document.querySelector(`.question-item[data-question="${questionNumber}"]`);
            if (questionItem) {
                const status = questionItem.querySelector('.question-status');

                // Reset all classes
                questionItem.classList.remove('answered', 'reviewed', 'answered-review');
                status.classList.remove('bg-gray-300', 'bg-green-500', 'bg-yellow-500', 'bg-red-500');

                const isAnswered = answeredQuestions.has(questionNumber);
                const isReviewed = reviewedQuestions.has(questionNumber);

                if (isAnswered && isReviewed) {
                    questionItem.classList.add('answered-review');
                    status.classList.add('bg-red-500');
                } else if (isAnswered) {
                    questionItem.classList.add('answered');
                    status.classList.add('bg-green-500');
                } else if (isReviewed) {
                    questionItem.classList.add('reviewed');
                    status.classList.add('bg-yellow-500');
                } else {
                    status.classList.add('bg-gray-300');
                }
            }
        }

        // Update navigation buttons state
        function updateNavigationButtons() {
            const totalQuestions = document.querySelectorAll('.question-content').length;
            prevBtn.disabled = currentQuestionIndex === 0;
            nextBtn.disabled = currentQuestionIndex === totalQuestions - 1;
        }

        // Event listeners for navigation
        prevBtn.addEventListener('click', () => {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                showQuestion(currentQuestionIndex);

            }
        });

        nextBtn.addEventListener('click', () => {
            const totalQuestions = document.querySelectorAll('.question-content').length;
            if (currentQuestionIndex < totalQuestions - 1) {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);

            }
        });

        // Question list click handler
        questionItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentQuestionIndex = index;
                showQuestion(currentQuestionIndex);

            });
        });

        // Initialize with first question
        showQuestion(0);

        // Update submitExam function
        async function submitExam() {
            if (submissionAttempted) return;
            submissionAttempted = true;

            // Validate all answered questions have a selection
            const unansweredQuestions = Array.from(answeredQuestions).filter(q => !validateAnswer(q));
            if (unansweredQuestions.length > 0) {
                if (!confirm(
                        'Some of your answered questions do not have a selection. Are you sure you want to submit?'
                    )) {
                    submissionAttempted = false;
                    return;
                }
            }

            // Show submission confirmation
            if (confirm('Are you sure you want to submit the exam? This action cannot be undone.')) {
                try {
                    const response = await fetch('/mcq/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            answers: Array.from(answeredQuestions).map(q => ({
                                question: q,
                                answer: document.querySelector(
                                    `input[name="answer"]:checked`)?.value
                            })),
                            timeSpent: examDuration - timeLeft,
                            reviewedQuestions: Array.from(reviewedQuestions)
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Disable all interactions
                        disableExamInterface();

                        // Show submission success message
                        showSuccessMessage('Exam submitted successfully!');
                    } else {
                        throw new Error('Submission failed');
                    }
                } catch (error) {
                    console.error('Error submitting exam:', error);
                    showWarning('Error submitting exam. Please try again.');
                    submissionAttempted = false;
                }
            } else {
                submissionAttempted = false;
            }
        }

        // Disable exam interface after submission
        function disableExamInterface() {
            questionItems.forEach(item => item.style.pointerEvents = 'none');
            optionItems.forEach(item => item.style.pointerEvents = 'none');
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            markReviewBtn.disabled = true;
            markAnsweredBtn.disabled = true;
            clearAnswerBtn.disabled = true;
            submitExamBtn.disabled = true;
        }

        // Show success message
        function showSuccessMessage(message) {
            const successDiv = document.createElement('div');
            successDiv.className =
                'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
            successDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
            document.body.appendChild(successDiv);
        }

        // Timer function
        function updateTimer() {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent =
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            } else {
                submitExam();
            }
        }

        // Update progress
        function updateProgress() {
            const progress = (answeredQuestions.size / totalQuestions) * 100;
            progressBar.style.width = `${progress}%`;
            progressText.textContent = `${answeredQuestions.size}/${totalQuestions}`;
        }

        // Function to get category name from code
        // function getCategoryName(code) {
        //     const categories = {
        //         'dsa': 'Data Structures & Algorithms',
        //         'system-design': 'System Design',
        //         'microservices': 'Microservices',
        //         'database': 'Database Design'
        //     };
        //     return categories[code] || code;
        // }

        // Handle logout functionality
        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logout-modal');
        const cancelLogoutBtn = document.getElementById('cancel-logout');
        const confirmLogoutBtn = document.getElementById('confirm-logout');

        logoutBtn.addEventListener('click', () => {
            logoutModal.classList.remove('hidden');
            logoutModal.classList.add('flex');
        });

        cancelLogoutBtn.addEventListener('click', () => {
            logoutModal.classList.add('hidden');
            logoutModal.classList.remove('flex');
        });

        confirmLogoutBtn.addEventListener('click', () => {
            submitExam().then(() => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            });
        });

        // Add event listeners for radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.name.split('-')[1];
                const questionItem = document.querySelector(
                    `.question-item[data-question="${questionId}"]`);

                if (questionItem) {
                    // Add to answered questions if not already there
                    if (!answeredQuestions.has(questionId)) {
                        answeredQuestions.add(questionId);
                        answeredCount.textContent = answeredQuestions.size;
                        updateProgress();
                    }

                    // Update question status with green highlight
                    questionItem.classList.add('answered');
                    const status = questionItem.querySelector('.question-status');
                    status.classList.remove('bg-gray-300', 'bg-yellow-500', 'bg-red-500');
                    status.classList.add('bg-green-500');

                    // Add selected class to the option container
                    const optionContainer = this.closest('.option-item');
                    optionContainer.classList.add('selected');

                    // Remove selected class from other options
                    const otherOptions = optionContainer.parentElement.querySelectorAll(
                        '.option-item');
                    otherOptions.forEach(opt => {
                        if (opt !== optionContainer) {
                            opt.classList.remove('selected');
                        }
                    });

                    // Update mark answered button state
                    const currentQuestionItem = questionItems[currentQuestionIndex];
                    if (currentQuestionItem && currentQuestionItem.dataset.question ===
                        questionId) {
                        markAnsweredBtn.classList.remove('bg-green-100');
                        markAnsweredBtn.classList.add('bg-green-200');
                    }

                    // Apply filters if they are active
                    if (isFilterVisible) {
                        applyFilters();
                    }
                }
            });
        });

        // Update the clearAnswerBtn click handler
        clearAnswerBtn.addEventListener('click', () => {
            const currentQuestionItem = questionItems[currentQuestionIndex];
            if (currentQuestionItem) {
                const questionId = currentQuestionItem.dataset.question;
                const questionContent = document.getElementById(`question-${questionId}`);

                // Clear radio selection
                const selectedRadio = questionContent.querySelector('input[type="radio"]:checked');
                if (selectedRadio) {
                    selectedRadio.checked = false;

                    // Remove selected class from all options
                    questionContent.querySelectorAll('.option-item').forEach(opt => {
                        opt.classList.remove('selected');
                    });

                    // Remove from answered questions
                    if (answeredQuestions.has(questionId)) {
                        answeredQuestions.delete(questionId);
                        answeredCount.textContent = answeredQuestions.size;
                        updateProgress();
                    }

                    // Remove highlight from question
                    const questionItem = document.querySelector(
                        `.question-item[data-question="${questionId}"]`);
                    if (questionItem) {
                        questionItem.classList.remove('answered');
                        const status = questionItem.querySelector('.question-status');
                        status.classList.remove('bg-green-500', 'bg-yellow-500', 'bg-red-500');
                        status.classList.add('bg-gray-300');
                    }

                    // Update mark answered button state
                    markAnsweredBtn.classList.remove('bg-green-200');
                    markAnsweredBtn.classList.add('bg-green-100');

                    // Apply filters if they are active
                    if (isFilterVisible) {
                        applyFilters();
                    }
                }
            }
        });

        // Add filter toggle functionality
        filterToggle.addEventListener('click', () => {
            isFilterVisible = !isFilterVisible;
            filterOptions.classList.toggle('hidden', !isFilterVisible);

            // Update filter toggle icon
            const filterIcon = filterToggle.querySelector('svg');
            if (isFilterVisible) {
                filterIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            } else {
                filterIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5"></path>';
            }
        });

        // Add filter checkbox event listeners
        showReviewed.addEventListener('change', () => {
            showReviewedOnly = showReviewed.checked;
            showAnsweredOnly = false; // Reset the other filter
            showAnswered.checked = false; // Uncheck the other checkbox
            applyFilters();
        });

        showAnswered.addEventListener('change', () => {
            showAnsweredOnly = showAnswered.checked;
            showReviewedOnly = false; // Reset the other filter
            showReviewed.checked = false; // Uncheck the other checkbox
            applyFilters();
        });

        // Function to apply filters
        function applyFilters() {
            // Process each question and category
            document.querySelectorAll('.category-section').forEach(section => {
                if (showReviewedOnly || showAnsweredOnly) {
                    // When either filter is checked
                    const questions = section.querySelectorAll('.question-item');
                    let hasVisibleQuestions = false;

                    questions.forEach(item => {
                        const questionId = item.dataset.question;
                        const isReviewed = reviewedQuestions.has(questionId);
                        const isAnswered = answeredQuestions.has(questionId);

                        // Show question only if it matches the active filter
                        if ((showReviewedOnly && isReviewed) || (showAnsweredOnly &&
                                isAnswered)) {
                            item.style.display = 'block';
                            hasVisibleQuestions = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Hide the category header if no matching questions
                    section.querySelector('h3').style.display = hasVisibleQuestions ? 'block' : 'none';
                } else {
                    // When no filters are active, show everything
                    section.style.display = 'block';
                    section.querySelector('h3').style.display = 'block';
                    section.querySelectorAll('.question-item').forEach(item => {
                        item.style.display = 'block';
                        highlightQuestionStatus(item.dataset.question);
                    });
                }
            });
        }








        async function submitFormBeforeTimeout() {
            // Reset error display
            emailError.classList.add('hidden');
            verificationEmail.classList.remove('border-red-500');

            const emailValue = verificationEmail.value.trim();

            // Validate email format
            if (!emailValue || !isValidEmail(emailValue)) {
                emailError.textContent = "Please enter a valid email address.";
                emailError.classList.remove('hidden');
                verificationEmail.classList.add('border-red-500');
                return;
            }

            // Ensure email matches the authenticated user's email
            if (emailValue !== authUserEmail) {
                emailError.textContent = "The email does not match your account email.";
                emailError.classList.remove('hidden');
                verificationEmail.classList.add('border-red-500');
                return;
            }

            // Prepare submission payload
            const submissionPayload = {
                email: emailValue,
                answers: Array.from(answeredQuestions).map(q => ({
                    question: q,
                    answer: document.querySelector(
                        `input[name="answer-${q}"][data-question-id="${q}"]:checked`
                    )?.value || null
                })),
                timeSpent: examDuration - remainingTime,
                reviewed_questions: Array.from(reviewedQuestions)
            };

            try {
                const response = await fetch('mcq/before-time-submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(submissionPayload)
                });
                const data = await response.json();
                if (data.success) {
                    disableExamInterface();
                    window.location.href = '/thanks';
                    // showSuccessMessage('Exam submitted successfully!');
                } else {
                    throw new Error(data.error || 'Exam submission failed. Please try again.');
                }
            } catch (error) {
                console.error('Error submitting exam:', error);
                emailError.textContent = error.message;
                emailError.classList.remove('hidden');
                verificationEmail.classList.add('border-red-500');
            }
        }

        // Modal: show on submit button click
        submitExamBtn.addEventListener('click', () => {
            totalQuestionsModal.textContent = totalQuestions;
            modalProgress.textContent = answeredQuestions.size;
            submitModal.classList.remove('hidden');
        });

        // Modal: close on cross icon click
        modalClose.addEventListener('click', () => {
            submitModal.classList.add('hidden');
        });

        // Modal: confirm submission
        modalConfirm.addEventListener('click', async () => {
            await submitFormBeforeTimeout();
            // submitModal.classList.add('hidden');
        });










        // Use localStorage to track the number of exam tabs.Client side sequirity //////////////////



        function logActivity(message) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
            fetch("{{ route('log.activity') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    message: message
                })
            }).catch(err => console.error("Error logging activity:", err));
        }




        function updateWarningModal() {
            document.getElementById("warningsLeft").textContent = maxWarnings - warningCount;
            document.getElementById("warningMessage").textContent = warningMessages.join(" ");
            document.getElementById("warningModal").classList.remove("hidden");
        }


        function clearWarnings() {
            warningMessages = [];
            hideWarningModal();
        }



        function updateTabCount(delta) {
            let count = parseInt(localStorage.getItem("examTabsCount") || "0", 10);
            count += delta;
            localStorage.setItem("examTabsCount", count);
            return count;
        }
        // On load, increment tab count.
        updateTabCount(1);
        // On unload, decrement tab count.
        window.addEventListener("beforeunload", function() {
            updateTabCount(-1);
        });

        // --- Full-Screen and Warning Variables ---


        // --- Full-Screen Request Function ---
        function requestFullScreen() {
            const elem = document.documentElement;
            let fullScreenPromise;
            if (elem.requestFullscreen) {
                fullScreenPromise = elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { // Safari
                fullScreenPromise = elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { // IE11
                fullScreenPromise = elem.msRequestFullscreen();
            }
            return fullScreenPromise.catch(err => {
                console.error("Request Fullscreen Error:", err);
            });
        }

        // --- Warning Modal Functions ---
        function showWarningModal(message) {
            console.log(message);
            document.getElementById("warningsLeft").textContent = maxWarnings - warningCount;
            // Update the warning message text.
            document.getElementById("warningMessage").textContent = message;
            document.getElementById("warningModal").classList.remove("hidden");
        }

        function hideWarningModal() {
            document.getElementById("warningModal").classList.add("hidden");
        }

        // --- AJAX Logout Function ---
        function ajaxLogout() {
            localStorage.removeItem("examTabsCount"); // Clear the tab count.
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
            fetch("{{ route('logout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            }).then(response => {
                if (response.ok) {
                    window.location.href = "/login";
                } else {
                    console.error("Logout failed:", response.statusText);
                }
            }).catch(error => {
                console.error("Error during logout:", error);
            });
        }

        // --- Event Listeners for Full-Screen and Tab Switching ---
        // Full-screen exit detection.
        document.addEventListener("fullscreenchange", () => {
            if (!document.fullscreenElement) {
                // If the document is not hidden, then it's likely an intentional exit.
                if (!document.hidden) {
                    warningCount++;
                    logActivity("User exited full-screen mode.");
                    warningMessages.push(
                        "You have exited full-screen mode. Please return to full-screen.");
                    if (warningCount < maxWarnings) {
                        updateWarningModal();
                    } else {
                        ajaxLogout();
                    }
                }
            }
        });


        // Tab switching detection.
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) {
                warningCount++;
                logActivity("User switched tabs during the exam.");
                warningMessages.push("Tab switching detected. Please remain on the exam tab.");
                if (warningCount < maxWarnings) {
                    updateWarningModal();
                } else {
                    ajaxLogout();
                }
            }
        });

        // Multiple tabs detection using storage events.
        window.addEventListener("storage", (event) => {
            if (event.key === "examTabsCount") {
                let count = parseInt(event.newValue || "0", 10);
                if (count > 1) {
                    warningCount++;
                    logActivity("Multiple exam tabs detected.");
                    if (warningCount < maxWarnings) {
                        showWarningModal("Multiple exam tabs detected. Please close all extra tabs.");
                    } else {
                        ajaxLogout();
                    }
                } else {
                    // If only one tab remains, hide the warning (if it was shown because of extra tabs).
                    hideWarningModal();
                }
            }
        });

        // Restore full-screen button in warning modal.
        document.getElementById("restoreFullScreen").addEventListener("click", () => {
            hideWarningModal();
            clearWarnings();
            requestFullScreen();
        });

        // --- Start Exam Button ---
        document.getElementById("startExam").addEventListener("click", function() {
            requestFullScreen().then(() => {
                // Hide the full-screen prompt modal on success.
                document.getElementById("fullscreenPrompt").style.display = "none";
            }).catch(err => {
                console.error("Error entering full-screen mode:", err);
            });
        });








        // document.addEventListener("contextmenu", function(event) {
        //     event.preventDefault();
        // });

        /////////////////////////////////////Time manage//////////////////%
        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            return `${hours}:${minutes}:${secs}`;
        }



        // Format exam duration as hh:mm:ss
        const formattedExamDuration = formatTime(examDuration);
        const timerDiv = document.getElementById('timer');

        // Function to update the timer display
        async function updateTimer() {
            if (remainingTime < 0) {
                timerDiv.textContent = "00:00:00";

                try {
                    const response = await fetch('/mcq/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            answers: Array.from(answeredQuestions).map(q => ({
                                question: q,
                                answer: document.querySelector(
                                    `input[name="answer-${q}"][data-question-id="${q}"]:checked`
                                )?.value || null
                            })),
                            timeSpent: examDuration - remainingTime,
                            reviewedQuestions: Array.from(reviewedQuestions)
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        disableExamInterface();
                        // Explicit client-side redirection after a successful submission
                        window.location.href = '/thanks';
                    } else {
                        throw new Error('Submission failed');
                    }
                } catch (error) {
                    console.error('Error submitting exam:', error);
                    showWarning('Error submitting exam. Please try again.');
                    submissionAttempted = false;
                }
                return;
            }

            // Update timer display and schedule the next update
            if (remainingTime >= 0) {
                timerDiv.textContent = `${formatTime(remainingTime)} / ${formattedExamDuration}`;
                remainingTime--;
                setTimeout(updateTimer, 1000);
            }
        }


        // Start the timer
        updateTimer();

    });
</script>
