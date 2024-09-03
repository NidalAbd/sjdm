<div class="row mb-4 mt-5">
    <!-- Affiliate Program Section Title -->
    <h4 class="text-center mb-4 platform-title">{{ __('adminlte.title_Affiliate') }}</h4>

    <div class="row col-md-12">
        <!-- Step 1 (Animate from left) -->
        <div class="col-md-4">
            <div class="stats-box aos-init aos-animate" data-aos="fade-right" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="mb-3">
                    <i class="fas fa-share-alt fa-3x text-primary"></i> <!-- Share Icon -->
                </div>
                <h4 class="mb-2">{{ __('adminlte.step1_title') }}</h4>
                <p class="review-text">{{ __('adminlte.step1_description') }}</p>
            </div>
        </div>

        <!-- Step 2 (Animate from bottom) -->
        <div class="col-md-4">
            <div class="stats-box aos-init aos-animate" data-aos="fade-up" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="mb-3">
                    <i class="fas fa-user-check fa-3x text-success"></i> <!-- Verify Icon -->
                </div>
                <h4 class="mb-2">{{ __('adminlte.step2_title') }}</h4>
                <p class="review-text">{{ __('adminlte.step2_description') }}</p>
            </div>
        </div>

        <!-- Step 3 (Animate from right) -->
        <div class="col-md-4">
            <div class="stats-box aos-init aos-animate" data-aos="fade-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="mb-3">
                    <i class="fas fa-gift fa-3x text-warning"></i> <!-- Reward Icon -->
                </div>
                <h4 class="mb-2">{{ __('adminlte.step3_title') }}</h4>
                <p class="review-text">{{ __('adminlte.step3_description') }}</p>
            </div>
        </div>
    </div>

    <!-- Optional: Call to Action -->
    <div class="text-center mt-4">
        <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
    </div>
</div>
