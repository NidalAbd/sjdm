<div class="col-lg-3 col-6">
    <div class="small-box bg-{{ $color }}">
        <div class="inner">
            <h3>{{ $count }}</h3>
            <p>{{ $title }}</p>
        </div>
        <div class="icon">
            <i class="{{ $icon }}"></i>
        </div>
        @if(!empty($link))
            <a href="{{ $link }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
        @endif
    </div>
</div>
