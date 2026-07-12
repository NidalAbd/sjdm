<template>
    <v-app :theme="store.theme">
        <!-- Sidebar -->
        <v-navigation-drawer v-model="drawer" :permanent="mdAndUp" :temporary="!mdAndUp" width="260">
            <div class="admin-brand">
                <img src="/images/logo.png" alt="SMM Panel" width="28" height="28">
                <span>SMM Panel</span>
            </div>

            <v-list nav density="compact" class="pa-2">
                <v-list-item to="/admin/dashboard" prepend-icon="mdi-view-dashboard" title="Dashboard"></v-list-item>
                <v-divider class="my-2"></v-divider>

                <template v-for="item in visibleMenu" :key="item.text">
                    <v-list-group v-if="item.children" :value="item.text">
                        <template v-slot:activator="{ props }">
                            <v-list-item v-bind="props" :prepend-icon="item.icon" :title="item.text"></v-list-item>
                        </template>
                        <v-list-item
                            v-for="child in visibleChildren(item)"
                            :key="child.text"
                            :to="child.to"
                            :prepend-icon="child.icon"
                            :title="child.text"
                            density="compact"
                        ></v-list-item>
                    </v-list-group>

                    <v-list-item v-else :to="item.to" :prepend-icon="item.icon" :title="item.text"></v-list-item>
                </template>

                <v-divider class="my-2"></v-divider>
                <v-list-item href="/" target="_blank" prepend-icon="mdi-open-in-new" title="View Site"></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <!-- Topbar -->
        <v-app-bar flat density="comfortable" border>
            <v-app-bar-nav-icon @click="drawer = !drawer" v-if="!mdAndUp"></v-app-bar-nav-icon>

            <v-spacer></v-spacer>

            <!-- Language -->
            <v-menu max-height="350">
                <template v-slot:activator="{ props }">
                    <button v-bind="props" class="icon-btn icon-btn-labeled">
                        <v-icon size="16">mdi-translate</v-icon>
                        <span class="d-none d-sm-inline">{{ currentLanguageName }}</span>
                    </button>
                </template>
                <v-list density="compact">
                    <v-list-item v-for="lang in store.languages" :key="lang.code" @click="changeLanguage(lang.code)" :active="store.locale === lang.code" density="compact">
                        <v-list-item-title class="text-body-2">{{ lang.native_name }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <!-- Theme -->
            <button class="icon-btn" @click="store.toggleTheme">
                <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
            </button>

            <!-- Notifications -->
            <v-menu :close-on-content-click="false" max-width="340">
                <template v-slot:activator="{ props }">
                    <button v-bind="props" class="icon-btn" style="position:relative;">
                        <v-icon size="18">mdi-bell</v-icon>
                        <v-badge v-if="unreadCount > 0" :content="unreadCount" color="error" floating></v-badge>
                    </button>
                </template>
                <v-list density="compact" max-height="360" style="overflow-y:auto;">
                    <v-list-item v-if="!notifications.length" title="No new notifications" density="compact"></v-list-item>
                    <v-list-item
                        v-for="n in notifications"
                        :key="n.id"
                        :href="n.url"
                        :title="n.title"
                        :subtitle="n.message"
                        density="compact"
                    ></v-list-item>
                    <v-divider v-if="notifications.length"></v-divider>
                    <v-list-item v-if="notifications.length" to="/admin/notifications" title="View all" density="compact" class="text-primary"></v-list-item>
                </v-list>
            </v-menu>

            <!-- User menu -->
            <v-menu>
                <template v-slot:activator="{ props }">
                    <button v-bind="props" class="icon-btn" style="margin-left:4px;">
                        <v-avatar size="30" color="primary">
                            <v-img v-if="authStore.userAvatar" :src="authStore.userAvatar" :alt="authStore.userName"></v-img>
                            <span v-else class="text-caption">{{ authStore.userName.charAt(0) }}</span>
                        </v-avatar>
                    </button>
                </template>
                <v-list density="compact">
                    <v-list-item :title="authStore.userName" :subtitle="authStore.userEmail" density="compact"></v-list-item>
                    <v-divider></v-divider>
                    <v-list-item to="/admin/profile" prepend-icon="mdi-account" title="Profile" density="compact"></v-list-item>
                    <v-list-item @click="handleLogout" prepend-icon="mdi-logout" title="Logout" density="compact"></v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <v-main>
            <div class="admin-content">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </div>
        </v-main>

        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top right">
            {{ snackbar.text }}
        </v-snackbar>
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, provide } from 'vue'
import { useDisplay } from 'vuetify'
import axios from 'axios'
import { useAppStore } from './stores/app'
import { useAuthStore } from './stores/auth'
import { loadLocaleMessages } from './plugins/i18n'
import { updateThemeColors } from './plugins/vuetify'
import { adminMenu } from './config/adminMenu'

const store = useAppStore()
const authStore = useAuthStore()
const { mdAndUp } = useDisplay()

const drawer = ref(mdAndUp.value)
const snackbar = ref({ show: false, text: '', color: 'success' })
const notifications = ref([])
const unreadCount = ref(0)

const showSnackbar = (text, color = 'success') => {
    snackbar.value = { show: true, text, color }
}
provide('showSnackbar', showSnackbar)

const currentLanguageName = computed(() => {
    const lang = store.languages.find(l => l.code === store.locale)
    return lang ? lang.native_name : 'EN'
})

const visibleMenu = computed(() => adminMenu.filter(item => !item.can || authStore.hasPermission(item.can)))
const visibleChildren = (item) => item.children.filter(child => !child.can || authStore.hasPermission(child.can))

const changeLanguage = async (lang) => {
    localStorage.setItem('locale', lang)
    await loadLocaleMessages(lang)
    await store.setLocale(lang)
    try { await axios.post('/api/set-locale', { locale: lang }) } catch {}
    window.location.reload()
}

const fetchNotifications = async () => {
    try {
        const [latestRes, countRes] = await Promise.all([
            axios.get('/notifications/latest'),
            axios.get('/notifications/unread-count'),
        ])
        notifications.value = latestRes.data || []
        unreadCount.value = countRes.data.count || 0
    } catch (error) {
        console.error('Error fetching notifications:', error)
    }
}

const handleLogout = async () => {
    await authStore.logout()
    window.location.href = '/login'
}

onMounted(async () => {
    await authStore.fetchUser()
    await store.fetchLanguages()
    updateThemeColors(store.themeColor)
    fetchNotifications()
})
</script>

<style scoped>
.admin-brand { display: flex; align-items: center; gap: 8px; padding: 16px; font-weight: 800; font-size: 1rem; }
.admin-brand img { border-radius: 6px; }
.admin-content { padding: 24px 28px; }
@media (max-width: 600px) { .admin-content { padding: 16px; } }
</style>
