<template>
    <v-app :theme="store.theme" :class="{ 'rtl': store.isRTL }">
        <template v-if="!isAdminRoute">
            <Navbar @toggle-drawer="drawer = !drawer" />

            <!-- Mobile Drawer -->
            <v-navigation-drawer v-model="drawer" temporary location="right" width="280">
                <v-list nav density="compact" class="pa-3">
                    <v-list-item to="/" prepend-icon="mdi-home" :title="$t('publicNav.home')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/all-services" prepend-icon="mdi-view-grid" :title="$t('publicNav.services')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/about" prepend-icon="mdi-information" :title="$t('publicNav.aboutUs')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/faq" prepend-icon="mdi-help-circle" :title="$t('publicNav.faq')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/contact-us" prepend-icon="mdi-email" :title="$t('publicNav.contactUs')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/privacy-policy" prepend-icon="mdi-shield-check" :title="$t('publicNav.privacy')" @click="drawer = false"></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-item href="/login" prepend-icon="mdi-login" :title="$t('publicNav.signIn')"></v-list-item>
                    <v-list-item href="/register" prepend-icon="mdi-account-plus" :title="$t('publicNav.signUp')"></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-subheader class="text-caption">{{ $t('publicNav.language') }}</v-list-subheader>
                    <v-list-item v-for="lang in store.languages" :key="lang.code" @click="changeLanguage(lang.code)" :active="store.locale === lang.code" :title="lang.native_name" density="compact"></v-list-item>
                </v-list>
            </v-navigation-drawer>

            <!-- Content -->
            <v-main style="padding:0;">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </v-main>

            <Footer />
        </template>

        <!-- Admin -->
        <router-view v-else v-slot="{ Component }">
            <component :is="Component" />
        </router-view>
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore } from './stores/app'
import { loadLocaleMessages } from './plugins/i18n'
import Navbar from './components/public/Navbar.vue'
import Footer from './components/public/Footer.vue'
import axios from 'axios'

const { locale } = useI18n()
const route = useRoute()
const store = useAppStore()
const drawer = ref(false)

watch(() => store.locale, (n) => {
    locale.value = n
    document.documentElement.dir = store.isRTL ? 'rtl' : 'ltr'
    document.documentElement.lang = n
}, { immediate: true })

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

const changeLanguage = async (lang) => {
    localStorage.setItem('locale', lang)
    await loadLocaleMessages(lang)
    await store.setLocale(lang)
    try { await axios.post('/api/set-locale', { locale: lang }) } catch {}
    if (isAdminRoute.value) { window.location.reload() } else { window.location.href = `/lang/${lang}` }
}

onMounted(async () => {
    await store.fetchLanguages()
    await loadLocaleMessages(store.locale)
    if (!isAdminRoute.value) { store.fetchServices(); store.fetchStats() }
})
</script>
