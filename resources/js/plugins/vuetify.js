import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const lightTheme = {
    dark: false,
    colors: {
        primary: '#6366f1',
        secondary: '#64748b',
        accent: '#f59e0b',
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        info: '#3b82f6',
        background: '#f8fafc',
        surface: '#ffffff',
        'on-background': '#1e293b',
        'on-surface': '#1e293b',
    }
}

const darkTheme = {
    dark: true,
    colors: {
        primary: '#818cf8',
        secondary: '#94a3b8',
        accent: '#fbbf24',
        success: '#34d399',
        warning: '#fbbf24',
        error: '#f87171',
        info: '#60a5fa',
        background: '#0f172a',
        surface: '#1e293b',
        'on-background': '#f1f5f9',
        'on-surface': '#f1f5f9',
    }
}

export default createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'dark',
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
