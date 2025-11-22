<template>
    <v-app :theme="store.theme">
        <!-- Navigation -->
        <v-app-bar elevation="0" class="px-4">
            <v-app-bar-title>
                <router-link to="/" class="d-flex align-center text-decoration-none">
                    <v-avatar size="40" class="mr-3">
                        <v-img src="/images/logo.png" alt="SMM Panel"></v-img>
                    </v-avatar>
                    <span class="text-h6 font-weight-bold text-primary">SMM Panel</span>
                </router-link>
            </v-app-bar-title>

            <template v-slot:append>
                <!-- Desktop Navigation -->
                <div class="d-none d-lg-flex align-center ga-1">
                    <v-btn variant="text" to="/" size="small">Home</v-btn>
                    <v-btn variant="text" to="/all-services" size="small">Services</v-btn>
                    <v-btn variant="text" href="https://smm-followerss.com/" target="_blank" size="small">
                        SMM Panel
                        <v-icon end size="small">mdi-open-in-new</v-icon>
                    </v-btn>
                    <v-btn variant="text" to="/about" size="small">About Us</v-btn>
                    <v-btn variant="text" to="/contact-us" size="small">Contact Us</v-btn>
                    <v-btn variant="text" to="/faq" size="small">FAQ</v-btn>
                    <v-btn variant="text" to="/privacy-policy" size="small">Privacy</v-btn>

                    <v-divider vertical class="mx-2" length="24"></v-divider>

                    <!-- Language Selector -->
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn variant="text" v-bind="props" size="small">
                                <v-icon start>mdi-translate</v-icon>
                                {{ store.locale === 'ar' ? 'العربية' : 'English' }}
                                <v-icon end>mdi-chevron-down</v-icon>
                            </v-btn>
                        </template>
                        <v-list density="compact">
                            <v-list-item @click="changeLanguage('en')" :active="store.locale === 'en'">
                                <v-list-item-title>English</v-list-item-title>
                            </v-list-item>
                            <v-list-item @click="changeLanguage('ar')" :active="store.locale === 'ar'">
                                <v-list-item-title>العربية</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <v-btn variant="text" href="/login" size="small">
                        <v-icon start>mdi-login</v-icon>
                        Sign In
                    </v-btn>
                    <v-btn variant="flat" color="primary" href="/register" size="small">
                        <v-icon start>mdi-account-plus</v-icon>
                        Sign Up
                    </v-btn>

                    <v-btn icon @click="store.toggleTheme" size="small" class="ml-2">
                        <v-icon>{{ store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                    </v-btn>
                </div>

                <!-- Mobile Menu -->
                <v-app-bar-nav-icon class="d-lg-none" @click="drawer = !drawer"></v-app-bar-nav-icon>
            </template>
        </v-app-bar>

        <!-- Mobile Drawer -->
        <v-navigation-drawer v-model="drawer" temporary location="right">
            <v-list nav>
                <v-list-item to="/" prepend-icon="mdi-home" title="Home" @click="drawer = false"></v-list-item>
                <v-list-item to="/all-services" prepend-icon="mdi-view-grid" title="Services" @click="drawer = false"></v-list-item>
                <v-list-item href="https://smm-followerss.com/" target="_blank" prepend-icon="mdi-cart" title="SMM Panel"></v-list-item>
                <v-list-item to="/about" prepend-icon="mdi-information" title="About Us" @click="drawer = false"></v-list-item>
                <v-list-item to="/contact-us" prepend-icon="mdi-email" title="Contact Us" @click="drawer = false"></v-list-item>
                <v-list-item to="/faq" prepend-icon="mdi-help-circle" title="FAQ" @click="drawer = false"></v-list-item>
                <v-list-item to="/privacy-policy" prepend-icon="mdi-shield-check" title="Privacy Policy" @click="drawer = false"></v-list-item>
                <v-divider class="my-2"></v-divider>
                <v-list-item href="/login" prepend-icon="mdi-login" title="Sign In"></v-list-item>
                <v-list-item href="/register" prepend-icon="mdi-account-plus" title="Sign Up"></v-list-item>
                <v-divider class="my-2"></v-divider>
                <v-list-subheader>Language</v-list-subheader>
                <v-list-item @click="changeLanguage('en')" :active="store.locale === 'en'" title="English"></v-list-item>
                <v-list-item @click="changeLanguage('ar')" :active="store.locale === 'ar'" title="العربية"></v-list-item>
                <v-divider class="my-2"></v-divider>
                <v-list-item @click="store.toggleTheme" :prepend-icon="store.isDark ? 'mdi-weather-sunny' : 'mdi-weather-night'" :title="store.isDark ? 'Light Mode' : 'Dark Mode'"></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </v-main>

        <!-- Footer -->
        <v-footer class="bg-surface pa-8">
            <v-container>
                <v-row>
                    <v-col cols="12" md="4">
                        <div class="d-flex align-center mb-4">
                            <v-avatar size="48" class="mr-3">
                                <v-img src="/images/logo.png" alt="SMM Panel"></v-img>
                            </v-avatar>
                            <div>
                                <div class="text-h6 font-weight-bold">SMM Panel</div>
                                <div class="text-caption text-medium-emphasis">Best SMM Services</div>
                            </div>
                        </div>
                        <p class="text-body-2 text-medium-emphasis">
                            The cheapest and most reliable SMM panel for Instagram, TikTok, YouTube, Facebook and more. Instant delivery with 24/7 support.
                        </p>
                    </v-col>

                    <v-col cols="6" md="2">
                        <div class="text-subtitle-1 font-weight-bold mb-3">Quick Links</div>
                        <v-list density="compact" class="bg-transparent pa-0">
                            <v-list-item to="/" class="px-0" min-height="32">Home</v-list-item>
                            <v-list-item to="/all-services" class="px-0" min-height="32">Services</v-list-item>
                            <v-list-item to="/faq" class="px-0" min-height="32">FAQ</v-list-item>
                            <v-list-item to="/contact-us" class="px-0" min-height="32">Contact</v-list-item>
                        </v-list>
                    </v-col>

                    <v-col cols="6" md="2">
                        <div class="text-subtitle-1 font-weight-bold mb-3">Legal</div>
                        <v-list density="compact" class="bg-transparent pa-0">
                            <v-list-item to="/privacy-policy" class="px-0" min-height="32">Privacy Policy</v-list-item>
                            <v-list-item to="/about" class="px-0" min-height="32">About Us</v-list-item>
                            <v-list-item to="/how-it-works" class="px-0" min-height="32">How It Works</v-list-item>
                        </v-list>
                    </v-col>

                    <v-col cols="12" md="4">
                        <div class="text-subtitle-1 font-weight-bold mb-3">Contact Us</div>
                        <div class="d-flex align-center mb-2">
                            <v-icon size="small" class="mr-2">mdi-email</v-icon>
                            <span class="text-body-2">support@smmjd.com</span>
                        </div>
                        <div class="d-flex align-center mb-4">
                            <v-icon size="small" class="mr-2">mdi-clock</v-icon>
                            <span class="text-body-2">24/7 Support Available</span>
                        </div>
                        <div class="d-flex ga-2">
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-facebook</v-icon>
                            </v-btn>
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-instagram</v-icon>
                            </v-btn>
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-twitter</v-icon>
                            </v-btn>
                            <v-btn icon variant="tonal" size="small">
                                <v-icon>mdi-telegram</v-icon>
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>

                <v-divider class="my-6"></v-divider>

                <div class="d-flex flex-wrap justify-space-between align-center">
                    <span class="text-body-2 text-medium-emphasis">
                        &copy; {{ new Date().getFullYear() }} SMM Panel. All rights reserved.
                    </span>
                    <span class="text-body-2 text-medium-emphasis">
                        Made with <v-icon size="small" color="error">mdi-heart</v-icon> for growth
                    </span>
                </div>
            </v-container>
        </v-footer>
    </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAppStore } from './stores/app'

const store = useAppStore()
const drawer = ref(false)

const changeLanguage = (lang) => {
    store.setLocale(lang)
    // Redirect to Laravel language route
    window.location.href = `/lang/${lang}`
}

onMounted(() => {
    store.fetchServices()
    store.fetchStats()
})
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

html {
    scroll-behavior: smooth;
}

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.5);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.8);
}
</style>
