<template>
    <v-app :theme="store.theme" class="admin-layout" :class="{ 'rtl': store.isRTL }">
        <!-- App Bar -->
        <v-app-bar elevation="0" color="surface" border="b" density="compact">
            <v-app-bar-nav-icon @click="drawer = !drawer" size="small"></v-app-bar-nav-icon>

            <v-toolbar-title class="text-body-1 font-weight-bold d-none d-sm-block">
                {{ $t('header.panel') }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <!-- Balance -->
            <v-chip color="success" variant="tonal" size="small" class="mr-1">
                <v-icon start size="14">mdi-wallet</v-icon>
                ${{ formatBalance(user?.balance) }}
            </v-chip>

            <!-- Theme Color -->
            <v-menu :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                    <v-btn icon variant="text" v-bind="props" size="small">
                        <v-icon size="18" :color="store.primaryColor">mdi-palette</v-icon>
                    </v-btn>
                </template>
                <v-card width="180" class="pa-3">
                    <div class="text-caption text-medium-emphasis mb-2">{{ $t('header.themeColor') }}</div>
                    <div class="d-flex flex-wrap ga-1">
                        <v-btn
                            v-for="(color, key) in themeColors"
                            :key="key"
                            :color="color.primary"
                            icon size="24"
                            :variant="store.themeColor === key ? 'flat' : 'outlined'"
                            @click="selectThemeColor(key)"
                        >
                            <v-icon v-if="store.themeColor === key" size="12">mdi-check</v-icon>
                        </v-btn>
                    </div>
                </v-card>
            </v-menu>

            <!-- Notifications -->
            <v-btn icon variant="text" to="/admin/notifications" size="small">
                <v-badge :content="unreadNotifications" color="error" :model-value="unreadNotifications > 0" dot>
                    <v-icon size="18">mdi-bell-outline</v-icon>
                </v-badge>
            </v-btn>

            <!-- Language -->
            <v-menu max-height="300">
                <template v-slot:activator="{ props }">
                    <v-btn icon variant="text" v-bind="props" size="small">
                        <v-icon size="18">mdi-translate</v-icon>
                    </v-btn>
                </template>
                <v-list density="compact" nav>
                    <v-list-item
                        v-for="lang in store.languages"
                        :key="lang.code"
                        @click="changeLanguage(lang.code)"
                        :active="store.locale === lang.code"
                        density="compact"
                    >
                        <v-list-item-title class="text-body-2">{{ lang.native_name }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <!-- Theme Toggle -->
            <v-btn icon variant="text" @click="store.toggleTheme" size="small">
                <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
            </v-btn>

            <!-- User Menu -->
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" variant="text" class="text-none ml-1" size="small">
                        <v-avatar size="28" color="primary">
                            <v-img v-if="user?.avatar" :src="user.avatar"></v-img>
                            <span v-else class="text-white text-caption">{{ user?.name?.charAt(0) || 'U' }}</span>
                        </v-avatar>
                        <span class="ml-2 d-none d-md-inline text-body-2">{{ user?.name || 'User' }}</span>
                    </v-btn>
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
        </v-app-bar>

        <!-- Sidebar -->
        <v-navigation-drawer v-model="drawer" :rail="rail" permanent @click="rail = false" color="surface" border="e">
            <!-- Brand -->
            <div class="pa-3 d-flex align-center" style="min-height: 48px;">
                <v-avatar size="28" rounded="lg" color="primary">
                    <v-img src="/images/logo.png"></v-img>
                </v-avatar>
                <template v-if="!rail">
                    <span class="ml-2 text-subtitle-2 font-weight-bold">SJDM Panel</span>
                    <v-spacer></v-spacer>
                    <v-btn icon variant="text" size="x-small" @click.stop="rail = true">
                        <v-icon size="16">mdi-chevron-left</v-icon>
                    </v-btn>
                </template>
            </div>

            <v-divider></v-divider>

            <!-- Nav Items -->
            <v-list density="compact" nav class="pa-2">
                <v-list-item
                    v-for="item in mainMenuItems"
                    :key="item.key"
                    :to="item.to"
                    :prepend-icon="item.icon"
                    :title="$t(item.titleKey)"
                    :value="item.key"
                    color="primary"
                    rounded="lg"
                    density="compact"
                    class="mb-1"
                ></v-list-item>

                <!-- Admin Section -->
                <template v-if="isAdmin">
                    <v-divider class="my-2"></v-divider>
                    <v-list-subheader v-if="!rail" class="text-uppercase text-caption" style="font-size: 0.65rem !important; letter-spacing: 0.08em;">
                        {{ $t('nav.admin') }}
                    </v-list-subheader>

                    <v-list-item
                        v-for="item in adminMenuItems"
                        :key="item.key"
                        :to="item.to"
                        :prepend-icon="item.icon"
                        :title="$t(item.titleKey)"
                        color="primary"
                        rounded="lg"
                        density="compact"
                        class="mb-1"
                    ></v-list-item>
                </template>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <v-container fluid class="pa-4 pa-md-6">
                <!-- Breadcrumbs -->
                <v-breadcrumbs :items="breadcrumbs" density="compact" class="pa-0 mb-4 text-caption">
                    <template v-slot:prepend>
                        <v-icon icon="mdi-home" size="14"></v-icon>
                    </template>
                </v-breadcrumbs>

                <!-- Page Content -->
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </v-container>
        </v-main>

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
import { ref, computed, onMounted, watchEffect, watch, provide } from 'vue'
import { useRoute } from 'vue-router'
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

const drawer = ref(true)
const rail = ref(false)
const notifications = ref([])
const snackbar = ref({ show: false, text: '', color: 'success' })

// RTL support - dynamic
const isRtl = computed(() => store.isRTL)

watch(() => store.locale, (newLocale) => {
    locale.value = newLocale
    document.documentElement.dir = store.isRTL ? 'rtl' : 'ltr'
    document.documentElement.lang = newLocale
}, { immediate: true })

const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)
const unreadNotifications = computed(() => notifications.value.filter(n => !n.read_at).length)

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

const breadcrumbs = computed(() => {
    const items = [{ title: t('common.home'), to: '/admin/dashboard', disabled: false }]
    if (route.meta?.breadcrumb) {
        items.push({ title: route.meta.breadcrumb, disabled: true })
    }
    return items
})

const formatBalance = (balance) => (parseFloat(balance) || 0).toFixed(2)

const selectThemeColor = (colorKey) => {
    store.setThemeColor(colorKey)
    updateThemeColors(colorKey)
}

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
    try {
        const response = await axios.get('/notifications/latest')
        notifications.value = response.data
    } catch {}
}

const showSnackbar = (text, color = 'success') => {
    snackbar.value = { show: true, text, color }
}

provide('showSnackbar', showSnackbar)

onMounted(async () => {
    await authStore.fetchUser()
    await store.fetchLanguages()
    fetchNotifications()
    updateThemeColors(store.themeColor)
    setInterval(fetchNotifications, 60000)
})
</script>
