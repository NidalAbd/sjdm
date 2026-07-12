// Sidebar menu structure for the admin shell (AdminApp.vue).
// Mirrors config/adminlte.php's `menu` array (icons translated from Font Awesome to MDI).
// `can` values are permission names checked against the Pinia auth store (see AdminApp.vue).

export const adminMenu = [
    { text: 'Add Order', to: '/admin/orders/create', icon: 'mdi-cart-plus', color: 'primary', can: 'create_order' },
    { text: 'Orders', to: '/admin/orders', icon: 'mdi-cart', color: 'success', can: 'view_orders' },
    { text: 'Services', to: '/admin/services', icon: 'mdi-tools', color: 'info', can: 'view_anyServices_services' },
    { text: 'Add Balance', to: '/admin/transactions/create', icon: 'mdi-cash-plus', color: 'purple', can: 'view_balance' },
    { text: 'Transactions', to: '/admin/transactions', icon: 'mdi-cash-multiple', color: 'warning', can: 'view_any_transaction' },
    { text: 'Support', to: '/admin/support', icon: 'mdi-headset', color: 'error', can: 'view_support' },
    { text: 'Notifications', to: '/admin/notifications', icon: 'mdi-bell', color: 'warning', can: 'view_support' },
    { text: 'Referrals', to: '/admin/referrals', icon: 'mdi-account-group', color: 'success', can: 'view_support' },
    { text: 'Points', to: '/admin/points', icon: 'mdi-coins', color: 'warning', can: 'view_support' },
    {
        text: 'Administration',
        icon: 'mdi-shield-account',
        color: 'primary',
        can: 'add_balance',
        children: [
            { text: 'Users', to: '/admin/users', icon: 'mdi-account', color: 'success', can: 'view_any_user' },
            { text: 'Roles', to: '/admin/roles', icon: 'mdi-account-cog', color: 'info', can: 'view_any_role' },
            { text: 'Permissions', to: '/admin/permissions', icon: 'mdi-lock', color: 'warning', can: 'view_any_permission' },
            { text: 'Payment Methods', to: '/admin/payment-methods', icon: 'mdi-credit-card', color: 'purple', can: 'add_balance' },
            { text: 'Fetch Services (AR)', to: '/admin/services/fetch-ar', icon: 'mdi-sync', color: 'info', can: 'fetch_services' },
            { text: 'Fetch Services (EN)', to: '/admin/services/fetch-en', icon: 'mdi-sync', color: 'info', can: 'fetch_services' },
            { text: 'Languages', to: '/admin/languages', icon: 'mdi-web', color: 'teal', can: 'add_balance' },
            { text: 'SEO Dashboard', to: '/admin/seo', icon: 'mdi-chart-line', color: 'info', can: 'add_balance' },
        ],
    },
]
