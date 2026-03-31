<template>
    <v-app :theme="store.theme" class="admin-layout" :class="{ 'rtl': isRtl }">
        <!-- App Bar -->
        <v-app-bar elevation="0" color="surface" border>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-toolbar-title class="text-h6 font-weight-bold">
                {{ $t('header.panel') }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <!-- Balance Display -->
            <v-chip color="success" variant="tonal" class="mr-2 d-none d-sm-flex">
                <v-icon start size="small">mdi-wallet</v-icon>
                ${{ formatBalance(user?.balance) }}
            </v-chip>

            <!-- Theme Color Selector -->
            <v-menu :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                    <v-btn icon variant="text" v-bind="props">
                        <v-icon :color="store.primaryColor">mdi-palette</v-icon>
                    </v-btn>
                </template>
                <v-card width="200">
                    <v-card-text>
                        <div class="text-caption text-medium-emphasis mb-2">{{ $t('header.themeColor') }}</div>
                        <div class="d-flex flex-wrap ga-2">
                            <v-btn
                                v-for="(color, key) in themeColors"
                                :key="key"
                                :color="color.primary"
                                icon
                                size="28"
                                :variant="store.themeColor === key ? 'flat' : 'outlined'"
                                @click="selectThemeColor(key)"
                            >
                                <v-icon v-if="store.themeColor === key" size="14">mdi-check</v-icon>
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-menu>

            <!-- Notifications -->
            <v-btn icon variant="text" to="/admin/notifications">
                <v-badge :content="unreadNotifications" color="error" :model-value="unreadNotifications > 0">
                    <v-icon>mdi-bell-outline</v-icon>
                </v-badge>
            </v-btn>

            <!-- Language Selector - Dynamic -->
            <v-menu max-height="400">
                <template v-slot:activator="{ props }">
                    <v-btn icon variant="text" v-bind="props">
                        <v-icon>mdi-translate</v-icon>
                    </v-btn>
                </template>
                <v-list density="compact" nav>
                    <v-list-item
                        v-for="lang in store.languages"
                        :key="lang.code"
                        @click="changeLanguage(lang.code)"
                        :active="store.locale === lang.code"
                    >
                        <v-list-item-title>{{ lang.native_name }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <!-- Theme Toggle -->
            <v-btn icon variant="text" @click="store.toggleTheme">
                <v-icon>{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
            </v-btn>

            <!-- User Menu -->
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" variant="text" class="text-none ml-1">
                        <v-avatar size="32" color="primary">
                            <v-img v-if="user?.avatar" :src="user.avatar"></v-img>
                            <span v-else class="text-white text-body-2">{{ user?.name?.charAt(0) || 'U' }}</span>
                        </v-avatar>
                        <span class="ml-2 d-none d-md-inline">{{ user?.name || 'User' }}</span>
                        <v-icon end size="small">mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list density="compact" nav>
                    <v-list-item to="/admin/profile" prepend-icon="mdi-account-outline">
                        <v-list-item-title>{{ $t('nav.profile') }}</v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/transactions/create" prepend-icon="mdi-wallet-plus-outline">
                        <v-list-item-title>{{ $t('nav.addBalance') }}</v-list-item-title>
                    </v-list-item>
                    <v-divider class="my-1"></v-divider>
                    <v-list-item @click="logout" prepend-icon="mdi-logout" base-color="error">
                        <v-list-item-title>{{ $t('nav.logout') }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Navigation Drawer -->
        <v-navigation-drawer v-model="drawer" :rail="rail" permanent @click="rail = false">
            <!-- Brand -->
            <div class="pa-3 d-flex align-center" style="min-height: 56px;">
                <v-avatar size="32" rounded="lg" color="primary">
                    <v-img src="/images/favicon-96x96.png"></v-img>
                </v-avatar>
                <template v-if="!rail">
                    <span class="ml-3 text-subtitle-1 font-weight-bold">SJDM</span>
                    <v-spacer></v-spacer>
                    <v-btn icon variant="text" size="small" @click.stop="rail = true">
                        <v-icon size="small">mdi-chevron-left</v-icon>
                    </v-btn>
                </template>
            </div>

            <v-divider></v-divider>

            <!-- Navigation -->
            <v-list density="compact" nav>
                <v-list-item
                    v-for="item in mainMenuItems"
                    :key="item.key"
                    :to="item.to"
                    :prepend-icon="item.icon"
                    :title="$t(item.titleKey)"
                    :value="item.key"
                    color="primary"
                    rounded="lg"
                ></v-list-item>

                <!-- Admin Section -->
                <template v-if="isAdmin">
                    <v-divider class="my-2"></v-divider>
                    <v-list-subheader v-if="!rail">{{ $t('nav.admin') }}</v-list-subheader>

                    <v-list-item
                        v-for="item in adminMenuItems"
                        :key="item.key"
                        :to="item.to"
                        :prepend-icon="item.icon"
                        :title="$t(item.titleKey)"
                        color="primary"
                        rounded="lg"
                    ></v-list-item>
                </template>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <!-- Breadcrumbs -->
            <v-container fluid class="py-2">
                <v-breadcrumbs :items="breadcrumbs" density="compact" class="pa-0">
                    <template v-slot:prepend>
                        <v-icon icon="mdi-home" size="small"></v-icon>
                    </template>
                </v-breadcrumbs>
            </v-container>

            <!-- Page Content -->
            <v-container fluid>
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </v-container>
        </v-main>

        <!-- Snackbar -->
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top right">
            {{ snackbar.text }}
            <template v-slot:actions>
                <v-btn variant="text" @click="snackbar.show = false">{{ $t('common.close') }}</v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, watchEffect, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore, themeColors } from '../stores/app'
import { useAuthStore } from '../stores/auth'
import { updateThemeColors } from '../plugins/vuetify'
import axios from 'axios'

const { t, locale } = useI18n()
const store = useAppStore()
const authStore = useAuthStore()
const route = useRoute()

const drawer = ref(true)
const rail = ref(false)
const notifications = ref([])
const snackbar = ref({ show: false, text: '', color: 'success' })

// RTL support - dynamic from API
const isRtl = computed(() => store.isRTL)

// Sync i18n locale with store
watch(() => store.locale, (newLocale) => {
    locale.value = newLocale
    document.documentElement.dir = store.isRTL ? 'rtl' : 'ltr'
    document.documentElement.lang = newLocale
}, { immediate: true })

// User data
const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)

// Unread notifications count
const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read_at).length
})

// Menu items with translation keys
const mainMenuItems = [
    { key: 'dashboard', titleKey: 'nav.dashboard', icon: 'mdi-view-dashboard-outline', to: '/admin/dashboard' },
    { key: 'newOrder', titleKey: 'nav.newOrder', icon: 'mdi-cart-plus', to: '/admin/orders/create' },
    { key: 'orders', titleKey: 'nav.orders', icon: 'mdi-shopping-outline', to: '/admin/orders' },
    { key: 'services', titleKey: 'nav.services', icon: 'mdi-cog-outline', to: '/admin/services' },
    { key: 'addBalance', titleKey: 'nav.addBalance', icon: 'mdi-wallet-plus-outline', to: '/admin/transactions/create' },
    { key: 'transactions', titleKey: 'nav.transactions', icon: 'mdi-cash-multiple', to: '/admin/transactions' },
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

// Breadcrumbs
const breadcrumbs = computed(() => {
    const items = [{ title: t('common.home'), to: '/admin/dashboard', disabled: false }]
    if (route.meta?.breadcrumb) {
        items.push({ title: route.meta.breadcrumb, disabled: true })
    }
    return items
})

// Methods
const formatBalance = (balance) => {
    return (parseFloat(balance) || 0).toFixed(2)
}

const selectThemeColor = (colorKey) => {
    store.setThemeColor(colorKey)
    updateThemeColors(colorKey)
}

const changeLanguage = async (lang) => {
    // Save to localStorage directly
    localStorage.setItem('locale', lang)
    store.setLocale(lang)

    // Also update Laravel session by calling the language endpoint
    try {
        await axios.post('/api/set-locale', { locale: lang })
    } catch (error) {
        console.error('Failed to sync locale with server:', error)
    }

    // Reload to apply new language throughout the app
    window.location.reload()
}

const logout = async () => {
    try {
        await axios.post('/logout')
        authStore.clearUser()
        window.location.href = '/login'
    } catch (error) {
        window.location.href = '/login'
    }
}

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/notifications/latest')
        notifications.value = response.data
    } catch (error) {
        console.error('Error fetching notifications:', error)
    }
}

const showSnackbar = (text, color = 'success') => {
    snackbar.value = { show: true, text, color }
}

// Lifecycle
onMounted(async () => {
    await authStore.fetchUser()
    fetchNotifications()
    updateThemeColors(store.themeColor)
    setInterval(fetchNotifications, 30000)
})

watchEffect(() => {
    console.log('AdminLayout: isAdmin =', isAdmin.value)
})

// Provide snackbar method to child components
import { provide } from 'vue'
provide('showSnackbar', showSnackbar)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* RTL Support */
.rtl {
    direction: rtl;
}

.rtl .v-navigation-drawer {
    left: auto;
    right: 0;
}
</style>
