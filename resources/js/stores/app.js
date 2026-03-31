import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

// Available theme colors - only primary color changes, backgrounds stay neutral
export const themeColors = {
    blue: { name: 'Blue', primary: '#3b82f6', primaryDark: '#60a5fa', gradient: '#8b5cf6' },
    indigo: { name: 'Indigo', primary: '#6366f1', primaryDark: '#818cf8', gradient: '#a855f7' },
    purple: { name: 'Purple', primary: '#8b5cf6', primaryDark: '#a78bfa', gradient: '#ec4899' },
    pink: { name: 'Pink', primary: '#ec4899', primaryDark: '#f472b6', gradient: '#f43f5e' },
    red: { name: 'Red', primary: '#ef4444', primaryDark: '#f87171', gradient: '#f97316' },
    orange: { name: 'Orange', primary: '#f97316', primaryDark: '#fb923c', gradient: '#eab308' },
    green: { name: 'Green', primary: '#22c55e', primaryDark: '#4ade80', gradient: '#14b8a6' },
    teal: { name: 'Teal', primary: '#14b8a6', primaryDark: '#2dd4bf', gradient: '#06b6d4' },
    cyan: { name: 'Cyan', primary: '#06b6d4', primaryDark: '#22d3ee', gradient: '#3b82f6' },
}

export const useAppStore = defineStore('app', {
    state: () => ({
        services: [],
        categories: [],
        selectedService: null,
        stats: {
            totalServices: 0,
            totalOrders: 0,
            totalUsers: 0,
        },
        loading: false,
        theme: localStorage.getItem('theme') || 'dark',
        themeColor: localStorage.getItem('themeColor') || 'blue',
        locale: localStorage.getItem('locale') || 'en',
        // Dynamic translations from API
        apiTranslations: {},
        translationsLoaded: false,
        // Available languages from API
        languages: [],
        languagesLoaded: false,
    }),

    getters: {
        isDark: (state) => state.theme === 'dark',
        isArabic: (state) => state.locale === 'ar',
        isRTL: (state) => {
            const lang = state.languages.find(l => l.code === state.locale)
            return lang ? lang.direction === 'rtl' : state.locale === 'ar'
        },
        currentThemeColor: (state) => themeColors[state.themeColor] || themeColors.blue,
        primaryColor: (state) => (themeColors[state.themeColor] || themeColors.blue).primary,
        primaryDarkColor: (state) => (themeColors[state.themeColor] || themeColors.blue).primaryDark,
        gradientColor: (state) => (themeColors[state.themeColor] || themeColors.blue).gradient,
        gradientStyle: (state) => {
            const c = themeColors[state.themeColor] || themeColors.blue
            const p = state.theme === 'dark' ? c.primaryDark : c.primary
            return `linear-gradient(135deg, ${p}, ${c.gradient})`
        },

        groupedServices: (state) => {
            const grouped = {}
            state.services.forEach(service => {
                const category = service.category || service.category_en || ''
                if (!grouped[category]) {
                    grouped[category] = []
                }
                grouped[category].push(service)
            })
            return grouped
        },

        featuredServices: (state) => {
            return state.services.slice(0, 6)
        }
    },

    actions: {
        async fetchLanguages() {
            try {
                const response = await axios.get('/api/languages')
                if (response.data.success) {
                    this.languages = response.data.languages
                    this.languagesLoaded = true
                }
            } catch (error) {
                console.error('Error fetching languages:', error)
            }
        },

        async loadTranslations(locale) {
            try {
                const response = await axios.get(`/api/translations/${locale}`)
                if (response.data.success) {
                    this.apiTranslations = response.data.translations
                    this.translationsLoaded = true
                }
            } catch (error) {
                console.error('Error loading translations:', error)
                this.apiTranslations = {}
            }
        },

        // Dynamic translation function - checks API translations first, then falls back to vue-i18n
        dt(key) {
            if (this.apiTranslations && this.apiTranslations[key]) {
                return this.apiTranslations[key]
            }
            return null // Let vue-i18n handle fallback
        },

        async fetchServices() {
            this.loading = true
            try {
                const response = await axios.get('/api/services', { params: { lang: this.locale } })
                this.services = response.data.services || response.data
                this.categories = [...new Set(this.services.map(s =>
                    s.category || s.category_en || ''
                ))]
            } catch (error) {
                console.error('Error fetching services:', error)
            } finally {
                this.loading = false
            }
        },

        async fetchService(id) {
            this.loading = true
            try {
                const response = await axios.get(`/api/services/${id}`)
                this.selectedService = response.data.service || response.data
                return this.selectedService
            } catch (error) {
                console.error('Error fetching service:', error)
                return null
            } finally {
                this.loading = false
            }
        },

        async fetchStats() {
            try {
                const response = await axios.get('/api/stats')
                this.stats = response.data
            } catch (error) {
                console.error('Error fetching stats:', error)
            }
        },

        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark'
            localStorage.setItem('theme', this.theme)
        },

        setTheme(theme) {
            this.theme = theme
            localStorage.setItem('theme', theme)
        },

        async setLocale(locale) {
            this.locale = locale
            localStorage.setItem('locale', locale)
            // Load translations from API for the new locale
            await this.loadTranslations(locale)
            this.fetchServices()
        },

        setThemeColor(colorKey) {
            if (themeColors[colorKey]) {
                this.themeColor = colorKey
                localStorage.setItem('themeColor', colorKey)
            }
        },
    }
})
