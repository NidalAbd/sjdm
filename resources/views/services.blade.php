@extends('layouts.welcome')
@section('content')
    <div class="services-page">
        <!-- Hero Section -->
        <div class="hero-section bg-gradient-primary text-white py-5 mb-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">{{ $seoTitle ?? __('All SMM Services') }}</h1>
                        <p class="lead mb-4">{{ $seoDescription ?? __('Discover our complete range of social media marketing services') }}</p>

                        @if(isset($filterStats) && $filterStats['has_filters'])
                            <div class="filter-summary">
                            <span class="badge bg-white text-primary px-3 py-2">
                                <i class="fas fa-filter me-2"></i>
                                {{ $filterStats['total_services'] }} {{ __('services found') }}
                            </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">{{ $filterStats['total_services'] ?? 0 }}</div>
                                <div class="stat-label">{{ __('Total Services') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Filters Section -->
            <div class="filters-section mb-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="filtersForm" method="GET" action="{{ route('services.all') }}">
                            <div class="row g-3">
                                <!-- Search -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="search-box">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-search text-primary me-2"></i>{{ __('Search Services') }}
                                        </label>
                                        <input type="text" name="search" class="form-control form-control-lg"
                                               placeholder="{{ __('Enter service name or ID...') }}"
                                               value="{{ request('search') }}">
                                    </div>
                                </div>

                                <!-- Platform Filter -->
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-globe text-primary me-2"></i>{{ __('Platform') }}
                                    </label>
                                    <select name="platform" class="form-select form-select-lg">
                                        <option value="">{{ __('All Platforms') }}</option>
                                        @if(isset($platformStats))
                                            @foreach($platformStats as $key => $stat)
                                                <option value="{{ $key }}" {{ request('platform') == $key ? 'selected' : '' }}>
                                                    {{ ucfirst($stat['name']) }} ({{ $stat['count'] }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Category Filter -->
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-tags text-primary me-2"></i>{{ __('Category') }}
                                    </label>
                                    <select name="category" class="form-select form-select-lg">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @if(isset($categories))
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                    {{ ucfirst($category) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Sort Options -->
                                <div class="col-lg-6 col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-sort text-primary me-2"></i>{{ __('Sort By') }}
                                    </label>
                                    <select name="sort_by" class="form-select form-select-lg">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>{{ __('Date Added') }}</option>
                                        <option value="rate" {{ request('sort_by') == 'rate' ? 'selected' : '' }}>{{ __('Price') }}</option>
                                        <option value="service_id" {{ request('sort_by') == 'service_id' ? 'selected' : '' }}>{{ __('Service ID') }}</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div class="col-lg-6 col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-arrow-up-down text-primary me-2"></i>{{ __('Order') }}
                                    </label>
                                    <select name="sort_order" class="form-select form-select-lg">
                                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-3 flex-wrap">
                                        <button type="submit" class="btn btn-primary btn-lg px-4">
                                            <i class="fas fa-filter me-2"></i>{{ __('Apply Filters') }}
                                        </button>
                                        <a href="{{ route('services.all') }}" class="btn btn-outline-secondary btn-lg px-4">
                                            <i class="fas fa-times me-2"></i>{{ __('Clear Filters') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Featured Services (if available) -->
            @if(isset($featuredServices) && $featuredServices->count() > 0)
                <div class="featured-section mb-5">
                    <div class="section-header mb-4">
                        <h2 class="h3 fw-bold text-dark">
                            <i class="fas fa-star text-warning me-2"></i>{{ __('Featured Services') }}
                        </h2>
                        <p class="text-muted">{{ __('Most popular and affordable services') }}</p>
                    </div>

                    <div class="row g-4">
                        @foreach($featuredServices as $featured)
                            <div class="col-lg-4 col-md-6">
                                <div class="featured-card">
                                    <div class="featured-badge">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">{{ app()->getLocale() === 'ar' ? $featured->name_ar : $featured->name_en }}</h6>
                                    <div class="price-tag">${{ number_format($featured->rate, 2) }}</div>
                                    <div class="limits">{{ number_format($featured->min) }} - {{ number_format($featured->max) }}</div>
                                    <a href="{{ route('service.show', $featured->service_id) }}" class="btn btn-sm btn-primary mt-2">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Services Grid/List -->
            <div class="services-section">
                <div class="section-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h3 fw-bold text-dark">
                                @if(request('search'))
                                    {{ __('Search Results') }}
                                @elseif(request('platform'))
                                    {{ ucfirst(request('platform')) }} {{ __('Services') }}
                                @elseif(request('category'))
                                    {{ ucfirst(request('category')) }} {{ __('Services') }}
                                @else
                                    {{ __('All Services') }}
                                @endif
                            </h2>
                            @if(isset($filterStats))
                                <p class="text-muted">{{ $filterStats['total_services'] }} {{ __('services available') }}</p>
                            @endif
                        </div>

                        <div class="view-toggles">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="gridView">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="listView">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services Content -->
                @if($services->count() > 0)
                    <!-- Grid View (Default) -->
                    <div id="servicesGrid" class="services-grid">
                        <div class="row g-4">
                            @foreach($services as $service)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-id">#{{ $service->service_id }}</div>
                                            <div class="service-type">{{ $service->type }}</div>
                                        </div>

                                        <div class="service-body">
                                            <h5 class="service-title">
                                                {{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}
                                            </h5>

                                            <div class="service-category mb-3">
                                                <i class="fas fa-tag text-primary me-2"></i>
                                                {{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}
                                            </div>

                                            <div class="service-stats row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="stat-box">
                                                        <div class="stat-label">{{ __('Min') }}</div>
                                                        <div class="stat-value">{{ number_format($service->min) }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="stat-box">
                                                        <div class="stat-label">{{ __('Max') }}</div>
                                                        <div class="stat-value">{{ number_format($service->max) }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="service-features mb-3">
                                                @if($service->refill)
                                                    <span class="feature-badge bg-success">
                                                    <i class="fas fa-redo me-1"></i>{{ __('Refill') }}
                                                </span>
                                                @endif
                                                @if($service->cancel)
                                                    <span class="feature-badge bg-warning">
                                                    <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="service-footer">
                                            <div class="price-section">
                                                <span class="price">${{ number_format($service->rate, 4) }}</span>
                                                <span class="price-unit">/ 1K</span>
                                            </div>
                                            <a href="{{ route('service.show', $service->service_id) }}" class="btn btn-primary">
                                                {{ __('Order Now') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- List View (Hidden by default) -->
                    <div id="servicesList" class="services-list d-none">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                <tr>
                                    <th>{{ __('Service') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Min/Max') }}</th>
                                    <th>{{ __('Features') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td>
                                            <div class="service-info">
                                                <div class="service-name fw-bold">
                                                    {{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}
                                                </div>
                                                <small class="text-muted">#{{ $service->service_id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="price fw-bold text-primary">${{ number_format($service->rate, 4) }}</span>
                                            <small class="text-muted">/ 1K</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ number_format($service->min) }} - {{ number_format($service->max) }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($service->refill)
                                                <span class="badge bg-success me-1">{{ __('Refill') }}</span>
                                            @endif
                                            @if($service->cancel)
                                                <span class="badge bg-warning">{{ __('Cancel') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('service.show', $service->service_id) }}" class="btn btn-sm btn-primary">
                                                {{ __('Order') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <!-- No Results -->
                    <div class="no-results text-center py-5">
                        <div class="no-results-icon mb-4">
                            <i class="fas fa-search fa-4x text-muted"></i>
                        </div>
                        <h3 class="text-muted mb-3">{{ __('No services found') }}</h3>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'platform', 'category']))
                                {{ __('Try adjusting your filters or search terms') }}
                            @else
                                {{ __('No services are currently available') }}
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'platform', 'category']))
                            <a href="{{ route('services.all') }}" class="btn btn-primary">
                                {{ __('View All Services') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($services->count() > 0)
                <div class="pagination-section mt-5">
                    <div class="d-flex justify-content-center">
                        {{ $services->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .hero-stats .stat-item {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }

        .hero-stats .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: white;
        }

        .hero-stats .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .service-header {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 1.5rem 1.5rem 0;
        }

        .service-id {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .service-type {
            background: #f8f9fa;
            color: #6c757d;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .service-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .service-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .service-category {
            color: #667eea;
            font-weight: 500;
        }

        .stat-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.8rem;
            text-align: center;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.2rem;
        }

        .stat-value {
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.9rem;
        }

        .feature-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            margin-right: 0.5rem;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
            border-radius: 0 0 15px 15px;
        }

        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }

        .price-unit {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .featured-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .featured-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ffc107;
            color: #000;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .price-tag {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 1rem 0;
        }

        .limits {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
        }

        .view-toggles .btn {
            border-radius: 8px;
        }

        .view-toggles .btn.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .filters-section .form-control,
        .filters-section .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .filters-section .form-control:focus,
        .filters-section .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .no-results-icon {
            opacity: 0.3;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background: #2c3e50;
            color: white;
        }

        .service-info .service-name {
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .hero-section {
                text-align: center;
            }

            .hero-stats {
                margin-top: 2rem;
            }

            .filters-section .row > div {
                margin-bottom: 1rem;
            }

            .service-card {
                margin-bottom: 1.5rem;
            }

            .view-toggles {
                margin-top: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gridViewBtn = document.getElementById('gridView');
            const listViewBtn = document.getElementById('listView');
            const servicesGrid = document.getElementById('servicesGrid');
            const servicesList = document.getElementById('servicesList');

            // View toggle functionality
            gridViewBtn.addEventListener('click', function() {
                servicesGrid.classList.remove('d-none');
                servicesList.classList.add('d-none');
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                localStorage.setItem('servicesView', 'grid');
            });

            listViewBtn.addEventListener('click', function() {
                servicesList.classList.remove('d-none');
                servicesGrid.classList.add('d-none');
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                localStorage.setItem('servicesView', 'list');
            });

            // Restore saved view preference
            const savedView = localStorage.getItem('servicesView');
            if (savedView === 'list') {
                listViewBtn.click();
            }

            // Auto-submit filters on change
            document.querySelectorAll('select[name="platform"], select[name="category"], select[name="sort_by"], select[name="sort_order"]').forEach(function(select) {
                select.addEventListener('change', function() {
                    // Add a small delay to prevent rapid submissions
                    setTimeout(function() {
                        document.getElementById('filtersForm').submit();
                    }, 100);
                });
            });

            // Search with debounce
            let searchTimeout;
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                            document.getElementById('filtersForm').submit();
                        }
                    }, 500);
                });
            }
        });
    </script>
@endsection
