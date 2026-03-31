<template>
    <div>
        <!-- Hero -->
        <section class="section-padding" :style="{ background: heroBg }">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" lg="7">
                        <v-chip color="primary" variant="tonal" size="small" class="mb-3">
                            <v-icon start size="14">mdi-star</v-icon>
                            #1 SMM Panel
                        </v-chip>

                        <h1 class="text-h3 text-md-h2 font-weight-black mb-3" style="line-height: 1.15;">
                            {{ $t('home.heroTitle') }}
                            <span class="gradient-text">{{ $t('home.heroTitleHighlight') }}</span>
                        </h1>

                        <p class="text-body-1 text-medium-emphasis mb-5" style="line-height: 1.7; max-width: 560px;">
                            {{ $t('home.heroDescription') }}
                        </p>

                        <div class="d-flex flex-wrap ga-2 mb-5">
                            <v-chip v-for="feat in heroFeatures" :key="feat" variant="tonal" color="success" size="small">
                                <v-icon start size="14">mdi-check-circle</v-icon>
                                {{ $t(feat) }}
                            </v-chip>
                        </div>

                        <div class="d-flex flex-wrap ga-2">
                            <v-btn size="large" color="primary" to="/all-services">
                                <v-icon start>mdi-view-grid</v-icon>
                                {{ $t('home.viewServices') }}
                            </v-btn>
                            <v-btn size="large" variant="outlined" href="/register">
                                <v-icon start>mdi-account-plus</v-icon>
                                {{ $t('home.getStarted') }}
                            </v-btn>
                        </div>
                    </v-col>

                    <!-- Quick Sign In -->
                    <v-col cols="12" lg="5" class="d-none d-lg-block">
                        <v-card variant="outlined" class="pa-5">
                            <div class="text-center mb-4">
                                <v-icon size="36" color="primary" class="mb-1">mdi-account-circle</v-icon>
                                <h3 class="text-subtitle-1 font-weight-bold">{{ $t('home.quickSignIn') }}</h3>
                                <p class="text-caption text-medium-emphasis">{{ $t('home.accessDashboard') }}</p>
                            </div>
                            <v-form @submit.prevent="handleLogin">
                                <v-text-field v-model="loginForm.email" :label="$t('contact.email')" type="email" prepend-inner-icon="mdi-email" class="mb-2" required></v-text-field>
                                <v-text-field v-model="loginForm.password" :label="$t('home.password')" :type="showPassword ? 'text' : 'password'" prepend-inner-icon="mdi-lock" :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="showPassword = !showPassword" class="mb-3" required></v-text-field>
                                <v-btn type="submit" color="primary" block :loading="loginLoading">
                                    <v-icon start>mdi-login</v-icon>
                                    {{ $t('publicNav.signIn') }}
                                </v-btn>
                            </v-form>
                            <div class="text-center mt-3">
                                <span class="text-caption text-medium-emphasis">{{ $t('home.noAccount') }}</span>
                                <a href="/register" class="text-primary text-decoration-none text-caption font-weight-medium ml-1">{{ $t('publicNav.signUp') }}</a>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Stats -->
        <section class="py-8">
            <v-container>
                <v-row>
                    <v-col v-for="(stat, i) in homeStats" :key="i" cols="6" md="3">
                        <div class="text-center">
                            <v-icon :color="stat.color" size="28" class="mb-1">{{ stat.icon }}</v-icon>
                            <div class="text-h5 font-weight-bold stat-value">{{ stat.value }}</div>
                            <div class="text-caption text-medium-emphasis">{{ $t(stat.labelKey) }}</div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Platforms -->
        <section class="section-padding">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.platformsWeSupport') }}</h2>
                    <p class="text-body-2 text-medium-emphasis">{{ $t('home.platformsDesc') }}</p>
                </div>
                <v-row justify="center">
                    <v-col v-for="platform in platforms" :key="platform.name" cols="4" sm="2">
                        <v-card variant="outlined" class="text-center pa-3 hover-lift" :to="`/all-services?platform=${platform.key}`">
                            <v-icon :color="platform.color" size="32" class="mb-1">{{ platform.icon }}</v-icon>
                            <div class="text-caption font-weight-medium">{{ platform.name }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Featured Services -->
        <section class="section-padding bg-surface-variant">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.popularServices') }}</h2>
                    <p class="text-body-2 text-medium-emphasis">{{ $t('home.popularServicesDesc') }}</p>
                </div>

                <v-row v-if="!store.loading">
                    <v-col v-for="service in featuredServices" :key="service.service_id" cols="12" sm="6" lg="4">
                        <v-card variant="outlined" class="h-100 hover-lift" :to="`/service/${service.service_id}`">
                            <v-card-text class="pa-4">
                                <div class="d-flex justify-space-between align-center mb-3">
                                    <v-chip size="x-small" color="primary" variant="tonal">#{{ service.service_id }}</v-chip>
                                    <span class="text-subtitle-2 font-weight-bold text-success">${{ Number(service.rate).toFixed(4) }}/1K</span>
                                </div>
                                <h3 class="text-body-2 font-weight-bold mb-2 service-name">
                                    {{ service.name || service.name_en }}
                                </h3>
                                <div class="text-caption text-primary mb-3">
                                    <v-icon size="12" class="mr-1">mdi-tag</v-icon>
                                    {{ service.category || service.category_en }}
                                </div>
                                <div class="d-flex ga-2">
                                    <v-chip size="x-small" variant="tonal">Min: {{ formatNumber(service.min) }}</v-chip>
                                    <v-chip size="x-small" variant="tonal">Max: {{ formatNumber(service.max) }}</v-chip>
                                    <v-chip v-if="service.refill" size="x-small" variant="tonal" color="success">
                                        <v-icon start size="10">mdi-refresh</v-icon>Refill
                                    </v-chip>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row v-else>
                    <v-col v-for="n in 6" :key="n" cols="12" sm="6" lg="4">
                        <v-skeleton-loader type="card" height="140"></v-skeleton-loader>
                    </v-col>
                </v-row>

                <div class="text-center mt-6">
                    <v-btn color="primary" to="/all-services">
                        {{ $t('home.viewServices') }}
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- How It Works -->
        <section class="section-padding">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.threeSimpleSteps') }}</h2>
                </div>
                <v-row justify="center">
                    <v-col v-for="(step, i) in steps" :key="i" cols="12" md="4">
                        <v-card variant="outlined" class="text-center pa-6 h-100">
                            <v-avatar color="primary" variant="tonal" size="48" class="mb-3">
                                <span class="text-h6 font-weight-bold">{{ i + 1 }}</span>
                            </v-avatar>
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">{{ $t(step.titleKey) }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(step.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- FAQ Preview -->
        <section class="section-padding bg-surface-variant">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.commonQuestions') }}</h2>
                </div>
                <v-row justify="center">
                    <v-col cols="12" lg="8">
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel v-for="(faq, i) in faqs" :key="i" :title="$t(faq.questionKey)" :text="$t(faq.answerKey)"></v-expansion-panel>
                        </v-expansion-panels>
                    </v-col>
                </v-row>
                <div class="text-center mt-6">
                    <v-btn variant="outlined" to="/faq">
                        {{ $t('home.viewAllFaqs') }}
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="section-padding">
            <v-container>
                <v-card class="pa-8 pa-md-12 text-center" variant="flat" :style="{ background: store.gradientStyle }">
                    <h2 class="text-h4 text-md-h3 font-weight-bold text-white mb-3">{{ $t('home.readyToGrow') }}</h2>
                    <p class="text-body-1 text-white mb-6" style="opacity: 0.85; max-width: 500px; margin: 0 auto;">
                        {{ $t('home.readyToGrowDesc') }}
                    </p>
                    <v-btn size="large" color="white" href="/register">
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
import { useAppStore } from '../stores/app'

const store = useAppStore()
const showPassword = ref(false)
const loginLoading = ref(false)
const loginForm = ref({ email: '', password: '' })

const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    return store.isDark
        ? `linear-gradient(135deg, rgba(${hexToRgb(p)}, 0.08) 0%, transparent 60%)`
        : `linear-gradient(135deg, rgba(${hexToRgb(p)}, 0.06) 0%, transparent 60%)`
})

function hexToRgb(hex) {
    const r = parseInt(hex.slice(1, 3), 16)
    const g = parseInt(hex.slice(3, 5), 16)
    const b = parseInt(hex.slice(5, 7), 16)
    return `${r},${g},${b}`
}

const handleLogin = () => {
    loginLoading.value = true
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = '/login'
    const csrf = document.createElement('input')
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = document.querySelector('meta[name="csrf-token"]').content
    form.appendChild(csrf)
    const email = document.createElement('input')
    email.type = 'hidden'; email.name = 'email'; email.value = loginForm.value.email
    form.appendChild(email)
    const pw = document.createElement('input')
    pw.type = 'hidden'; pw.name = 'password'; pw.value = loginForm.value.password
    form.appendChild(pw)
    document.body.appendChild(form)
    form.submit()
}

const featuredServices = computed(() => store.services.slice(0, 6))
const formatNumber = (num) => new Intl.NumberFormat().format(num)

const heroFeatures = ['home.instantDelivery', 'home.support247', 'home.bestPrices']

const homeStats = [
    { value: '10K+', labelKey: 'home.activeUsers', icon: 'mdi-account-group', color: 'primary' },
    { value: '500+', labelKey: 'home.servicesCount', icon: 'mdi-view-grid', color: 'success' },
    { value: '1M+', labelKey: 'home.ordersCompleted', icon: 'mdi-check-circle', color: 'warning' },
    { value: '24/7', labelKey: 'home.support', icon: 'mdi-headset', color: 'info' },
]

const platforms = [
    { key: 'instagram', name: 'Instagram', icon: 'mdi-instagram', color: '#E4405F' },
    { key: 'tiktok', name: 'TikTok', icon: 'mdi-music-note', color: '#000000' },
    { key: 'youtube', name: 'YouTube', icon: 'mdi-youtube', color: '#FF0000' },
    { key: 'facebook', name: 'Facebook', icon: 'mdi-facebook', color: '#1877F2' },
    { key: 'twitter', name: 'Twitter', icon: 'mdi-twitter', color: '#1DA1F2' },
    { key: 'telegram', name: 'Telegram', icon: 'mdi-telegram', color: '#0088cc' },
]

const steps = [
    { titleKey: 'howItWorks.step1Title', descKey: 'howItWorks.step1Desc' },
    { titleKey: 'howItWorks.step2Title', descKey: 'howItWorks.step2Desc' },
    { titleKey: 'home.getResultsTitle', descKey: 'home.getResultsDesc' },
]

const faqs = [
    { questionKey: 'home.faq1Question', answerKey: 'home.faq1Answer' },
    { questionKey: 'home.faq2Question', answerKey: 'home.faq2Answer' },
    { questionKey: 'home.faq3Question', answerKey: 'home.faq3Answer' },
    { questionKey: 'home.faq4Question', answerKey: 'home.faq4Answer' },
]
</script>

<style scoped>
.service-name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.bg-surface-variant {
    background: rgb(var(--v-theme-surface-variant));
}
</style>
