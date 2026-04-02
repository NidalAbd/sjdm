import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import { themeColors } from '../stores/app'

const storedThemeColor = localStorage.getItem('themeColor') || 'indigo'
const themeColor = themeColors[storedThemeColor] || themeColors.indigo

const lightTheme = {
    dark: false,
    colors: {
        primary: themeColor.primary,
        secondary: '#64748b',
        accent: '#f59e0b',
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        info: '#3b82f6',
        background: '#fafafa',
        surface: '#ffffff',
        'surface-variant': '#f5f5f5',
        'on-background': '#111111',
        'on-surface': '#333333',
        'on-primary': '#ffffff',
    }
}

const darkTheme = {
    dark: true,
    colors: {
        primary: themeColor.primaryDark,
        secondary: '#94a3b8',
        accent: '#fbbf24',
        success: '#34d399',
        warning: '#fbbf24',
        error: '#f87171',
        info: '#60a5fa',
        background: '#0c0c0e',
        surface: '#161618',
        'surface-variant': '#1c1c1f',
        'on-background': '#f5f5f5',
        'on-surface': '#d4d4d4',
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
        VBtn: { rounded: 'lg', elevation: 0 },
        VCard: { rounded: 'xl', elevation: 0 },
        VTextField: { variant: 'outlined', density: 'compact', hideDetails: 'auto', color: 'primary' },
        VSelect: { variant: 'outlined', density: 'compact', hideDetails: 'auto', color: 'primary' },
        VChip: { rounded: 'lg', size: 'small' },
        VAppBar: { elevation: 0 },
    },
})

export function updateThemeColors(colorKey) {
    const color = themeColors[colorKey] || themeColors.indigo
    if (vuetify.theme.themes.value) {
        vuetify.theme.themes.value.light.colors.primary = color.primary
        vuetify.theme.themes.value.dark.colors.primary = color.primaryDark
    }
}

export default vuetify
