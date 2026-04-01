<template>
    <v-app :theme="store.theme" class="admin-app" :class="{ 'rtl': store.isRTL }">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" @click="drawer = !drawer">
                    <v-icon size="20">mdi-menu</v-icon>
                </button>
                <span class="topbar-brand d-none d-sm-inline">{{ $t('header.panel') }}</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-balance">
                    <v-icon size="14" color="success" class="mr-1">mdi-wallet</v-icon>
                    ${{ formatBalance(user?.balance) }}
                </div>

                <button class="topbar-btn" @click="store.toggleTheme">
                    <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                </button>

                <v-menu max-height="300">
                    <template v-slot:activator="{ props }">
                        <button class="topbar-btn" v-bind="props">
                            <v-icon size="18">mdi-translate</v-icon>
                        </button>
                    </template>
                    <v-list density="compact" nav>
                        <v-list-item v-for="lang in store.languages" :key="lang.code" @click="changeLanguage(lang.code)" :active="store.locale === lang.code" density="compact">
                            <v-list-item-title class="text-body-2">{{ lang.native_name }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>

                <button class="topbar-btn" @click="$router.push('/admin/notifications')">
                    <v-badge :content="unreadNotifications" color="error" :model-value="unreadNotifications > 0" dot>
                        <v-icon size="18">mdi-bell-outline</v-icon>
                    </v-badge>
                </button>

                <v-menu>
                    <template v-slot:activator="{ props }">
                        <button class="topbar-user" v-bind="props">
                            <v-avatar size="30" color="primary">
                                <v-img v-if="user?.avatar" :src="user.avatar"></v-img>
                                <span v-else class="text-white text-caption">{{ user?.name?.charAt(0) || 'U' }}</span>
                            </v-avatar>
                            <span class="topbar-username d-none d-md-inline">{{ user?.name || 'User' }}</span>
                            <v-icon size="14">mdi-chevron-down</v-icon>
                        </button>
                    </template>
                    <v-list density="compact" nav>
                        <v-list-item to="/admin/profile" prepend-icon="mdi-account-outline" density="compact">
                            <v-list-item-title class="text-body-2">{{ $t('nav.profile') }}</v-list-item-title>
                        </v-list-item>
                        <v-divider class="my-1"></v-divider>
                        <v-list-item @click="logout" prepend-icon="mdi-logout" base-color="error" density="compact">
                            <v-list-item-title class="text-body-2">{{ $t('nav.logout') }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
        </header>

        <!-- Sidebar -->
        <aside class="admin-sidebar" :class="{ 'collapsed': !drawer, 'rail': rail }">
            <div class="sidebar-brand">
                <v-avatar size="32" rounded="lg">
                    <v-img src="/images/logo.png"></v-img>
                </v-avatar>
                <span v-if="!rail" class="sidebar-brand-text">SJDM</span>
                <button v-if="!rail" class="sidebar-collapse-btn" @click="rail = true">
                    <v-icon size="16">mdi-chevron-left</v-icon>
                </button>
            </div>

            <nav class="sidebar-nav" @click="rail && (rail = false)">
                <div class="sidebar-section">
                    <router-link v-for="item in mainMenuItems" :key="item.key" :to="item.to" class="sidebar-item" :class="{ active: isActive(item.to) }">
                        <v-icon size="20">{{ item.icon }}</v-icon>
                        <span v-if="!rail" class="sidebar-item-text">{{ $t(item.titleKey) }}</span>
                    </router-link>
                </div>

                <template v-if="isAdmin">
                    <div class="sidebar-divider"></div>
                    <div v-if="!rail" class="sidebar-label">{{ $t('nav.admin') }}</div>
                    <div class="sidebar-section">
                        <router-link v-for="item in adminMenuItems" :key="item.key" :to="item.to" class="sidebar-item" :class="{ active: isActive(item.to) }">
                            <v-icon size="20">{{ item.icon }}</v-icon>
                            <span v-if="!rail" class="sidebar-item-text">{{ $t(item.titleKey) }}</span>
                        </router-link>
                    </div>
                </template>
            </nav>
        </aside>

        <!-- Mobile Overlay -->
        <div v-if="drawer && isMobile" class="sidebar-overlay" @click="drawer = false"></div>

        <!-- Main Content -->
        <main class="admin-main" :class="{ 'sidebar-open': drawer && !rail, 'sidebar-rail': rail }">
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </main>

        <!-- Snackbar -->
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top right" rounded="lg">
            {{ snackbar.text }}
            <template v-slot:actions>
                <v-btn variant="text" size="small" @click="snackbar.show = false">{{ $t('common.close') }}</v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, provide } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore, themeColors } from '../stores/app'
import { useAuthStore } from '../stores/auth'
import { updateThemeColors } from '../plugins/vuetify'
import { loadLocaleMessages } from '../plugins/i18n'
import axios from 'axios'

const { t, locale } = useI18n()
const store = useAppStore()
const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()

const drawer = ref(window.innerWidth >= 1024)
const rail = ref(false)
const isMobile = ref(window.innerWidth < 1024)
const notifications = ref([])
const snackbar = ref({ show: false, text: '', color: 'success' })

const handleResize = () => {
    isMobile.value = window.innerWidth < 1024
    if (isMobile.value) { drawer.value = false; rail.value = false }
}

watch(() => store.locale, (newLocale) => {
    locale.value = newLocale
    document.documentElement.dir = store.isRTL ? 'rtl' : 'ltr'
    document.documentElement.lang = newLocale
}, { immediate: true })

const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const unreadNotifications = computed(() => notifications.value.filter(n => !n.read_at).length)

const isActive = (to) => route.path === to || route.path.startsWith(to + '/')

const mainMenuItems = [
    { key: 'dashboard', titleKey: 'nav.dashboard', icon: 'mdi-view-dashboard-outline', to: '/admin/dashboard' },
    { key: 'newOrder', titleKey: 'nav.newOrder', icon: 'mdi-cart-plus', to: '/admin/orders/create' },
    { key: 'orders', titleKey: 'nav.orders', icon: 'mdi-shopping-outline', to: '/admin/orders' },
    { key: 'services', titleKey: 'nav.services', icon: 'mdi-cog-outline', to: '/admin/services' },
    { key: 'addBalance', titleKey: 'nav.addBalance', icon: 'mdi-wallet-plus-outline', to: '/admin/transactions/create' },
    { key: 'transactions', titleKey: 'nav.transactions', icon: 'mdi-swap-horizontal', to: '/admin/transactions' },
    { key: 'support', titleKey: 'nav.support', icon: 'mdi-headset', to: '/admin/support' },
    { key: 'notifications', titleKey: 'nav.notifications', icon: 'mdi-bell-outline', to: '/admin/notifications' },
    { key: 'referrals', titleKey: 'nav.referrals', icon: 'mdi-account-group-outline', to: '/admin/referrals' },
    { key: 'points', titleKey: 'nav.points', icon: 'mdi-star-outline', to: '/admin/points' },
]

const adminMenuItems = [
    { key: 'users', titleKey: 'nav.users', icon: 'mdi-account-multiple-outline', to: '/admin/users' },
    { key: 'roles', titleKey: 'nav.roles', icon: 'mdi-shield-account-outline', to: '/admin/roles' },
    { key: 'permissions', titleKey: 'nav.permissions', icon: 'mdi-lock-outline', to: '/admin/permissions' },
    { key: 'paymentMethods', titleKey: 'nav.paymentMethods', icon: 'mdi-credit-card-outline', to: '/admin/payment-methods' },
    { key: 'fetchAr', titleKey: 'nav.fetchServicesAr', icon: 'mdi-sync', to: '/admin/services/fetch-ar' },
    { key: 'fetchEn', titleKey: 'nav.fetchServicesEn', icon: 'mdi-sync', to: '/admin/services/fetch-en' },
    { key: 'languages', titleKey: 'nav.languages', icon: 'mdi-translate', to: '/admin/languages' },
]

const formatBalance = (b) => (parseFloat(b) || 0).toFixed(2)

const changeLanguage = async (lang) => {
    localStorage.setItem('locale', lang)
    await loadLocaleMessages(lang)
    await store.setLocale(lang)
    try { await axios.post('/api/set-locale', { locale: lang }) } catch {}
    window.location.reload()
}

const logout = async () => {
    try { await axios.post('/logout') } catch {}
    authStore.clearUser()
    window.location.href = '/login'
}

const fetchNotifications = async () => {
    try { const r = await axios.get('/notifications/latest'); notifications.value = r.data } catch {}
}

const showSnackbar = (text, color = 'success') => { snackbar.value = { show: true, text, color } }

provide('showSnackbar', showSnackbar)

onMounted(async () => {
    await authStore.fetchUser()
    await store.fetchLanguages()
    fetchNotifications()
    updateThemeColors(store.themeColor)
    window.addEventListener('resize', handleResize)
    setInterval(fetchNotifications, 60000)
})

onUnmounted(() => { window.removeEventListener('resize', handleResize) })
</script>

<style scoped>
/* Top Bar */
.admin-topbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    background: rgb(var(--v-theme-surface));
    border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    z-index: 1001;
}
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-right { display: flex; align-items: center; gap: 4px; }
.topbar-toggle {
    width: 36px; height: 36px; border-radius: 10px; border: none;
    background: rgba(var(--v-theme-on-surface), 0.05); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: inherit; transition: background 0.15s ease;
}
.topbar-toggle:hover { background: rgba(var(--v-theme-on-surface), 0.1); }
.topbar-brand { font-size: 0.95rem; font-weight: 700; }
.topbar-balance {
    display: flex; align-items: center;
    font-size: 0.8rem; font-weight: 700; color: rgb(var(--v-theme-success));
    padding: 6px 12px; border-radius: 8px;
    background: rgba(var(--v-theme-success), 0.08);
    margin-right: 4px;
}
.topbar-btn {
    width: 36px; height: 36px; border-radius: 10px; border: none;
    background: transparent; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: inherit; transition: background 0.15s ease;
}
.topbar-btn:hover { background: rgba(var(--v-theme-on-surface), 0.07); }
.topbar-user {
    display: flex; align-items: center; gap: 8px;
    padding: 4px 8px 4px 4px; border-radius: 10px; border: none;
    background: transparent; cursor: pointer; color: inherit;
    transition: background 0.15s ease;
}
.topbar-user:hover { background: rgba(var(--v-theme-on-surface), 0.07); }
.topbar-username { font-size: 0.82rem; font-weight: 600; }

/* Sidebar */
.admin-sidebar {
    position: fixed;
    top: 52px;
    left: 0;
    bottom: 0;
    width: 240px;
    background: rgb(var(--v-theme-surface));
    border-right: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    transition: width 0.2s ease, transform 0.2s ease;
}
.admin-sidebar.rail { width: 64px; }
.admin-sidebar.collapsed { transform: translateX(-100%); }
.sidebar-brand {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px; border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}
.sidebar-brand-text { font-size: 1rem; font-weight: 800; letter-spacing: -0.02em; }
.sidebar-collapse-btn {
    margin-left: auto; width: 24px; height: 24px; border-radius: 6px; border: none;
    background: rgba(var(--v-theme-on-surface), 0.05); cursor: pointer;
    display: flex; align-items: center; justify-content: center; color: inherit;
}
.sidebar-nav { padding: 8px; }
.sidebar-section { display: flex; flex-direction: column; gap: 2px; }
.sidebar-divider { height: 1px; background: rgba(var(--v-border-color), var(--v-border-opacity)); margin: 8px 0; }
.sidebar-label {
    font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; opacity: 0.35; padding: 8px 12px 4px;
}
.sidebar-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px; border-radius: 10px; text-decoration: none;
    color: inherit; opacity: 0.65; font-size: 0.82rem; font-weight: 500;
    transition: all 0.12s ease; white-space: nowrap;
}
.sidebar-item:hover { opacity: 1; background: rgba(var(--v-theme-on-surface), 0.05); }
.sidebar-item.active {
    opacity: 1; background: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary)); font-weight: 600;
    border-inline-start: 3px solid rgb(var(--v-theme-primary));
    padding-inline-start: 9px;
}
.sidebar-item-text { overflow: hidden; text-overflow: ellipsis; }

/* Overlay */
.sidebar-overlay {
    position: fixed; top: 52px; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 999;
}

/* Main Content */
.admin-main {
    margin-top: 52px;
    margin-left: 0;
    padding: 24px;
    min-height: calc(100vh - 52px);
    transition: margin-left 0.2s ease;
}
.admin-main.sidebar-open { margin-left: 240px; }
.admin-main.sidebar-rail { margin-left: 64px; }

/* RTL */
.rtl .admin-sidebar { left: auto; right: 0; border-right: none; border-left: 1px solid rgba(var(--v-border-color), var(--v-border-opacity)); }
.rtl .admin-sidebar.collapsed { transform: translateX(100%); }
.rtl .admin-main.sidebar-open { margin-left: 0; margin-right: 240px; }
.rtl .admin-main.sidebar-rail { margin-left: 0; margin-right: 64px; }

/* Mobile */
@media (max-width: 1023px) {
    .admin-sidebar { transform: translateX(-100%); z-index: 1002; }
    .admin-sidebar:not(.collapsed) { transform: translateX(0); }
    .admin-main { margin-left: 0 !important; margin-right: 0 !important; padding: 16px; }
    .rtl .admin-sidebar { transform: translateX(100%); }
    .rtl .admin-sidebar:not(.collapsed) { transform: translateX(0); }
}

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.12s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Scrollbar */
.admin-sidebar::-webkit-scrollbar { width: 3px; }
.admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
</style>
