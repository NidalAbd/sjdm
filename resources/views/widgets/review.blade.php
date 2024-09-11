<div class="row mb-4 mt-5">
    <!-- Review Section Title -->
    <h4 class="text-center mb-4 platform-title">{{ __('adminlte.title_Reviews') }}</h4>

    <div class="row col-md-12">
        <!-- Review 1 (Animate from left) -->
        <div class="col-md-4">
            <div class="stats-box aos-init aos-animate" data-aos="fade-right" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="mb-3">
                    <img src="{{ asset('images/avatar1.png') }}" alt="{{ __('adminlte.reviewer_name1') }}" class="rounded-circle" style="width: 250px; height: 250px;">
                </div>
                <h4 class="mb-2">{{ __('adminlte.reviewer_name1') }}</h4>
                <div class="review-rating text-warning mb-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="review-text">{{ __('adminlte.review_text1') }}</p>
            </div>
        </div>

        <!-- Review 2 (Animate from bottom) -->
        <div class="col-md-4">
            <div class="stats-box aos-init aos-animate" data-aos="fade-up" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="mb-3">
                    <img src="{{ asset('images/avatar2.png') }}" alt="{{ __('adminlte.reviewer_name2') }}" class="rounded-circle" style="width: 250px; height: 250px;">
                </div>
                <h4 class="mb-2">{{ __('adminlte.reviewer_name2') }}</h4>
                <div class="review-rating text-warning mb-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="review-text">{{ __('adminlte.review_text2') }}</p>
            </div>
        </div>

        <!-- Review 3 (Animate from right) -->
        <div class="col-md-4">
            <div class="stats-box aos-init aos-animate" data-aos="fade-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="mb-3">
                    <img src="{{ asset('images/avatar3.png') }}" alt="{{ __('adminlte.reviewer_name3') }}" class="rounded-circle" style="width: 250px; height: 250px;">
                </div>
                <h4 class="mb-2">{{ __('adminlte.reviewer_name3') }}</h4>
                <div class="review-rating text-warning mb-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <i class="far fa-star"></i>
                </div>
                <p class="review-text">{{ __('adminlte.review_text3') }}</p>
            </div>
        </div>
    </div>

    <!-- Optional: Call to Action -->
    <div class="text-center mt-4">
        <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
    </div>
</div>
