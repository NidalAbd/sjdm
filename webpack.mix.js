const mix = require('laravel-mix');
require('laravel-mix-purgecss');

// Compile AdminLTE styles and apply PurgeCSS
mix.sass('resources/sass/app.scss', 'public/css')
    .purgeCss({
        content: [
            './resources/**/*.blade.php',  // Laravel Blade templates
            './resources/**/*.vue',        // Vue components (if used)
            './resources/js/**/*.js',      // Custom JavaScript files
            './node_modules/admin-lte/**/*.js', // AdminLTE JS files
            './public/js/**/*.js',         // Compiled JS files
        ],
        safelist: [
            'sidebar-mini', 'nav-item', 'active', 'menu-open', 'text-danger', 'text-warning', 'text-info',
            'dropdown-menu', 'dropdown-item', 'btn-primary', 'btn-secondary', 'card', 'table', 'modal',
            /^navbar-/, /^btn-/, /^alert-/, /^fa-/, /^card-/, /^table-/, /^form-/, /^text-/, /^bg-/
        ],
        defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || []
    });

// Enable versioning for cache busting in production
if (mix.inProduction()) {
    mix.version();
}

// Enable source maps for easier debugging in development
mix.sourceMaps();
