import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import { themeColors } from '../stores/app'

const storedThemeColor = localStorage.getItem('themeColor') || 'blue'
const themeColor = themeColors[storedThemeColor] || themeColors.blue

const lightTheme = {
    dark: false,
    colors: {
        primary: themeColor.primary,
        secondary: '#64748b',
        accent: '#f59e0b',
        success: '#16a34a',
        warning: '#f59e0b',
        error: '#dc2626',
        info: '#2563eb',
        background: '#f8fafc',
        surface: '#ffffff',
        'surface-variant': '#f1f5f9',
        'surface-bright': '#ffffff',
        'on-background': '#0f172a',
        'on-surface': '#1e293b',
        'on-primary': '#ffffff',
    }
}

const darkTheme = {
    dark: true,
    colors: {
        primary: themeColor.primaryDark,
        secondary: '#94a3b8',
        accent: '#fbbf24',
        success: '#4ade80',
        warning: '#fbbf24',
        error: '#f87171',
        info: '#60a5fa',
        background: '#0a0e1a',
        surface: '#111827',
        'surface-variant': '#1e293b',
        'surface-bright': '#1e293b',
        'on-background': '#f1f5f9',
        'on-surface': '#e2e8f0',
        'on-primary': '#ffffff',
    }
}

const vuetify = createVuetify({
    theme: {
        defaultTheme: localStorage.getItem('theme') || 'dark',
        themes: {
            light: lightTheme,
            dark: darkTheme,
        },
    },
    defaults: {
        VBtn: {
            rounded: 'lg',
            elevation: 0,
        },
        VCard: {
            rounded: 'xl',
            elevation: 0,
            border: true,
        },
        VTextField: {
            variant: 'outlined',
            density: 'compact',
            hideDetails: 'auto',
        },
        VSelect: {
            variant: 'outlined',
            density: 'compact',
            hideDetails: 'auto',
        },
        VChip: {
            rounded: 'lg',
            size: 'small',
        },
        VDialog: {
            rounded: 'xl',
        },
        VNavigationDrawer: {
            elevation: 0,
        },
        VAppBar: {
            elevation: 0,
        },
        VDataTable: {
            hover: true,
        },
    },
})

export function updateThemeColors(colorKey) {
    const color = themeColors[colorKey] || themeColors.blue
    if (vuetify.theme.themes.value) {
        vuetify.theme.themes.value.light.colors.primary = color.primary
        vuetify.theme.themes.value.dark.colors.primary = color.primaryDark
    }
}

export default vuetify
