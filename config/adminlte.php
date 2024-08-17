<?php

return [

    'title' => 'SJDM',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => false,

    'google_fonts' => [
        'allowed' => true,
    ],

    'logo' => '<b>SJDM</b>',
    'logo_img' => 'images/MaxPeak.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'SJDM Logo',

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'images/MaxPeak.png',
            'alt' => 'SJDM Logo',
            'class' => 'brand-image img-circle elevation-3',
            'width' => 50,
            'height' => 50,
        ],
    ],

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images/MaxPeak.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary-outline',
    'usermenu_image' => true,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    'layout_topnav' => false,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => 'container',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container-fluid',

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => 'profile/settings',

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    'menu' => [
        // Left-aligned items (close to the username)

        [
            'type' => 'navbar-search',
            'text' => 'Search',
            'topnav_right' => true, // Left alignment
        ],
        [
            'text'         => '',
            'url'          => '#',
            'icon'         => 'fas fa-moon',
            'topnav_right' => true, // Left alignment
            'id'           => 'dark-mode-toggle',
        ],
        [
            'type'          => 'navbar-notification',
            'id'            => 'notifications',
            'icon'          => 'far fa-bell',
            'label'         => 3,
            'label_color'   => 'danger',
            'topnav_right'  => true, // Left alignment
            'url'           => 'notifications',
        ],

        // Admin category remains with submenu items
        [
            'text'    => 'Admin',
            'icon'    => 'fas fa-fw fa-user-shield',
            'topnav_center' => true,
            'can' => 'view_dashboard',
            'submenu' => [
                [
                    'text' => 'Users',
                    'url'  => 'users',
                    'icon' => 'fas fa-fw fa-user',
                    'can'  => 'view_any_user',
                ],
                [
                    'text' => 'Roles',
                    'url'  => 'roles',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'can'  => 'view_any_role',
                ],
                [
                    'text' => 'Permissions',
                    'url'  => 'permissions',
                    'icon' => 'fas fa-fw fa-lock',
                    'can'  => 'view_any_permission',
                ],
            ],
        ],

        // Flattened main items
        [
            'text' => 'Add Order',
            'url'  => 'orders/create', // Just the relative URL
            'icon' => 'fas fa-cart-plus',
            'topnav_center' => true,
            'can'  => 'create_order',
        ],
        [
            'text' => 'Orders',
            'url'  => 'orders', // Just the relative URL
            'icon' => 'fas fa-shopping-cart',
            'topnav_center' => true,
            'can'  => 'view_orders',
        ],
        [
            'text' => 'Balance',
            'url'  => 'transactions', // Just the relative URL
            'icon' => 'fas fa-wallet',
            'topnav_center' => true,
            'can'  => 'add_balance',
        ],

        [
            'text' => 'Add Balance',
            'url'  => 'transactions/create', // Just the relative URL
            'icon' => 'fab fa-stripe',
            'topnav_center' => true,
            'can'  => 'add_balance',
        ],
        [
            'text' => 'Support',
            'url'  => 'support',
            'icon' => 'fas fa-headset',
            'topnav_center' => true,
            'can'  => 'view_support',
        ],
        [
            'text' => 'Service',
            'url'  => 'services',
            'icon' => 'fas fa-tools',
            'topnav_center' => true,
            'can'  => 'view_services',
        ],
    ],


    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    'livewire' => false,
];
