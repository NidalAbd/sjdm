import { defineStore } from 'pinia'
import axios from 'axios'

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
        locale: localStorage.getItem('locale') || 'en',
    }),

    getters: {
        isDark: (state) => state.theme === 'dark',
        isArabic: (state) => state.locale === 'ar',

        groupedServices: (state) => {
            const grouped = {}
            state.services.forEach(service => {
                const category = state.locale === 'ar' ? service.category_ar : service.category_en
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
        async fetchServices() {
            this.loading = true
            try {
                const response = await axios.get('/api/services')
                this.services = response.data.services || response.data
                this.categories = [...new Set(this.services.map(s =>
                    this.locale === 'ar' ? s.category_ar : s.category_en
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

        setLocale(locale) {
            this.locale = locale
            localStorage.setItem('locale', locale)
            // Refresh services to get correct language
            this.fetchServices()
        }
    }
})
