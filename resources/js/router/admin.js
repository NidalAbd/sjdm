import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    { path: '/admin', redirect: '/admin/dashboard' },
    { path: '/admin/dashboard', name: 'Dashboard', component: () => import('../views/admin/Dashboard.vue'), meta: { title: 'Dashboard' } },
    { path: '/admin/orders', name: 'Orders', component: () => import('../views/admin/Orders.vue'), meta: { title: 'Orders' } },
    { path: '/admin/orders/create', name: 'OrderCreate', component: () => import('../views/admin/OrderCreate.vue'), meta: { title: 'New Order' } },
    { path: '/admin/services', name: 'Services', component: () => import('../views/admin/Services.vue'), meta: { title: 'Services' } },
    { path: '/admin/services/:id/edit', name: 'ServiceEdit', component: () => import('../views/admin/ServiceEdit.vue'), meta: { title: 'Edit Service' } },
    { path: '/admin/transactions', name: 'Transactions', component: () => import('../views/admin/Transactions.vue'), meta: { title: 'Transactions' } },
    { path: '/admin/transactions/create', name: 'TransactionCreate', component: () => import('../views/admin/TransactionCreate.vue'), meta: { title: 'Add Balance' } },
    { path: '/admin/support', name: 'Support', component: () => import('../views/admin/Support.vue'), meta: { title: 'Support' } },
    { path: '/admin/support/:id', name: 'SupportDetail', component: () => import('../views/admin/SupportDetail.vue'), meta: { title: 'Ticket' } },
    { path: '/admin/users', name: 'Users', component: () => import('../views/admin/Users.vue'), meta: { title: 'Users' } },
    { path: '/admin/roles', name: 'Roles', component: () => import('../views/admin/Roles.vue'), meta: { title: 'Roles' } },
    { path: '/admin/permissions', name: 'Permissions', component: () => import('../views/admin/Permissions.vue'), meta: { title: 'Permissions' } },
    { path: '/admin/payment-methods', name: 'PaymentMethods', component: () => import('../views/admin/PaymentMethods.vue'), meta: { title: 'Payment Methods' } },
    { path: '/admin/notifications', name: 'Notifications', component: () => import('../views/admin/Notifications.vue'), meta: { title: 'Notifications' } },
    { path: '/admin/profile', name: 'Profile', component: () => import('../views/admin/Profile.vue'), meta: { title: 'Profile' } },
    { path: '/admin/referrals', name: 'Referrals', component: () => import('../views/admin/Referrals.vue'), meta: { title: 'Referrals' } },
    { path: '/admin/points', name: 'Points', component: () => import('../views/admin/Points.vue'), meta: { title: 'Points' } },
    { path: '/admin/services/fetch-ar', name: 'FetchServicesAr', component: () => import('../views/admin/FetchServices.vue'), meta: { title: 'Fetch AR', language: 'ar' } },
    { path: '/admin/services/fetch-en', name: 'FetchServicesEn', component: () => import('../views/admin/FetchServices.vue'), meta: { title: 'Fetch EN', language: 'en' } },
    { path: '/admin/languages', name: 'Languages', component: () => import('../views/admin/Languages.vue'), meta: { title: 'Languages' } },
    { path: '/admin/seo', name: 'SeoDashboard', component: () => import('../views/admin/SeoDashboard.vue'), meta: { title: 'SEO Dashboard' } },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to) => {
    document.title = (to.meta.title || 'Admin') + ' - SJDM'
})

export default router
