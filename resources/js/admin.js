import { createApp } from 'vue'
import { createPinia } from 'pinia'
import AdminApp from './AdminApp.vue'
import adminRouter from './router/admin'
import vuetify from './plugins/vuetify'
import i18n, { loadLocaleMessages } from './plugins/i18n'

const app = createApp(AdminApp)
const pinia = createPinia()

app.use(pinia)
app.use(adminRouter)
app.use(vuetify)
app.use(i18n)

// Load translations for current locale
const locale = localStorage.getItem('locale') || 'en'
loadLocaleMessages(locale)

app.mount('#admin-app')
