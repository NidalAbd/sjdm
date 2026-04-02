import { createRouter, createWebHistory } from 'vue-router'

// Page components
const Home = () => import('../views/Home.vue')
const Services = () => import('../views/Services.vue')
const ServiceDetails = () => import('../views/ServiceDetails.vue')
const About = () => import('../views/About.vue')
const Contact = () => import('../views/Contact.vue')
const FAQ = () => import('../views/FAQ.vue')
const PrivacyPolicy = () => import('../views/PrivacyPolicy.vue')
const HowItWorks = () => import('../views/HowItWorks.vue')

// Route definitions (shared between default and localized)
const pageRoutes = [
    { path: '', name: 'Home', component: Home, meta: { title: 'Best SMM Panel' } },
    { path: 'all-services', name: 'Services', component: Services, meta: { title: 'All Services' } },
    { path: 'service/:id', name: 'ServiceDetails', component: ServiceDetails, meta: { title: 'Service Details' } },
    { path: 'about', name: 'About', component: About, meta: { title: 'About Us' } },
    { path: 'contact-us', name: 'Contact', component: Contact, meta: { title: 'Contact Us' } },
    { path: 'faq', name: 'FAQ', component: FAQ, meta: { title: 'FAQ' } },
    { path: 'privacy-policy', name: 'PrivacyPolicy', component: PrivacyPolicy, meta: { title: 'Privacy Policy' } },
    { path: 'how-it-works', name: 'HowItWorks', component: HowItWorks, meta: { title: 'How It Works' } },
    { path: 'terms-and-conditions', name: 'Terms', component: PrivacyPolicy, meta: { title: 'Terms' } },
]

// Generate routes: default (/) + all 16 locale prefixes (/:locale/)
const locales = ['ar','es','fr','de','ru','zh','hi','pt','ja','ko','tr','it','pl','nl','vi','th']

const publicRoutes = [
    // Default English routes (no prefix)
    ...pageRoutes.map(r => ({
        ...r,
        path: '/' + r.path,
        name: r.name,
    })),
    // Localized routes (/:locale/path)
    ...locales.flatMap(locale =>
        pageRoutes.map(r => ({
            ...r,
            path: '/' + locale + (r.path ? '/' + r.path : ''),
            name: r.name + '_' + locale,
        }))
    ),
]

// Admin/Dashboard routes (protected)
const adminRoutes = [
    {
        path: '/admin',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/admin/dashboard'
            },
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: () => import('../views/admin/Dashboard.vue'),
                meta: { title: 'Dashboard - SJDM', breadcrumb: 'Dashboard' }
            },
            // Orders
            {
                path: 'orders',
                name: 'AdminOrders',
                component: () => import('../views/admin/Orders.vue'),
                meta: { title: 'Orders - SJDM', breadcrumb: 'Orders' }
            },
            {
                path: 'orders/create',
                name: 'AdminOrderCreate',
                component: () => import('../views/admin/OrderCreate.vue'),
                meta: { title: 'Create Order - SJDM', breadcrumb: 'Create Order' }
            },
            // Services
            {
                path: 'services',
                name: 'AdminServices',
                component: () => import('../views/admin/Services.vue'),
                meta: { title: 'Services - SJDM', breadcrumb: 'Services' }
            },
            {
                path: 'services/:id/edit',
                name: 'AdminServiceEdit',
                component: () => import('../views/admin/ServiceEdit.vue'),
                meta: { title: 'Edit Service - SJDM', breadcrumb: 'Edit Service' }
            },
            // Transactions
            {
                path: 'transactions',
                name: 'AdminTransactions',
                component: () => import('../views/admin/Transactions.vue'),
                meta: { title: 'Transactions - SJDM', breadcrumb: 'Transactions' }
            },
            {
                path: 'transactions/create',
                name: 'AdminTransactionCreate',
                component: () => import('../views/admin/TransactionCreate.vue'),
                meta: { title: 'Add Balance - SJDM', breadcrumb: 'Add Balance' }
            },
            // Support
            {
                path: 'support',
                name: 'AdminSupport',
                component: () => import('../views/admin/Support.vue'),
                meta: { title: 'Support - SJDM', breadcrumb: 'Support' }
            },
            {
                path: 'support/:id',
                name: 'AdminSupportDetail',
                component: () => import('../views/admin/SupportDetail.vue'),
                meta: { title: 'Support Ticket - SJDM', breadcrumb: 'Ticket' }
            },
            // Users (Admin only)
            {
                path: 'users',
                name: 'AdminUsers',
                component: () => import('../views/admin/Users.vue'),
                meta: { title: 'Users - SJDM', breadcrumb: 'Users', requiresAdmin: true }
            },
            // Roles (Admin only)
            {
                path: 'roles',
                name: 'AdminRoles',
                component: () => import('../views/admin/Roles.vue'),
                meta: { title: 'Roles - SJDM', breadcrumb: 'Roles', requiresAdmin: true }
            },
            // Permissions (Admin only)
            {
                path: 'permissions',
                name: 'AdminPermissions',
                component: () => import('../views/admin/Permissions.vue'),
                meta: { title: 'Permissions - SJDM', breadcrumb: 'Permissions', requiresAdmin: true }
            },
            // Payment Methods (Admin only)
            {
                path: 'payment-methods',
                name: 'AdminPaymentMethods',
                component: () => import('../views/admin/PaymentMethods.vue'),
                meta: { title: 'Payment Methods - SJDM', breadcrumb: 'Payment Methods', requiresAdmin: true }
            },
            // Notifications
            {
                path: 'notifications',
                name: 'AdminNotifications',
                component: () => import('../views/admin/Notifications.vue'),
                meta: { title: 'Notifications - SJDM', breadcrumb: 'Notifications' }
            },
            // Profile
            {
                path: 'profile',
                name: 'AdminProfile',
                component: () => import('../views/admin/Profile.vue'),
                meta: { title: 'Profile - SJDM', breadcrumb: 'Profile' }
            },
            // Referrals
            {
                path: 'referrals',
                name: 'AdminReferrals',
                component: () => import('../views/admin/Referrals.vue'),
                meta: { title: 'Referrals - SJDM', breadcrumb: 'Referrals' }
            },
            // Points
            {
                path: 'points',
                name: 'AdminPoints',
                component: () => import('../views/admin/Points.vue'),
                meta: { title: 'Points - SJDM', breadcrumb: 'Points' }
            },
            // Fetch Services (Admin only)
            {
                path: 'services/fetch-ar',
                name: 'AdminFetchServicesAr',
                component: () => import('../views/admin/FetchServices.vue'),
                meta: { title: 'Fetch Services AR - SJDM', breadcrumb: 'Fetch Services AR', requiresAdmin: true, language: 'ar' }
            },
            {
                path: 'services/fetch-en',
                name: 'AdminFetchServicesEn',
                component: () => import('../views/admin/FetchServices.vue'),
                meta: { title: 'Fetch Services EN - SJDM', breadcrumb: 'Fetch Services EN', requiresAdmin: true, language: 'en' }
            },
            // Languages & Translations (Admin only)
            {
                path: 'languages',
                name: 'AdminLanguages',
                component: () => import('../views/admin/Languages.vue'),
                meta: { title: 'Languages - SJDM', breadcrumb: 'Languages', requiresAdmin: true }
            },
            // SEO Dashboard (Admin only)
            {
                path: 'seo',
                name: 'AdminSeoDashboard',
                component: () => import('../views/admin/SeoDashboard.vue'),
                meta: { title: 'SEO Dashboard - SJDM', breadcrumb: 'SEO Dashboard', requiresAdmin: true }
            },
        ]
    }
]

const routes = [...publicRoutes, ...adminRoutes]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (to.hash) {
            return { el: to.hash, behavior: 'smooth' }
        }
        if (savedPosition) {
            return savedPosition
        }
        return { top: 0, behavior: 'smooth' }
    }
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title || 'SMM Panel'

    // Check if route requires authentication
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // For now, we'll let the server handle authentication
        // The Laravel middleware will redirect to login if not authenticated
        next()
    } else {
        next()
    }
})

export default router
