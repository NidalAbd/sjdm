<!-- resources/views/widgets/faq.blade.php -->
@extends('layouts.welcome')
@section('title', __('adminlte.faq'))
@section('content')
    <div class="content-section active" id="faq-section">
        <div class="container my-5">
            <h2 class="text-center mb-4 platform-title">{{ __('adminlte.faq_frequently') }}</h2>

            <div class="card shadow-lg rounded-lg border-1 mt-4">
                <div class="card-body p-5">
                    <!-- FAQ Questions -->
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group" id="faq-list">
                                <li class="list-group-item list-group-item-action" data-answer="answer1">{{ __('adminlte.faq_question1') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer2">{{ __('adminlte.faq_question2') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer3">{{ __('adminlte.faq_question3') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer4">{{ __('adminlte.faq_question4') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer5">{{ __('adminlte.faq_question5') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer6">{{ __('adminlte.faq_question6') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer7">{{ __('adminlte.faq_question7') }}</li>
                                <li class="list-group-item list-group-item-action" data-answer="answer8">{{ __('adminlte.faq_question8') }}</li>
                            </ul>
                        </div>
                        <!-- FAQ Answers -->
                        <div class="col-md-8">
                            <div id="answer1" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question1') }}</h4>
                                <p>{{ __('adminlte.faq_answer1') }}</p>
                            </div>
                            <div id="answer2" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question2') }}</h4>
                                <p>{{ __('adminlte.faq_answer2') }}</p>
                            </div>
                            <div id="answer3" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question3') }}</h4>
                                <p>{{ __('adminlte.faq_answer3') }}</p>
                            </div>
                            <div id="answer4" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question4') }}</h4>
                                <p>{{ __('adminlte.faq_answer4') }}</p>
                            </div>
                            <div id="answer5" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question5') }}</h4>
                                <p>{{ __('adminlte.faq_answer5') }}</p>
                            </div>
                            <div id="answer6" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question6') }}</h4>
                                <p>{{ __('adminlte.faq_answer6') }}</p>
                            </div>
                            <div id="answer7" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question7') }}</h4>
                                <p>{{ __('adminlte.faq_answer7') }}</p>
                            </div>
                            <div id="answer8" class="faq-answer" style="display: none;">
                                <h4>{{ __('adminlte.faq_question8') }}</h4>
                                <p>{{ __('adminlte.faq_answer8') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- AOS Animation Library -->
    <style>
        .faq-answer {
            display: none; /* Hide all answers by default */
        }
        /* Light mode styles */
        body.light-mode .list-group-item {
            background-color: #f8f9fa;
            color: #000;
        }
        body.light-mode .list-group-item-action:hover,
        body.light-mode .list-group-item-action:focus {
            background-color: #121111;
            color: #000;
        }
        /* Dark mode styles */
        body.dark-mode .list-group-item {
            background-color: rgba(51, 51, 51, 0);
            color: #fff;
        }
        body.dark-mode .list-group-item-action:hover,
        body.dark-mode .list-group-item-action:focus {
            color: #fff;
        }
        .dark-mode .card-body {
            background-color: #333;
            color: #fff;
        }
        .dark-mode .platform-title,
        .dark-mode .fw-bold {
            color: #fff;
        }
    </style>
@endpush

@push('scripts')
    <!-- AOS Animation Library -->
    <script>
        AOS.init(); // Initialize AOS for animations

        // FAQ toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqList = document.getElementById('faq-list');
            const answers = document.querySelectorAll('.faq-answer');

            faqList.addEventListener('click', function(e) {
                if (e.target && e.target.matches('li.list-group-item')) {
                    const answerId = e.target.getAttribute('data-answer');

                    // Hide all answers
                    answers.forEach(answer => answer.style.display = 'none');

                    // Show the selected answer
                    document.getElementById(answerId).style.display = 'block';
                }
            });
        });
    </script>
@endpush
