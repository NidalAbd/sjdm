<template>
    <v-app :theme="store.theme" :class="{ 'rtl': store.isRTL }">
        <!-- Public Layout -->
        <template v-if="!isAdminRoute">
            <!-- Navbar -->
            <v-app-bar elevation="0" color="surface" border="b" density="compact">
                <v-container class="d-flex align-center py-0">
                    <router-link to="/" class="d-flex align-center text-decoration-none">
                        <v-avatar size="32" class="mr-2">
                            <v-img src="/images/logo.png" alt="SMM Panel"></v-img>
                        </v-avatar>
                        <span class="text-subtitle-1 font-weight-bold text-primary d-none d-sm-inline">SMM Panel</span>
                    </router-link>

                    <v-spacer></v-spacer>

                    <!-- Desktop Nav -->
                    <div class="d-none d-lg-flex align-center ga-0">
                        <v-btn variant="text" to="/" size="small" class="text-body-2">{{ $t('publicNav.home') }}</v-btn>
                        <v-btn variant="text" to="/all-services" size="small" class="text-body-2">{{ $t('publicNav.services') }}</v-btn>
                        <v-btn variant="text" to="/about" size="small" class="text-body-2">{{ $t('publicNav.aboutUs') }}</v-btn>
                        <v-btn variant="text" to="/faq" size="small" class="text-body-2">{{ $t('publicNav.faq') }}</v-btn>
                        <v-btn variant="text" to="/contact-us" size="small" class="text-body-2">{{ $t('publicNav.contactUs') }}</v-btn>

                        <v-divider vertical class="mx-2" length="20"></v-divider>

                        <!-- Language -->
                        <v-menu max-height="350">
                            <template v-slot:activator="{ props }">
                                <v-btn variant="text" v-bind="props" size="small" class="text-body-2">
                                    <v-icon start size="16">mdi-translate</v-icon>
                                    {{ currentLanguageName }}
                                    <v-icon end size="14">mdi-chevron-down</v-icon>
                                </v-btn>
                            </template>
                            <v-list density="compact">
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

                        <v-btn variant="text" href="/login" size="small" class="text-body-2">
                            <v-icon start size="16">mdi-login</v-icon>
                            {{ $t('publicNav.signIn') }}
                        </v-btn>
                        <v-btn variant="flat" color="primary" href="/register" size="small" class="text-body-2">
                            {{ $t('publicNav.signUp') }}
                        </v-btn>
                        <v-btn icon @click="store.toggleTheme" size="small" variant="text">
                            <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                        </v-btn>
                    </div>

                    <!-- Mobile -->
                    <v-app-bar-nav-icon class="d-lg-none" @click="drawer = !drawer" size="small"></v-app-bar-nav-icon>
                </v-container>
            </v-app-bar>

            <!-- Mobile Drawer -->
            <v-navigation-drawer v-model="drawer" temporary location="right" width="280">
                <v-list nav density="compact" class="pa-2">
                    <v-list-item to="/" prepend-icon="mdi-home" :title="$t('publicNav.home')" @click="drawer = false" density="compact"></v-list-item>
                    <v-list-item to="/all-services" prepend-icon="mdi-view-grid" :title="$t('publicNav.services')" @click="drawer = false" density="compact"></v-list-item>
                    <v-list-item to="/about" prepend-icon="mdi-information" :title="$t('publicNav.aboutUs')" @click="drawer = false" density="compact"></v-list-item>
                    <v-list-item to="/faq" prepend-icon="mdi-help-circle" :title="$t('publicNav.faq')" @click="drawer = false" density="compact"></v-list-item>
                    <v-list-item to="/contact-us" prepend-icon="mdi-email" :title="$t('publicNav.contactUs')" @click="drawer = false" density="compact"></v-list-item>
                    <v-list-item to="/privacy-policy" prepend-icon="mdi-shield-check" :title="$t('publicNav.privacy')" @click="drawer = false" density="compact"></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-item href="/login" prepend-icon="mdi-login" :title="$t('publicNav.signIn')" density="compact"></v-list-item>
                    <v-list-item href="/register" prepend-icon="mdi-account-plus" :title="$t('publicNav.signUp')" density="compact"></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-subheader class="text-caption">{{ $t('publicNav.language') }}</v-list-subheader>
                    <v-list-item
                        v-for="lang in store.languages"
                        :key="lang.code"
                        @click="changeLanguage(lang.code)"
                        :active="store.locale === lang.code"
                        :title="lang.native_name"
                        density="compact"
                    ></v-list-item>
                    <v-divider class="my-2"></v-divider>
                    <v-list-item @click="store.toggleTheme" :prepend-icon="store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night'" :title="store.isDark ? $t('publicNav.lightMode') : $t('publicNav.darkMode')" density="compact"></v-list-item>
                </v-list>
            </v-navigation-drawer>

            <!-- Content -->
            <v-main>
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </v-main>

            <!-- Footer -->
            <v-footer class="bg-surface pa-0" border="t">
                <v-container class="py-8">
                    <v-row>
                        <v-col cols="12" md="4">
                            <div class="d-flex align-center mb-3">
                                <v-avatar size="36" class="mr-2">
                                    <v-img src="/images/logo.png" alt="SMM Panel"></v-img>
                                </v-avatar>
                                <div>
                                    <div class="text-subtitle-1 font-weight-bold">SMM Panel</div>
                                    <div class="text-caption text-medium-emphasis">{{ $t('footer.bestServices') }}</div>
                                </div>
                            </div>
                            <p class="text-body-2 text-medium-emphasis" style="line-height: 1.6;">
                                {{ $t('footer.description') }}
                            </p>
                        </v-col>

                        <v-col cols="6" md="2">
                            <div class="text-caption font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.08em;">{{ $t('footer.quickLinks') }}</div>
                            <v-list density="compact" class="bg-transparent pa-0">
                                <v-list-item to="/" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.home') }}</span></v-list-item>
                                <v-list-item to="/all-services" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.services') }}</span></v-list-item>
                                <v-list-item to="/faq" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.faq') }}</span></v-list-item>
                                <v-list-item to="/contact-us" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.contactUs') }}</span></v-list-item>
                            </v-list>
                        </v-col>

                        <v-col cols="6" md="2">
                            <div class="text-caption font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.08em;">{{ $t('footer.legal') }}</div>
                            <v-list density="compact" class="bg-transparent pa-0">
                                <v-list-item to="/privacy-policy" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.privacy') }}</span></v-list-item>
                                <v-list-item to="/about" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.aboutUs') }}</span></v-list-item>
                                <v-list-item to="/how-it-works" class="px-0" min-height="28" density="compact"><span class="text-body-2">{{ $t('publicNav.howItWorks') }}</span></v-list-item>
                            </v-list>
                        </v-col>

                        <v-col cols="12" md="4">
                            <div class="text-caption font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.08em;">{{ $t('footer.contactUs') }}</div>
                            <div class="d-flex align-center mb-2">
                                <v-icon size="14" class="mr-2 text-medium-emphasis">mdi-email</v-icon>
                                <span class="text-body-2">support@smmjd.com</span>
                            </div>
                            <div class="d-flex align-center mb-3">
                                <v-icon size="14" class="mr-2 text-medium-emphasis">mdi-clock</v-icon>
                                <span class="text-body-2">{{ $t('footer.supportAvailable') }}</span>
                            </div>
                            <div class="d-flex ga-1">
                                <v-btn icon variant="tonal" size="x-small"><v-icon size="16">mdi-facebook</v-icon></v-btn>
                                <v-btn icon variant="tonal" size="x-small"><v-icon size="16">mdi-instagram</v-icon></v-btn>
                                <v-btn icon variant="tonal" size="x-small"><v-icon size="16">mdi-twitter</v-icon></v-btn>
                                <v-btn icon variant="tonal" size="x-small"><v-icon size="16">mdi-telegram</v-icon></v-btn>
                            </div>
                        </v-col>
                    </v-row>

                    <v-divider class="my-4"></v-divider>

                    <div class="d-flex flex-wrap justify-space-between align-center">
                        <span class="text-caption text-medium-emphasis">
                            &copy; {{ new Date().getFullYear() }} SMM Panel. {{ $t('footer.allRightsReserved') }}.
                        </span>
                    </div>
                </v-container>
            </v-footer>
        </template>

        <!-- Admin Layout -->
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
import axios from 'axios'

const { locale } = useI18n()
const route = useRoute()
const store = useAppStore()
const drawer = ref(false)

const currentLanguageName = computed(() => {
    const lang = store.languages.find(l => l.code === store.locale)
    return lang ? lang.native_name : 'English'
})

watch(() => store.locale, (newLocale) => {
    locale.value = newLocale
    document.documentElement.dir = store.isRTL ? 'rtl' : 'ltr'
    document.documentElement.lang = newLocale
}, { immediate: true })

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

const changeLanguage = async (lang) => {
    localStorage.setItem('locale', lang)
    await loadLocaleMessages(lang)
    await store.setLocale(lang)
    try { await axios.post('/api/set-locale', { locale: lang }) } catch {}
    if (isAdminRoute.value) {
        window.location.reload()
    } else {
        window.location.href = `/lang/${lang}`
    }
}

onMounted(async () => {
    await store.fetchLanguages()
    await loadLocaleMessages(store.locale)
    if (!isAdminRoute.value) {
        store.fetchServices()
        store.fetchStats()
    }
})
</script>
