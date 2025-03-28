<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exam Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure various settings for the MCQ exam system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'encrypt_questions' => env('EXAM_ENCRYPT_QUESTIONS', true),
    'require_authentication' => env('EXAM_REQUIRE_AUTH', true),
    'track_ip' => env('EXAM_TRACK_IP', true),
    'track_user_agent' => env('EXAM_TRACK_USER_AGENT', true),
    'track_device_fingerprint' => env('EXAM_TRACK_DEVICE_FINGERPRINT', true),
    'max_tab_switches' =>(int) env('EXAM_MAX_TAB_SWITCHES', 3),
    'max_inactive_time' => (int)env('EXAM_MAX_INACTIVE_TIME', 300), // 5 minutes in seconds
    'auto_submit_on_suspicious' => env('EXAM_AUTO_SUBMIT_SUSPICIOUS', true),
    'notify_admin_on_suspicious' => env('EXAM_NOTIFY_ADMIN_SUSPICIOUS', true),

    /*
    |--------------------------------------------------------------------------
    | Exam Settings
    |--------------------------------------------------------------------------
    */
    'max_duration' => (int)env('EXAM_MAX_DURATION', 60), // 2 hours in seconds
    'randomize_questions' => env('EXAM_RANDOMIZE_QUESTIONS', true),
    'randomize_options' => env('EXAM_RANDOMIZE_OPTIONS', true),
    'show_correct_answers' => env('EXAM_SHOW_CORRECT_ANSWERS', false),
    'show_explanation' => env('EXAM_SHOW_EXPLANATION', false),
    'min_questions_to_pass' => env('EXAM_MIN_QUESTIONS_TO_PASS', 5),
    'passing_percentage' => env('EXAM_PASSING_PERCENTAGE', 60),

    /*
    |--------------------------------------------------------------------------
    | UI Settings
    |--------------------------------------------------------------------------
    */
    'show_progress_bar' => env('EXAM_SHOW_PROGRESS_BAR', true),
    'show_timer' => env('EXAM_SHOW_TIMER', true),
    'show_question_numbers' => env('EXAM_SHOW_QUESTION_NUMBERS', true),
    'show_category_names' => env('EXAM_SHOW_CATEGORY_NAMES', true),
    'allow_review' => env('EXAM_ALLOW_REVIEW', true),
    'allow_mark_for_review' => env('EXAM_ALLOW_MARK_FOR_REVIEW', true),

    /*
    |--------------------------------------------------------------------------
    | Additional Settings
    |--------------------------------------------------------------------------
    */
    'allow_copy_paste' => false,
    'allow_right_click' => false,
    'allow_keyboard_shortcuts' => false,
    'allow_question_navigation' => true,
    'allow_question_review' => true,
    'allow_answer_changes' => true,
    'show_difficulty_levels' => false,
    'allow_retake' => false,
    'max_retakes' => 0,
    'retake_interval' => 24,
    'use_secure_session' => true,
    'force_https' => true,
    'use_rate_limiting' => true,
    'rate_limit' => 60,
    'use_csrf_protection' => true,
    'use_xss_protection' => true,
    'use_sql_injection_protection' => true,
    'use_input_validation' => true,
    'use_output_sanitization' => true,
    'use_prepared_statements' => true,
    'use_parameterized_queries' => true,
    'use_stored_procedures' => false,
    'use_database_encryption' => true,
    'use_file_encryption' => true,
    'use_api_authentication' => true,
    'use_api_rate_limiting' => true,
    'api_rate_limit' => 30,
    'use_api_token_rotation' => true,
    'api_token_rotation_interval' => 24,
    'use_api_request_signing' => true,
    'use_api_response_signing' => true,
    'use_api_request_validation' => true,
    'use_api_response_validation' => true,
    'use_api_request_logging' => true,
    'use_api_response_logging' => true,
    'use_api_error_handling' => true,
    'use_api_versioning' => true,
    'use_api_documentation' => true,
    'use_api_testing' => true,
    'use_api_monitoring' => true,
    'use_api_analytics' => true,
    'use_api_caching' => true,
    'api_cache_duration' => 60,
    'use_api_compression' => true,
    'use_api_load_balancing' => true,
    'use_api_failover' => true,
    'use_api_backup' => true,
    'use_api_recovery' => true,
    'use_api_scaling' => true,
    'use_api_security_headers' => true,
    'use_api_cors' => true,
    'use_api_ip_rate_limiting' => true,
    'use_api_user_rate_limiting' => true,
    'use_api_endpoint_rate_limiting' => true,
    'use_api_method_rate_limiting' => true,
    'use_api_status_rate_limiting' => true,
    'use_api_response_time_rate_limiting' => true,
    'use_api_error_rate_limiting' => true,
    'use_api_bandwidth_rate_limiting' => true,
    'use_api_concurrent_rate_limiting' => true,
    'use_api_session_rate_limiting' => true,
    'use_api_token_rate_limiting' => true,
    'use_api_role_rate_limiting' => true,
    'use_api_permission_rate_limiting' => true,
    'use_api_custom_rate_limiting' => true,
]; 