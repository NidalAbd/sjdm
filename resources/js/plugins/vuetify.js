import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { themeColors } from '../stores/app'

// Get stored theme color or default to blue
const storedThemeColor = localStorage.getItem('themeColor') || 'blue'
const themeColor = themeColors[storedThemeColor] || themeColors.blue

// Clean, professional light theme - neutral backgrounds
const lightTheme = {
    dark: false,
    colors: {
        primary: themeColor.primary,
        secondary: '#64748b',
        accent: '#f59e0b',
        success: '#22c55e',
        warning: '#f59e0b',
        error: '#ef4444',
        info: '#3b82f6',
        background: '#f1f5f9',
        surface: '#ffffff',
        'on-background': '#1e293b',
        'on-surface': '#334155',
        'on-primary': '#ffffff',
    }
}

// Clean, professional dark theme - neutral backgrounds
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
        background: '#0f172a',
        surface: '#1e293b',
        'on-background': '#e2e8f0',
        'on-surface': '#cbd5e1',
        'on-primary': '#ffffff',
    }
}

const vuetify = createVuetify({
    components,
    directives,
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
            rounded: 'lg',
            elevation: 1,
        },
        VTextField: {
            variant: 'outlined',
            density: 'comfortable',
        },
        VSelect: {
            variant: 'outlined',
            density: 'comfortable',
        },
    },
})

// Export function to update theme colors dynamically - only primary color changes
export function updateThemeColors(colorKey) {
    const color = themeColors[colorKey] || themeColors.blue
    if (vuetify.theme.themes.value) {
        vuetify.theme.themes.value.light.colors.primary = color.primary
        vuetify.theme.themes.value.dark.colors.primary = color.primaryDark
    }
}

export default vuetify
