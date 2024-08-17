<header class="main-header navbar navbar-expand {{ session('dark-mode') ? 'navbar-dark' : 'navbar-light' }} bg-light">
    <div class="container-fluid">
        <button class="btn btn-icon-only" data-widget="pushmenu" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/MaxPeak.png') }}" alt="Max Peak Logo" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
            <span class="brand-text font-weight-bold" style="font-size: 1.25rem;">Max Peak</span>
        </a>

        <div class="d-flex align-items-center ml-auto">
            <div class="dropdown me-3">
                <button class="btn btn-outline-secondary dropdown-toggle text-var-primary" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-globe"></i> @lang('messages.language')
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('ar')"><img src="{{ asset('images/arabic-flag.png') }}" alt="Arabic Language" width="20"> العربية</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('en')"><img src="{{ asset('images/english-flag.png') }}" alt="English Language" width="20"> English</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('es')"><img src="{{ asset('images/spanish-flag.png') }}" alt="Spanish Language" width="20"> Spanish</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('fr')"><img src="{{ asset('images/french-flag.png') }}" alt="French Language" width="20"> French</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('de')"><img src="{{ asset('images/german-flag.png') }}" alt="German Language" width="20"> German</a></li>
                </ul>
            </div>

            @guest
                <a class="btn btn-outline-secondary me-3 text-var-primary" href="{{ route('login') }}">@lang('messages.login')</a>
                @if (Route::has('register'))
                    <a class="btn btn-outline-secondary me-3 text-var-primary" href="{{ route('register') }}">@lang('messages.register')</a>
                @endif
            @else
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary dropdown-toggle text-var-primary" type="button" id="authDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="authDropdown">
                        @role('admin')<li><a class="dropdown-item" href="{{ route('home') }}">@lang('messages.dashboard')</a></li>@endrole
                        <li><a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                @lang('messages.logout')
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
                <div class="me-3">
                    <span class="btn btn-outline-secondary text-var-primary">
                        Balance: {{ number_format(Auth::user()->balance, 2) }} {{ Auth::user()->currency }}
                    </span>
                </div>
            @endguest

            <button class="btn btn-outline-secondary text-var-primary" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </div>
</header>
@section('scripts')
    <script>
        // Initialize AdminLTE Push Menu
        $(document).ready(function () {
            $('[data-widget="pushmenu"]').PushMenu();

            // Adjust the header and footer full width
            $('.main-header, .main-footer').addClass('container-fluid').removeClass('container');

            // Adjust RTL settings
            if ($('body').hasClass('rtl')) {
                $('.main-sidebar').css('right', '0');
                $('.main-sidebar').css('left', 'auto');
            }
        });

        // Dark/Light Mode Toggle
        document.getElementById('themeToggle').addEventListener('click', function () {
            const isDarkMode = document.body.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', isDarkMode);

            // Update icon
            const themeIcon = isDarkMode ? 'fa-sun' : 'fa-moon';
            this.innerHTML = `<i class="fas ${themeIcon}"></i>`;

            // Toggle AdminLTE dark mode classes
            toggleDarkModeClasses(isDarkMode);
        });

        function toggleDarkModeClasses(isDarkMode) {
            // Sidebar
            const sidebar = document.querySelector('.main-sidebar');
            sidebar.classList.toggle('sidebar-dark-primary', isDarkMode);
            sidebar.classList.toggle('sidebar-light-primary', !isDarkMode);

            // Alerts
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.toggle('alert-dark', isDarkMode);
                alert.classList.toggle('alert-light', !isDarkMode);
            });

            // Breadcrumbs
            const breadcrumbs = document.querySelector('.breadcrumb');
            breadcrumbs.classList.toggle('breadcrumb-dark', isDarkMode);
            breadcrumbs.classList.toggle('breadcrumb-light', !isDarkMode);

            // Table
            const table = document.querySelector('.table');
            table.classList.toggle('table-dark', isDarkMode);
            table.classList.toggle('table-light', !isDarkMode);
        }

        // Language and RTL Toggle
        function changeLanguage(lang) {
            const isRtl = lang === 'ar';
            localStorage.setItem('language', lang);
            localStorage.setItem('rtl', isRtl);

            document.documentElement.setAttribute('lang', lang);
            document.body.setAttribute('dir', isRtl ? 'rtl' : 'ltr');
            document.body.classList.toggle('rtl', isRtl);

            location.reload();
        }

        // Apply settings on page load
        document.addEventListener('DOMContentLoaded', function () {
            const savedLang = localStorage.getItem('language') || 'en';
            const isRtl = localStorage.getItem('rtl') === 'true';
            const darkMode = localStorage.getItem('dark-mode') === 'true';

            document.documentElement.setAttribute('lang', savedLang);
            document.body.setAttribute('dir', isRtl ? 'rtl' : 'ltr');
            document.body.classList.toggle('rtl', isRtl);

            if (darkMode) {
                document.body.classList.add('dark-mode');
                document.getElementById('themeToggle').innerHTML = '<i class="fas fa-sun"></i>';
            }

            toggleDarkModeClasses(darkMode);
        });
    </script>
@endsection
