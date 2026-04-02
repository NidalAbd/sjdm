<template>
    <v-app :theme="store.theme" :class="{ 'rtl': store.isRTL }">
        <template v-if="!isAdminRoute">
            <!-- Navbar -->
            <div class="pub-navbar">
                <router-link to="/" class="pub-navbar-logo">
                    <img src="/images/logo.png" alt="SMM Panel" width="28" height="28" style="border-radius: 6px;">
                    <span>SMM Panel</span>
                </router-link>

                <div class="pub-navbar-links">
                    <router-link to="/">{{ $t('publicNav.home') }}</router-link>
                    <router-link to="/all-services">{{ $t('publicNav.services') }}</router-link>
                    <router-link to="/about">{{ $t('publicNav.aboutUs') }}</router-link>
                    <router-link to="/faq">{{ $t('publicNav.faq') }}</router-link>
                    <router-link to="/contact-us">{{ $t('publicNav.contactUs') }}</router-link>
                </div>

                <div class="pub-navbar-auth">
                    <!-- Language -->
                    <v-menu max-height="350">
                        <template v-slot:activator="{ props }">
                            <button v-bind="props" style="display:flex;align-items:center;gap:4px;padding:6px 10px;border:none;background:none;cursor:pointer;color:inherit;border-radius:8px;font-size:0.78rem;" class="opacity-60">
                                <v-icon size="16">mdi-translate</v-icon>
                                <span class="auth-text">{{ currentLanguageName }}</span>
                            </button>
                        </template>
                        <v-list density="compact">
                            <v-list-item v-for="lang in store.languages" :key="lang.code" @click="changeLanguage(lang.code)" :active="store.locale === lang.code" density="compact">
                                <v-list-item-title class="text-body-2">{{ lang.native_name }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <!-- Theme -->
                    <button @click="store.toggleTheme" style="display:flex;align-items:center;padding:6px;border:none;background:none;cursor:pointer;color:inherit;border-radius:8px;opacity:0.6;">
                        <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                    </button>

                    <a href="/login" class="auth-text" style="padding:6px 14px;border-radius:8px;text-decoration:none;color:inherit;font-size:0.82rem;font-weight:500;opacity:0.7;">{{ $t('publicNav.signIn') }}</a>
                    <v-btn color="primary" href="/register" size="small" class="auth-text">{{ $t('publicNav.signUp') }}</v-btn>

                    <!-- Mobile menu -->
                    <button class="d-lg-none" @click="drawer = !drawer" style="display:flex;align-items:center;padding:6px;border:none;background:none;cursor:pointer;color:inherit;">
                        <v-icon size="22">mdi-menu</v-icon>
                    </button>
                </div>
            </div>

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

            <!-- Footer -->
            <footer class="pub-footer">
                <v-container>
                    <div class="footer-grid">
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                <img src="/images/logo.png" alt="SMM Panel" width="32" height="32" style="border-radius:8px;">
                                <span style="font-weight:800;font-size:1rem;">SMM Panel</span>
                            </div>
                            <p style="font-size:0.82rem;opacity:0.5;line-height:1.7;">{{ $t('footer.description') }}</p>
                        </div>
                        <div>
                            <div class="footer-heading">{{ $t('footer.quickLinks') }}</div>
                            <ul class="footer-links">
                                <li><router-link to="/">{{ $t('publicNav.home') }}</router-link></li>
                                <li><router-link to="/all-services">{{ $t('publicNav.services') }}</router-link></li>
                                <li><router-link to="/faq">{{ $t('publicNav.faq') }}</router-link></li>
                                <li><router-link to="/contact-us">{{ $t('publicNav.contactUs') }}</router-link></li>
                            </ul>
                        </div>
                        <div>
                            <div class="footer-heading">{{ $t('footer.legal') }}</div>
                            <ul class="footer-links">
                                <li><router-link to="/privacy-policy">{{ $t('publicNav.privacy') }}</router-link></li>
                                <li><router-link to="/about">{{ $t('publicNav.aboutUs') }}</router-link></li>
                                <li><router-link to="/how-it-works">{{ $t('publicNav.howItWorks') }}</router-link></li>
                            </ul>
                        </div>
                        <div>
                            <div class="footer-heading">{{ $t('footer.contactUs') }}</div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:0.82rem;opacity:0.6;margin-bottom:6px;">
                                <v-icon size="14">mdi-email</v-icon> support@smmjd.com
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:0.82rem;opacity:0.6;">
                                <v-icon size="14">mdi-clock</v-icon> {{ $t('footer.supportAvailable') }}
                            </div>
                        </div>
                    </div>
                    <div class="footer-copy">&copy; {{ new Date().getFullYear() }} SMM Panel. {{ $t('footer.allRightsReserved') }}.</div>
                </v-container>
            </footer>
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
import axios from 'axios'

const { locale } = useI18n()
const route = useRoute()
const store = useAppStore()
const drawer = ref(false)

const currentLanguageName = computed(() => {
    const lang = store.languages.find(l => l.code === store.locale)
    return lang ? lang.native_name : 'EN'
})

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
