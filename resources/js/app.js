/**
 * Vue 3 SPA Application
 * SMM Panel
 */

import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createHead } from '@vueuse/head';
import vuetify from './plugins/vuetify';
import i18n from './plugins/i18n';
import router from './router';
import App from './App.vue';

// Create Vue application
const app = createApp(App);

// Create head instance for SEO
const head = createHead();

// Use plugins
app.use(createPinia());
app.use(router);
app.use(vuetify);
app.use(i18n);
app.use(head);

// Mount the application
app.mount('#app');
