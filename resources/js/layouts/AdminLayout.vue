<template>
    <v-app :theme="store.theme" :class="{ 'rtl': store.isRTL }">
        <!-- Navbar (AdminLTE style - top) -->
        <nav class="main-header">
            <div class="nav-left">
                <button class="nav-link" @click="toggleSidebar">
                    <v-icon size="20">mdi-menu</v-icon>
                </button>
            </div>
            <div class="nav-right">
                <div class="balance-badge">
                    <v-icon size="14" class="mr-1">mdi-wallet</v-icon>
                    ${{ formatBalance(user?.balance) }}
                </div>
                <button class="nav-link" @click="store.toggleTheme">
                    <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                </button>
                <v-menu max-height="300">
                    <template v-slot:activator="{ props }">
                        <button class="nav-link" v-bind="props">
                            <v-icon size="18">mdi-translate</v-icon>
                        </button>
                    </template>
                    <v-list density="compact" nav>
                        <v-list-item v-for="lang in store.languages" :key="lang.code" @click="changeLanguage(lang.code)" :active="store.locale === lang.code" density="compact">
                            <v-list-item-title class="text-body-2">{{ lang.native_name }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <button class="nav-link" @click="$router.push('/admin/notifications')">
                    <v-badge :content="unreadNotifications" color="error" :model-value="unreadNotifications > 0" dot>
                        <v-icon size="18">mdi-bell-outline</v-icon>
                    </v-badge>
                </button>
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <button class="nav-link user-link" v-bind="props">
                            <v-avatar size="28" color="primary">
                                <v-img v-if="user?.avatar" :src="user.avatar"></v-img>
                                <span v-else class="text-white" style="font-size:11px;">{{ user?.name?.charAt(0) || 'U' }}</span>
                            </v-avatar>
                            <span class="user-name">{{ user?.name || 'User' }}</span>
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
        </nav>

        <!-- Sidebar (AdminLTE style) -->
        <aside class="main-sidebar" :class="{ 'open': sidebarOpen }">
            <!-- Brand -->
            <div class="brand-link">
                <v-avatar size="33" rounded="lg">
                    <v-img src="/images/logo.png"></v-img>
                </v-avatar>
                <span class="brand-text">SJDM Panel</span>
            </div>

            <!-- User Panel -->
            <div class="user-panel">
                <v-avatar size="36" color="primary">
                    <v-img v-if="user?.avatar" :src="user.avatar"></v-img>
                    <span v-else class="text-white" style="font-size:13px;">{{ user?.name?.charAt(0) || 'U' }}</span>
                </v-avatar>
                <div class="user-info">
                    <div class="user-info-name">{{ user?.name || 'User' }}</div>
                    <div class="user-info-role">
                        <v-icon size="8" color="success" class="mr-1">mdi-circle</v-icon>
                        {{ isAdmin ? 'Admin' : 'User' }}
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-header">MAIN</li>
                    <li v-for="item in mainMenuItems" :key="item.key">
                        <router-link :to="item.to" class="nav-item" :class="{ active: isActive(item.to) }" @click="closeMobile">
                            <v-icon size="18" class="nav-icon">{{ item.icon }}</v-icon>
                            <span>{{ $t(item.titleKey) }}</span>
                        </router-link>
                    </li>

                    <template v-if="isAdmin">
                        <li class="nav-header">{{ $t('nav.admin').toUpperCase() }}</li>
                        <li v-for="item in adminMenuItems" :key="item.key">
                            <router-link :to="item.to" class="nav-item" :class="{ active: isActive(item.to) }" @click="closeMobile">
                                <v-icon size="18" class="nav-icon">{{ item.icon }}</v-icon>
                                <span>{{ $t(item.titleKey) }}</span>
                            </router-link>
                        </li>
                    </template>
                </ul>
            </nav>
        </aside>

        <!-- Overlay -->
        <div v-if="sidebarOpen && isMobile" class="sidebar-overlay" @click="sidebarOpen = false"></div>

        <!-- Content -->
        <div class="content-wrapper" :class="{ 'sidebar-expanded': sidebarOpen && !isMobile }">
            <div class="content-inner">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </div>
        </div>

        <!-- Snackbar -->
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top right">
            {{ snackbar.text }}
        </v-snackbar>
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, provide } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '../stores/app'
import { useAuthStore } from '../stores/auth'
import { updateThemeColors } from '../plugins/vuetify'
import { loadLocaleMessages } from '../plugins/i18n'
import axios from 'axios'

const { t, locale } = useI18n()
const store = useAppStore()
const authStore = useAuthStore()
const route = useRoute()

const sidebarOpen = ref(window.innerWidth >= 992)
const isMobile = ref(window.innerWidth < 992)
const notifications = ref([])
const snackbar = ref({ show: false, text: '', color: 'success' })

const handleResize = () => {
    isMobile.value = window.innerWidth < 992
    if (!isMobile.value) sidebarOpen.value = true
}
const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value }
const closeMobile = () => { if (isMobile.value) sidebarOpen.value = false }

watch(() => store.locale, (n) => {
    locale.value = n
    document.documentElement.dir = store.isRTL ? 'rtl' : 'ltr'
    document.documentElement.lang = n
}, { immediate: true })

const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const unreadNotifications = computed(() => notifications.value.filter(n => !n.read_at).length)
const isActive = (to) => route.path === to || (to !== '/admin/dashboard' && route.path.startsWith(to + '/'))

const mainMenuItems = [
    { key: 'dashboard', titleKey: 'nav.dashboard', icon: 'mdi-view-dashboard', to: '/admin/dashboard' },
    { key: 'newOrder', titleKey: 'nav.newOrder', icon: 'mdi-cart-plus', to: '/admin/orders/create' },
    { key: 'orders', titleKey: 'nav.orders', icon: 'mdi-shopping', to: '/admin/orders' },
    { key: 'services', titleKey: 'nav.services', icon: 'mdi-cog', to: '/admin/services' },
    { key: 'addBalance', titleKey: 'nav.addBalance', icon: 'mdi-wallet-plus', to: '/admin/transactions/create' },
    { key: 'transactions', titleKey: 'nav.transactions', icon: 'mdi-swap-horizontal', to: '/admin/transactions' },
    { key: 'support', titleKey: 'nav.support', icon: 'mdi-headset', to: '/admin/support' },
    { key: 'notifications', titleKey: 'nav.notifications', icon: 'mdi-bell', to: '/admin/notifications' },
    { key: 'referrals', titleKey: 'nav.referrals', icon: 'mdi-account-group', to: '/admin/referrals' },
    { key: 'points', titleKey: 'nav.points', icon: 'mdi-star', to: '/admin/points' },
]

const adminMenuItems = [
    { key: 'users', titleKey: 'nav.users', icon: 'mdi-account-multiple', to: '/admin/users' },
    { key: 'roles', titleKey: 'nav.roles', icon: 'mdi-shield-account', to: '/admin/roles' },
    { key: 'permissions', titleKey: 'nav.permissions', icon: 'mdi-lock', to: '/admin/permissions' },
    { key: 'paymentMethods', titleKey: 'nav.paymentMethods', icon: 'mdi-credit-card', to: '/admin/payment-methods' },
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
/* ========== NAVBAR ========== */
.main-header {
    position: fixed; top: 0; left: 0; right: 0; height: 50px; z-index: 1030;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 12px;
    background: rgb(var(--v-theme-surface));
    border-bottom: 1px solid rgba(var(--v-border-color), 0.12);
}
.nav-left, .nav-right { display: flex; align-items: center; gap: 2px; }
.nav-link {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 10px; border: none; background: none;
    color: rgb(var(--v-theme-on-surface)); cursor: pointer;
    border-radius: 6px; transition: background 0.15s;
}
.nav-link:hover { background: rgba(var(--v-theme-on-surface), 0.06); }
.balance-badge {
    display: flex; align-items: center;
    font-size: 0.78rem; font-weight: 700;
    padding: 5px 12px; border-radius: 6px;
    background: rgba(var(--v-theme-success), 0.1);
    color: rgb(var(--v-theme-success));
}
.user-link { padding: 4px 8px 4px 4px; }
.user-name { font-size: 0.8rem; font-weight: 600; display: none; }
@media(min-width:768px) { .user-name { display: inline; } }

/* ========== SIDEBAR ========== */
.main-sidebar {
    position: fixed; top: 50px; left: 0; bottom: 0; width: 250px;
    background: #343a40;
    overflow-y: auto; overflow-x: hidden;
    z-index: 1020;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}
.main-sidebar.open { transform: translateX(0); }
.v-theme--light .main-sidebar { background: #fff; border-right: 1px solid #dee2e6; }

/* Brand */
.brand-link {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    background: rgba(0,0,0,0.15);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.v-theme--light .brand-link { background: rgba(0,0,0,0.03); border-bottom: 1px solid #dee2e6; }
.brand-text { font-size: 1rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.v-theme--light .brand-text { color: #343a40; }

/* User Panel */
.user-panel {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.v-theme--light .user-panel { border-bottom: 1px solid #dee2e6; }
.user-info-name { font-size: 0.82rem; font-weight: 600; color: #fff; }
.user-info-role { font-size: 0.68rem; color: rgba(255,255,255,0.5); }
.v-theme--light .user-info-name { color: #343a40; }
.v-theme--light .user-info-role { color: #6c757d; }

/* Nav */
.sidebar-nav { padding: 6px 8px; }
.nav-list { list-style: none; padding: 0; margin: 0; }
.nav-header {
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em;
    color: rgba(255,255,255,0.35); padding: 14px 12px 6px;
    text-transform: uppercase;
}
.v-theme--light .nav-header { color: #868e96; }

.nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 12px; margin-bottom: 2px;
    border-radius: 4px; text-decoration: none;
    color: rgba(255,255,255,0.75); font-size: 0.84rem;
    transition: all 0.15s;
}
.v-theme--light .nav-item { color: #343a40; }
.nav-item:hover { color: #fff; background: rgba(255,255,255,0.08); }
.v-theme--light .nav-item:hover { color: #343a40; background: rgba(0,0,0,0.04); }
.nav-item.active {
    color: #fff !important;
    background: rgb(var(--v-theme-primary)) !important;
}
.nav-icon { flex-shrink: 0; opacity: 0.7; }
.nav-item:hover .nav-icon, .nav-item.active .nav-icon { opacity: 1; }
.nav-item.active .nav-icon { color: #fff !important; }

/* Overlay */
.sidebar-overlay {
    position: fixed; top: 50px; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 1010;
}

/* Content */
.content-wrapper {
    margin-top: 50px; min-height: calc(100vh - 50px);
    transition: margin-left 0.3s ease;
}
.content-wrapper.sidebar-expanded { margin-left: 250px; }
.content-inner { padding: 20px; }

/* RTL */
.rtl .main-sidebar { left: auto; right: 0; transform: translateX(100%); }
.rtl .main-sidebar.open { transform: translateX(0); }
.rtl .content-wrapper.sidebar-expanded { margin-left: 0; margin-right: 250px; }

/* Mobile */
@media (max-width: 991px) {
    .main-sidebar { z-index: 1040; }
    .content-wrapper { margin-left: 0 !important; margin-right: 0 !important; }
    .content-inner { padding: 12px; }
}

/* Scrollbar */
.main-sidebar::-webkit-scrollbar { width: 4px; }
.main-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
.v-theme--light .main-sidebar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.12s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
