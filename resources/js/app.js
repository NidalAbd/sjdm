/**
 * Vue 3 SPA Application
 * SMM Panel
 */

import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import vuetify from './plugins/vuetify';
import router from './router';
import App from './App.vue';

// Create Vue application
const app = createApp(App);

// Use plugins
app.use(createPinia());
app.use(router);
app.use(vuetify);

// Mount the application
app.mount('#app');
