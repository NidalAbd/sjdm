<template>
    <div>
        <!-- Hero Section with Quick Sign In -->
        <section class="hero-section">
            <v-container class="py-16">
                <v-row align="center">
                    <v-col cols="12" lg="7">
                        <v-chip color="primary" variant="tonal" class="mb-4">
                            <v-icon start>mdi-star</v-icon>
                            #1 SMM Panel
                        </v-chip>

                        <h1 class="text-h2 text-md-h1 font-weight-black mb-4">
                            {{ $t('home.heroTitle') }} <span class="text-primary">{{ $t('home.heroTitleHighlight') }}</span>
                        </h1>

                        <p class="text-h6 text-medium-emphasis mb-6" style="line-height: 1.7;">
                            {{ $t('home.heroDescription') }}
                        </p>

                        <div class="d-flex flex-wrap ga-3 mb-8">
                            <div class="d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                <span>{{ $t('home.instantDelivery') }}</span>
                            </div>
                            <div class="d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                <span>{{ $t('home.support247') }}</span>
                            </div>
                            <div class="d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                <span>{{ $t('home.bestPrices') }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap ga-3">
                            <v-btn size="x-large" color="primary" to="/all-services" class="px-8">
                                <v-icon start>mdi-view-grid</v-icon>
                                {{ $t('home.viewServices') }}
                            </v-btn>
                            <v-btn size="x-large" variant="outlined" href="/register" class="px-8">
                                <v-icon start>mdi-account-plus</v-icon>
                                {{ $t('home.getStarted') }}
                            </v-btn>
                        </div>
                    </v-col>

                    <!-- Quick Sign In Card -->
                    <v-col cols="12" lg="5">
                        <v-card class="pa-6 quick-signin-card">
                            <div class="text-center mb-6">
                                <v-icon size="48" color="primary" class="mb-2">mdi-account-circle</v-icon>
                                <h3 class="text-h5 font-weight-bold">{{ $t('home.quickSignIn') }}</h3>
                                <p class="text-body-2 text-medium-emphasis">{{ $t('home.accessDashboard') }}</p>
                            </div>

                            <v-form @submit.prevent="handleLogin">
                                <v-text-field
                                    v-model="loginForm.email"
                                    :label="$t('contact.email')"
                                    type="email"
                                    prepend-inner-icon="mdi-email"
                                    class="mb-2"
                                    required
                                ></v-text-field>

                                <v-text-field
                                    v-model="loginForm.password"
                                    :label="$t('home.password')"
                                    :type="showPassword ? 'text' : 'password'"
                                    prepend-inner-icon="mdi-lock"
                                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="showPassword = !showPassword"
                                    class="mb-4"
                                    required
                                ></v-text-field>

                                <v-btn type="submit" color="primary" size="large" block :loading="loginLoading">
                                    <v-icon start>mdi-login</v-icon>
                                    {{ $t('publicNav.signIn') }}
                                </v-btn>
                            </v-form>

                            <div class="text-center mt-4">
                                <span class="text-body-2 text-medium-emphasis">{{ $t('home.noAccount') }}</span>
                                <a href="/register" class="text-primary text-decoration-none ml-1 font-weight-medium">{{ $t('publicNav.signUp') }}</a>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>

            <div class="hero-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-12 bg-surface">
            <v-container>
                <v-row>
                    <v-col v-for="(stat, i) in stats" :key="i" cols="6" md="3">
                        <v-card variant="flat" class="text-center pa-6 stat-card">
                            <v-icon :color="stat.color" size="40" class="mb-2">{{ stat.icon }}</v-icon>
                            <div class="text-h4 font-weight-bold">{{ stat.value }}</div>
                            <div class="text-body-2 text-medium-emphasis">{{ $t(stat.labelKey) }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Platform Cards -->
        <section class="py-16">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('home.allPlatforms') }}</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">{{ $t('home.platformsWeSupport') }}</h2>
                    <p class="text-h6 text-medium-emphasis">{{ $t('home.platformsDesc') }}</p>
                </div>

                <v-row>
                    <v-col v-for="platform in platforms" :key="platform.name" cols="6" sm="4" md="2">
                        <v-card class="platform-card text-center pa-4" hover :to="`/all-services?platform=${platform.key}`">
                            <v-icon :color="platform.color" size="48" class="mb-2">{{ platform.icon }}</v-icon>
                            <div class="text-subtitle-2 font-weight-medium">{{ platform.name }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Featured Services -->
        <section id="services" class="py-16 bg-surface">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('publicNav.services') }}</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">{{ $t('home.popularServices') }}</h2>
                    <p class="text-h6 text-medium-emphasis">{{ $t('home.popularServicesDesc') }}</p>
                </div>

                <v-row v-if="!store.loading">
                    <v-col v-for="service in featuredServices" :key="service.service_id" cols="12" sm="6" lg="4">
                        <v-card class="service-card h-100" variant="outlined" :to="`/service/${service.service_id}`">
                            <v-card-text class="pa-6">
                                <div class="d-flex justify-space-between align-start mb-4">
                                    <v-chip size="small" color="primary" variant="flat">
                                        #{{ service.service_id }}
                                    </v-chip>
                                    <v-chip size="small" color="success" variant="flat">
                                        ${{ Number(service.rate).toFixed(4) }}/1K
                                    </v-chip>
                                </div>
                                <h3 class="text-subtitle-1 font-weight-bold mb-3 service-name">
                                    {{ store.locale === 'ar' ? service.name_ar : service.name_en }}
                                </h3>
                                <div class="text-body-2 text-primary mb-3">
                                    <v-icon size="14" class="mr-1">mdi-tag</v-icon>
                                    {{ store.locale === 'ar' ? service.category_ar : service.category_en }}
                                </div>
                                <v-row dense>
                                    <v-col cols="6">
                                        <div class="stat-box pa-2 text-center rounded">
                                            <div class="text-caption text-medium-emphasis">{{ $t('servicesPage.min') }}</div>
                                            <div class="text-body-2 font-weight-bold">{{ formatNumber(service.min) }}</div>
                                        </div>
                                    </v-col>
                                    <v-col cols="6">
                                        <div class="stat-box pa-2 text-center rounded">
                                            <div class="text-caption text-medium-emphasis">{{ $t('servicesPage.max') }}</div>
                                            <div class="text-body-2 font-weight-bold">{{ formatNumber(service.max) }}</div>
                                        </div>
                                    </v-col>
                                </v-row>
                                <div class="d-flex gap-2 mt-3" v-if="service.refill || service.cancel">
                                    <v-chip v-if="service.refill" color="success" size="x-small" variant="flat">
                                        <v-icon start size="12">mdi-refresh</v-icon>
                                        {{ $t('services.refill') }}
                                    </v-chip>
                                    <v-chip v-if="service.cancel" color="warning" size="x-small" variant="flat">
                                        <v-icon start size="12">mdi-close</v-icon>
                                        {{ $t('common.cancel') }}
                                    </v-chip>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row v-else>
                    <v-col v-for="n in 6" :key="n" cols="12" sm="6" lg="4">
                        <v-skeleton-loader type="card" height="150"></v-skeleton-loader>
                    </v-col>
                </v-row>

                <div class="text-center mt-8">
                    <v-btn size="large" color="primary" to="/all-services">
                        {{ $t('home.viewServices') }}
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- How It Works -->
        <section class="py-16">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('publicNav.howItWorks') }}</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">{{ $t('home.threeSimpleSteps') }}</h2>
                </div>

                <v-row>
                    <v-col v-for="(step, i) in steps" :key="i" cols="12" md="4">
                        <v-card class="step-card text-center pa-8 h-100" variant="outlined">
                            <v-avatar color="primary" size="64" class="mb-4">
                                <span class="text-h4 font-weight-bold">{{ i + 1 }}</span>
                            </v-avatar>
                            <h3 class="text-h6 font-weight-bold mb-2">{{ $t(step.titleKey) }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(step.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- FAQ Preview -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('publicNav.faq') }}</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">{{ $t('home.commonQuestions') }}</h2>
                </div>

                <v-row justify="center">
                    <v-col cols="12" lg="10">
                        <v-row>
                            <v-col v-for="(faq, i) in faqs" :key="i" cols="12" md="6">
                                <v-card class="faq-card h-100 pa-6" variant="outlined" hover>
                                    <div class="d-flex align-start mb-4">
                                        <v-avatar color="primary" variant="tonal" size="40" class="mr-4 flex-shrink-0">
                                            <v-icon>mdi-help</v-icon>
                                        </v-avatar>
                                        <h3 class="text-subtitle-1 font-weight-bold">{{ $t(faq.questionKey) }}</h3>
                                    </div>
                                    <p class="text-body-2 text-medium-emphasis pl-14">{{ $t(faq.answerKey) }}</p>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <div class="text-center mt-8">
                    <v-btn size="large" variant="outlined" to="/faq">
                        {{ $t('home.viewAllFaqs') }}
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- CTA Section -->
        <section class="py-16">
            <v-container>
                <v-card class="pa-12 text-center cta-card" color="primary">
                    <h2 class="text-h3 font-weight-bold text-white mb-4">{{ $t('home.readyToGrow') }}</h2>
                    <p class="text-h6 text-white mb-8" style="opacity: 0.9;">
                        {{ $t('home.readyToGrowDesc') }}
                    </p>
                    <v-btn size="x-large" color="white" href="/register">
                        <v-icon start>mdi-rocket-launch</v-icon>
                        {{ $t('home.getStartedNow') }}
                    </v-btn>
                </v-card>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '../stores/app'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const store = useAppStore()
const { locale } = useI18n()

// SEO Configuration
const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.home, ...seoConfigsAr.home } : seoConfigs.home
    return config
})
useSeo(seoConfig.value)
const showPassword = ref(false)
const loginLoading = ref(false)

const loginForm = ref({
    email: '',
    password: ''
})

const handleLogin = () => {
    loginLoading.value = true
    // Submit to Laravel login
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = '/login'

    const csrfInput = document.createElement('input')
    csrfInput.type = 'hidden'
    csrfInput.name = '_token'
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content
    form.appendChild(csrfInput)

    const emailInput = document.createElement('input')
    emailInput.type = 'hidden'
    emailInput.name = 'email'
    emailInput.value = loginForm.value.email
    form.appendChild(emailInput)

    const passwordInput = document.createElement('input')
    passwordInput.type = 'hidden'
    passwordInput.name = 'password'
    passwordInput.value = loginForm.value.password
    form.appendChild(passwordInput)

    document.body.appendChild(form)
    form.submit()
}

const featuredServices = computed(() => store.services.slice(0, 6))

const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num)
}

const stats = ref([
    { value: '10K+', labelKey: 'home.activeUsers', icon: 'mdi-account-group', color: 'primary' },
    { value: '500+', labelKey: 'home.servicesCount', icon: 'mdi-view-grid', color: 'success' },
    { value: '1M+', labelKey: 'home.ordersCompleted', icon: 'mdi-check-circle', color: 'warning' },
    { value: '24/7', labelKey: 'home.support', icon: 'mdi-headset', color: 'info' },
])

const platforms = ref([
    { key: 'instagram', name: 'Instagram', icon: 'mdi-instagram', color: '#E4405F' },
    { key: 'tiktok', name: 'TikTok', icon: 'mdi-music-note', color: '#000000' },
    { key: 'youtube', name: 'YouTube', icon: 'mdi-youtube', color: '#FF0000' },
    { key: 'facebook', name: 'Facebook', icon: 'mdi-facebook', color: '#1877F2' },
    { key: 'twitter', name: 'Twitter', icon: 'mdi-twitter', color: '#1DA1F2' },
    { key: 'telegram', name: 'Telegram', icon: 'mdi-telegram', color: '#0088cc' },
])

const steps = ref([
    { titleKey: 'howItWorks.step1Title', descKey: 'howItWorks.step1Desc' },
    { titleKey: 'howItWorks.step2Title', descKey: 'howItWorks.step2Desc' },
    { titleKey: 'home.getResultsTitle', descKey: 'home.getResultsDesc' },
])

const faqs = ref([
    { questionKey: 'home.faq1Question', answerKey: 'home.faq1Answer' },
    { questionKey: 'home.faq2Question', answerKey: 'home.faq2Answer' },
    { questionKey: 'home.faq3Question', answerKey: 'home.faq3Answer' },
    { questionKey: 'home.faq4Question', answerKey: 'home.faq4Answer' },
])
</script>

<style scoped>
.hero-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}

.hero-shapes {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
}

.shape-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    top: -100px;
    right: -100px;
    animation: float 6s ease-in-out infinite;
}

.shape-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    bottom: 10%;
    left: -50px;
    animation: float 8s ease-in-out infinite reverse;
}

.shape-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    top: 50%;
    right: 20%;
    animation: float 7s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.quick-signin-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.stat-card, .platform-card, .service-card, .step-card, .faq-card {
    transition: all 0.3s ease;
    border-radius: 16px !important;
}

.stat-card:hover, .platform-card:hover, .step-card:hover {
    transform: translateY(-4px);
}

.service-card {
    cursor: pointer;
    background: rgb(var(--v-theme-surface));
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    border-color: rgb(var(--v-theme-primary)) !important;
}

.stat-box {
    background: rgba(var(--v-theme-on-surface), 0.05);
}

.faq-card:hover {
    transform: translateY(-4px);
    border-color: rgba(99, 102, 241, 0.5) !important;
}

.service-name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.pl-14 {
    padding-left: 56px;
}

.cta-card {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
}
</style>
