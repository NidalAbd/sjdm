import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('../views/Home.vue'),
        meta: { title: 'Best SMM Panel - Buy Instagram, TikTok, YouTube Followers' }
    },
    {
        path: '/all-services',
        name: 'Services',
        component: () => import('../views/Services.vue'),
        meta: { title: 'All Services - SMM Panel' }
    },
    {
        path: '/service/:id',
        name: 'ServiceDetails',
        component: () => import('../views/ServiceDetails.vue'),
        meta: { title: 'Service Details - SMM Panel' }
    },
    {
        path: '/about',
        name: 'About',
        component: () => import('../views/About.vue'),
        meta: { title: 'About Us - SMM Panel' }
    },
    {
        path: '/contact-us',
        name: 'Contact',
        component: () => import('../views/Contact.vue'),
        meta: { title: 'Contact Us - SMM Panel' }
    },
    {
        path: '/faq',
        name: 'FAQ',
        component: () => import('../views/FAQ.vue'),
        meta: { title: 'FAQ - SMM Panel' }
    },
    {
        path: '/privacy-policy',
        name: 'PrivacyPolicy',
        component: () => import('../views/PrivacyPolicy.vue'),
        meta: { title: 'Privacy Policy - SMM Panel' }
    },
    {
        path: '/how-it-works',
        name: 'HowItWorks',
        component: () => import('../views/HowItWorks.vue'),
        meta: { title: 'How It Works - SMM Panel' }
    },
]

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
    next()
})

export default router
