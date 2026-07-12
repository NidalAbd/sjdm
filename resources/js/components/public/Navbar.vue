<template>
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
                    <button v-bind="props" class="icon-btn icon-btn-labeled opacity-60">
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
            <button @click="store.toggleTheme" class="icon-btn">
                <v-icon size="18">{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
            </button>

            <a href="/login" class="auth-text" style="padding:6px 14px;border-radius:8px;text-decoration:none;color:inherit;font-size:0.82rem;font-weight:500;opacity:0.7;">{{ $t('publicNav.signIn') }}</a>
            <v-btn color="primary" href="/register" size="small" class="auth-text">{{ $t('publicNav.signUp') }}</v-btn>

            <!-- Mobile menu -->
            <button class="d-lg-none icon-btn" @click="$emit('toggle-drawer')">
                <v-icon size="22">mdi-menu</v-icon>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAppStore } from '../../stores/app'
import { loadLocaleMessages } from '../../plugins/i18n'
import axios from 'axios'

defineEmits(['toggle-drawer'])

const store = useAppStore()

const currentLanguageName = computed(() => {
    const lang = store.languages.find(l => l.code === store.locale)
    return lang ? lang.native_name : 'EN'
})

const changeLanguage = async (lang) => {
    localStorage.setItem('locale', lang)
    await loadLocaleMessages(lang)
    await store.setLocale(lang)
    try { await axios.post('/api/set-locale', { locale: lang }) } catch {}
    window.location.href = `/lang/${lang}`
}
</script>
