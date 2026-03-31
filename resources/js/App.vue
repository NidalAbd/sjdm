<template>
    <v-app :theme="store.theme" :class="{ 'rtl': store.isRTL }">
        <!-- Only show public header/nav when NOT on admin routes -->
        <template v-if="!isAdminRoute">
            <!-- Navigation -->
            <v-app-bar elevation="0" class="px-4">
                <v-app-bar-title>
                    <router-link to="/" class="d-flex align-center text-decoration-none">
                        <v-avatar size="40" class="mr-3">
                            <v-img src="/images/logo.png" alt="SMM Panel"></v-img>
                        </v-avatar>
                        <span class="text-h6 font-weight-bold text-primary">SMM Panel</span>
                    </router-link>
                </v-app-bar-title>

                <template v-slot:append>
                    <!-- Desktop Navigation -->
                    <div class="d-none d-lg-flex align-center ga-1">
                        <v-btn variant="text" to="/" size="small">{{ $t('publicNav.home') }}</v-btn>
                        <v-btn variant="text" to="/all-services" size="small">{{ $t('publicNav.services') }}</v-btn>
                        <v-btn variant="text" href="https://smm-followerss.com/" target="_blank" size="small">
                            {{ $t('publicNav.smmPanel') }}
                            <v-icon end size="small">mdi-open-in-new</v-icon>
                        </v-btn>
                        <v-btn variant="text" to="/about" size="small">{{ $t('publicNav.aboutUs') }}</v-btn>
                        <v-btn variant="text" to="/contact-us" size="small">{{ $t('publicNav.contactUs') }}</v-btn>
                        <v-btn variant="text" to="/faq" size="small">{{ $t('publicNav.faq') }}</v-btn>
                        <v-btn variant="text" to="/privacy-policy" size="small">{{ $t('publicNav.privacy') }}</v-btn>

                        <v-divider vertical class="mx-2" length="24"></v-divider>

                        <!-- Language Selector - Dynamic from API -->
                        <v-menu max-height="400">
                            <template v-slot:activator="{ props }">
                                <v-btn variant="text" v-bind="props" size="small">
                                    <v-icon start>mdi-translate</v-icon>
                                    {{ currentLanguageName }}
                                    <v-icon end>mdi-chevron-down</v-icon>
                                </v-btn>
                            </template>
                            <v-list density="compact">
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

                        <v-btn variant="text" href="/login" size="small">
                            <v-icon start>mdi-login</v-icon>
                            {{ $t('publicNav.signIn') }}
                        </v-btn>
                        <v-btn variant="flat" color="primary" href="/register" size="small">
                            <v-icon start>mdi-account-plus</v-icon>
                            {{ $t('publicNav.signUp') }}
                        </v-btn>

                        <v-btn icon @click="store.toggleTheme" size="small" class="ml-2">
                            <v-icon>{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                        </v-btn>
                    </div>

                    <!-- Mobile Menu -->
                    <v-app-bar-nav-icon class="d-lg-none" @click="drawer = !drawer"></v-app-bar-nav-icon>
                </template>
            </v-app-bar>

            <!-- Mobile Drawer -->
            <v-navigation-drawer v-model="drawer" temporary location="right">
                <v-list nav>
                    <v-list-item to="/" prepend-icon="mdi-home" :title="$t('publicNav.home')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/all-services" prepend-icon="mdi-view-grid" :title="$t('publicNav.services')" @click="drawer = false"></v-list-item>
                    <v-list-item href="https://smm-followerss.com/" target="_blank" prepend-icon="mdi-cart" :title="$t('publicNav.smmPanel')"></v-list-item>
                    <v-list-item to="/about" prepend-icon="mdi-information" :title="$t('publicNav.aboutUs')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/contact-us" prepend-icon="mdi-email" :title="$t('publicNav.contactUs')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/faq" prepend-icon="mdi-help-circle" :title="$t('publicNav.faq')" @click="drawer = false"></v-list-item>
                    <v-list-item to="/privacy-policy" prepend-icon="mdi-shield-check" :title="$t('publicNav.privacy')" @click="drawer = false"></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-item href="/login" prepend-icon="mdi-login" :title="$t('publicNav.signIn')"></v-list-item>
                    <v-list-item href="/register" prepend-icon="mdi-account-plus" :title="$t('publicNav.signUp')"></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-subheader>{{ $t('publicNav.language') }}</v-list-subheader>
                    <v-list-item
                        v-for="lang in store.languages"
                        :key="lang.code"
                        @click="changeLanguage(lang.code)"
                        :active="store.locale === lang.code"
                        :title="lang.native_name"
                    ></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-item @click="store.toggleTheme" :prepend-icon="store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night'" :title="store.isDark ? $t('publicNav.lightMode') : $t('publicNav.darkMode')"></v-list-item>
                </v-list>
            </v-navigation-drawer>
        </template>

        <!-- Main Content -->
        <v-main v-if="!isAdminRoute">
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </v-main>

        <!-- Admin routes render directly without public layout wrapper -->
        <router-view v-else v-slot="{ Component }">
            <component :is="Component" />
        </router-view>

        <!-- Footer - only on public routes -->
        <v-footer v-if="!isAdminRoute" class="bg-surface pa-8">
            <v-container>
                <v-row>
                    <v-col cols="12" md="4">
                        <div class="d-flex align-center mb-4">
                            <v-avatar size="48" class="mr-3">
                                <v-img src="/images/logo.png" alt="SMM Panel"></v-img>
                            </v-avatar>
                            <div>
                                <div class="text-h6 font-weight-bold">SMM Panel</div>
                                <div class="text-caption text-medium-emphasis">{{ $t('footer.bestServices') }}</div>
                            </div>
                        </div>
                        <p class="text-body-2 text-medium-emphasis">
                            {{ $t('footer.description') }}
                        </p>
                    </v-col>

                    <v-col cols="6" md="2">
                        <div class="text-subtitle-1 font-weight-bold mb-3">{{ $t('footer.quickLinks') }}</div>
                        <v-list density="compact" class="bg-transparent pa-0">
                            <v-list-item to="/" class="px-0" min-height="32">{{ $t('publicNav.home') }}</v-list-item>
                            <v-list-item to="/all-services" class="px-0" min-height="32">{{ $t('publicNav.services') }}</v-list-item>
                            <v-list-item to="/faq" class="px-0" min-height="32">{{ $t('publicNav.faq') }}</v-list-item>
                            <v-list-item to="/contact-us" class="px-0" min-height="32">{{ $t('publicNav.contactUs') }}</v-list-item>
                        </v-list>
                    </v-col>

                    <v-col cols="6" md="2">
                        <div class="text-subtitle-1 font-weight-bold mb-3">{{ $t('footer.legal') }}</div>
                        <v-list density="compact" class="bg-transparent pa-0">
                            <v-list-item to="/privacy-policy" class="px-0" min-height="32">{{ $t('publicNav.privacy') }}</v-list-item>
                            <v-list-item to="/about" class="px-0" min-height="32">{{ $t('publicNav.aboutUs') }}</v-list-item>
                            <v-list-item to="/how-it-works" class="px-0" min-height="32">{{ $t('publicNav.howItWorks') }}</v-list-item>
                        </v-list>
                    </v-col>

                    <v-col cols="12" md="4">
                        <div class="text-subtitle-1 font-weight-bold mb-3">{{ $t('footer.contactUs') }}</div>
                        <div class="d-flex align-center mb-2">
                            <v-icon size="small" class="mr-2">mdi-email</v-icon>
                            <span class="text-body-2">support@smmjd.com</span>
                        </div>
                        <div class="d-flex align-center mb-4">
                            <v-icon size="small" class="mr-2">mdi-clock</v-icon>
                            <span class="text-body-2">{{ $t('footer.supportAvailable') }}</span>
                        </div>
                        <div class="d-flex ga-2">
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-facebook</v-icon>
                            </v-btn>
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-instagram</v-icon>
                            </v-btn>
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-twitter</v-icon>
                            </v-btn>
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-telegram</v-icon>
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>

                <v-divider class="my-6"></v-divider>

                <div class="d-flex flex-wrap justify-space-between align-center">
                    <span class="text-body-2 text-medium-emphasis">
                        &copy; {{ new Date().getFullYear() }} SMM Panel. {{ $t('footer.allRightsReserved') }}.
                    </span>
                    <span class="text-body-2 text-medium-emphasis">
                        {{ $t('footer.madeWith') }} <v-icon size="small" color="error">mdi-heart</v-icon> {{ $t('footer.forGrowth') }}
                    </span>
                </div>
            </v-container>
        </v-footer>
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore } from './stores/app'
import { loadLocaleMessages } from './plugins/i18n'
import axios from 'axios'

const { locale } = useI18n()
const route = useRoute()
const store = useAppStore()
const drawer = ref(false)

// Current language display name
const currentLanguageName = computed(() => {
    const lang = store.languages.find(l => l.code === store.locale)
    return lang ? lang.native_name : 'English'
})

// Sync i18n locale with store + RTL support for all languages
watch(() => store.locale, (newLocale) => {
    locale.value = newLocale
    const isRtl = store.isRTL
    document.documentElement.dir = isRtl ? 'rtl' : 'ltr'
    document.documentElement.lang = newLocale
}, { immediate: true })

// Check if current route is an admin route
const isAdminRoute = computed(() => {
    return route.path.startsWith('/admin')
})

const changeLanguage = async (lang) => {
    localStorage.setItem('locale', lang)

    // Load translations from API first
    await loadLocaleMessages(lang)
    await store.setLocale(lang)

    // Sync with Laravel session
    try {
        await axios.post('/api/set-locale', { locale: lang })
    } catch (error) {
        console.error('Failed to sync locale with server:', error)
    }

    if (isAdminRoute.value) {
        window.location.reload()
    } else {
        window.location.href = `/lang/${lang}`
    }
}

onMounted(async () => {
    // Load available languages from API
    await store.fetchLanguages()

    // Load translations for current locale from API
    await loadLocaleMessages(store.locale)

    // Only fetch public data on public routes
    if (!isAdminRoute.value) {
        store.fetchServices()
        store.fetchStats()
    }
})
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

html {
    scroll-behavior: smooth;
}

/* RTL Support */
.rtl {
    direction: rtl;
}

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.5);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.8);
}
</style>
