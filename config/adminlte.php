<?php

return array(

    'title' => 'SJDM',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => true,

    'google_fonts' => array(
        'allowed' => true,
    ),

    'logo' => '<b>SJDM</b>',
    'logo_img' => 'images/favicon-96x96.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'SJDM Logo',

    'auth_logo' => array(
        'enabled' => true,
        'img' => array(
            'path' => 'images/favicon-96x96.png',
            'alt' => 'SJDM Logo',
            'class' => 'brand-image img-circle elevation-3',
            'width' => 50,
            'height' => 50,
        ),
    ),

    'preloader' => array(
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => array(
            'path' => 'images/sjdm_logo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ),
    ),

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary-outline',
    'usermenu_image' => true,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    'layout_topnav' => false,
    'layout_boxed' => null,  // Ensure this is null or false for full width
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => true,
    'layout_dark_mode' => true,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',  // You can leave this as an empty string
    'classes_content_header' => '',   // You can leave this as an empty string
    'classes_content' => 'container-fluid',  // Use 'container-fluid' for full width
    'classes_sidebar' => 'sidebar-dark-primary',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container-fluid',  // Already set correctly

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
    'dashboard_url' => '/admin/dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => 'profile/settings',

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    'menu' => array(
        // Left-aligned items (close to the username)
        array(
            'text'         => '',
            'url'          => '#',
            'icon'         => 'fas fa-moon',
            'topnav_right' => true, // Left alignment
            'id'           => 'dark-mode-toggle',
            'icon_color'   => 'secondary', // Set predefined color
        ),

        // Notification Bell
        array(
            'text'         => '',
            'url'          => '#',
            'icon'         => 'fas fa-bell',
            'topnav_right' => true,
            'id'           => 'notification-dropdown',
            'icon_color'   => 'warning',
            'classes'      => 'notification-bell',
            'label'        => 0, // Will be updated by JavaScript
            'label_color'  => 'danger',
        ),

        array(
            'text' =>'home',
            'url'  => '/',
            'icon' => 'fas fa-home',
            'icon_color' => 'primary',
            'target' => '_blank',
        ),


        // Admin category with submenu items
        array(
            'text'    => 'admin', // Just use the translation key
            'icon'    => 'fas fa-fw fa-user-shield',
            'topnav_center' => true,
            'can' => 'add_balance',
            'icon_color'   => 'primary', // Set predefined color
            'submenu' => array(
                array(
                    'text' => 'users',
                    'url'  => 'admin/users',
                    'icon' => 'fas fa-fw fa-user',
                    'can'  => 'view_any_user',
                    'icon_color'   => 'success',
                ),
                array(
                    'text' => 'roles',
                    'url'  => 'admin/roles',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'can'  => 'view_any_role',
                    'icon_color'   => 'info',
                ),
                array(
                    'text' => 'permissions',
                    'url'  => 'admin/permissions',
                    'icon' => 'fas fa-fw fa-lock',
                    'can'  => 'view_any_permission',
                    'icon_color'   => 'warning',
                ),
                array(
                    'text' => 'payment_methods',
                    'url'  => 'admin/payment-methods',
                    'icon' => 'fas fa-fw fa-credit-card',
                    'can'  => 'add_balance',
                    'icon_color'   => 'purple',
                ),
                array(
                    'text' => 'fetch services ar',
                    'url'  => 'admin/services/fetch-ar',
                    'icon' => 'fas fa-sync-alt',
                    'can'  => 'fetch_services',
                    'icon_color'   => 'info',
                ),
                array(
                    'text' => 'fetch services en',
                    'url'  => 'admin/services/fetch-en',
                    'icon' => 'fas fa-sync-alt',
                    'can'  => 'fetch_services',
                    'icon_color'   => 'info',
                ),
                array(
                    'text' => 'Languages',
                    'url'  => 'admin/languages',
                    'icon' => 'fas fa-language',
                    'can'  => 'add_balance',
                    'icon_color'   => 'teal',
                ),
            ),
        ),

        // Flattened main items
        array(
            'text' => 'add_order',
            'url'  => 'admin/orders/create',
            'icon' => 'fas fa-cart-plus',
            'can'  => 'create_order',
            'icon_color'   => 'primary',
        ),
        array(
            'text' => 'orders',
            'url'  => 'admin/orders',
            'icon' => 'fas fa-shopping-cart',
            'can'  => 'view_orders',
            'icon_color'   => 'success',
        ),
        array(
            'text' => 'services',
            'url'  => 'admin/services',
            'icon' => 'fas fa-tools',
            'can'  => 'view_anyServices_services',
            'icon_color'   => 'info',
        ),
        array(
            'text' => 'add_balance',
            'url'  => 'admin/transactions/create',
            'icon' => 'fab fa-stripe',
            'can'  => 'view_balance',
            'icon_color'   => 'purple',
        ),
        array(
            'text' => 'transactions',
            'url'  => 'admin/transactions',
            'icon' => 'fas fa-comments-dollar',
            'can'  => 'view_any_transaction',
            'icon_color'   => 'warning',
        ),
        array(
            'text' => 'support',
            'url'  => 'admin/support',
            'icon' => 'fas fa-headset',
            'can'  => 'view_support',
            'icon_color'   => 'danger',
        ),
        array(
            'text' => 'notifications',
            'url'  => 'admin/notifications',
            'icon' => 'fas fa-bell',
            'can'  => 'view_support',
            'icon_color'   => 'warning',
        ),
        array(
            'text' => 'referral',
            'url'  => 'admin/referrals',
            'icon' => 'fas fa-user-friends',
            'can'  => 'view_support',
            'icon_color' => 'success',
        ),
        array(
            'text' => 'Points',
            'url'  => 'admin/points',
            'icon' => 'fas fa-coins',
            'can'  => 'view_support',
            'icon_color' => 'warning',
        ),



    ),

    'filters' => array(
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class, // Ensure this filter is enabled
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ),

    'plugins' => array(
        'Datatables' => array(
            'active' => true,
            'files' => array(
                array(
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ),
                array(
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ),
                array(
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ),
            ),
        ),
        'Select2' => array(
            'active' => true,
            'files' => array(
                array(
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ),
                array(
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ),
            ),
        ),
        'Chartjs' => array(
            'active' => true,
            'files' => array(
                array(
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ),
            ),
        ),
        'Sweetalert2' => array(
            'active' => true,
            'files' => array(
                array(
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ),
            ),
        ),
        'Pace' => array(
            'active' => true,
            'files' => array(
                array(
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ),
                array(
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ),
            ),
        ),
    ),

    'iframe' => array(
        'default_tab' => array(
            'url' => null,
            'title' => null,
        ),
        'buttons' => array(
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ),
        'options' => array(
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ),
    ),

    'livewire' => false,
);
