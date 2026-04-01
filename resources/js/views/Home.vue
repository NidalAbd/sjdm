<template>
    <div>
        <!-- Hero -->
        <section class="section-py">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" lg="7">
                        <v-chip color="primary" variant="tonal" size="small" class="mb-4">
                            <v-icon start size="14">mdi-star</v-icon>
                            #1 SMM Panel
                        </v-chip>

                        <h1 class="text-h3 text-sm-h2 font-weight-black mb-4" style="line-height: 1.1;">
                            {{ $t('home.heroTitle') }}
                            <br>
                            <span class="text-gradient">{{ $t('home.heroTitleHighlight') }}</span>
                        </h1>

                        <p class="text-body-1 mb-5" style="opacity: 0.7; line-height: 1.7; max-width: 520px;">
                            {{ $t('home.heroDescription') }}
                        </p>

                        <div class="d-flex flex-wrap ga-2 mb-6">
                            <v-chip v-for="feat in heroFeatures" :key="feat" variant="tonal" color="success" size="small">
                                <v-icon start size="14">mdi-check-circle</v-icon>
                                {{ $t(feat) }}
                            </v-chip>
                        </div>

                        <div class="d-flex flex-wrap ga-3">
                            <v-btn size="large" color="primary" to="/all-services" class="px-6">
                                <v-icon start>mdi-view-grid</v-icon>
                                {{ $t('home.viewServices') }}
                            </v-btn>
                            <v-btn size="large" variant="outlined" href="/register" class="px-6">
                                <v-icon start>mdi-account-plus</v-icon>
                                {{ $t('home.getStarted') }}
                            </v-btn>
                        </div>
                    </v-col>

                    <!-- Quick Sign In -->
                    <v-col cols="12" lg="5" class="d-none d-lg-block">
                        <v-card class="pa-6">
                            <div class="text-center mb-5">
                                <v-avatar color="primary" variant="tonal" size="56" class="mb-3">
                                    <v-icon size="28">mdi-account</v-icon>
                                </v-avatar>
                                <h3 class="text-h6 font-weight-bold">{{ $t('home.quickSignIn') }}</h3>
                                <p class="text-caption" style="opacity: 0.6;">{{ $t('home.accessDashboard') }}</p>
                            </div>
                            <v-form @submit.prevent="handleLogin">
                                <v-text-field v-model="loginForm.email" :label="$t('contact.email')" type="email" prepend-inner-icon="mdi-email" class="mb-3"></v-text-field>
                                <v-text-field v-model="loginForm.password" :label="$t('home.password')" :type="showPassword ? 'text' : 'password'" prepend-inner-icon="mdi-lock" :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="showPassword = !showPassword" class="mb-4"></v-text-field>
                                <v-btn type="submit" color="primary" size="large" block :loading="loginLoading">
                                    <v-icon start>mdi-login</v-icon>
                                    {{ $t('publicNav.signIn') }}
                                </v-btn>
                            </v-form>
                            <div class="text-center mt-4">
                                <span class="text-body-2" style="opacity: 0.5;">{{ $t('home.noAccount') }}</span>
                                <a href="/register" class="text-primary text-decoration-none font-weight-bold ml-1">{{ $t('publicNav.signUp') }}</a>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Stats Bar -->
        <section style="border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity)); border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));">
            <v-container class="py-6">
                <v-row>
                    <v-col v-for="(stat, i) in homeStats" :key="i" cols="6" md="3">
                        <div class="text-center">
                            <div class="text-h4 font-weight-black text-gradient stat-value">{{ stat.value }}</div>
                            <div class="text-caption" style="opacity: 0.5;">{{ $t(stat.labelKey) }}</div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Platforms -->
        <section class="section-py">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.platformsWeSupport') }}</h2>
                    <p class="text-body-2" style="opacity: 0.5;">{{ $t('home.platformsDesc') }}</p>
                </div>
                <v-row justify="center">
                    <v-col v-for="platform in platforms" :key="platform.name" cols="4" sm="2">
                        <v-card class="text-center pa-4 hover-lift" :to="`/all-services?platform=${platform.key}`">
                            <v-icon :color="platform.color" size="36" class="mb-2">{{ platform.icon }}</v-icon>
                            <div class="text-caption font-weight-bold">{{ platform.name }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Featured Services -->
        <section class="section-py" style="background: rgb(var(--v-theme-surface-variant));">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.popularServices') }}</h2>
                    <p class="text-body-2" style="opacity: 0.5;">{{ $t('home.popularServicesDesc') }}</p>
                </div>

                <v-row v-if="!store.loading">
                    <v-col v-for="service in featuredServices" :key="service.service_id" cols="12" sm="6" lg="4">
                        <ServiceCard :service="service" />
                    </v-col>
                </v-row>
                <v-row v-else>
                    <v-col v-for="n in 6" :key="n" cols="12" sm="6" lg="4">
                        <v-skeleton-loader type="card" height="160"></v-skeleton-loader>
                    </v-col>
                </v-row>

                <div class="text-center mt-8">
                    <v-btn size="large" color="primary" to="/all-services" class="px-8">
                        {{ $t('home.viewServices') }}
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- How It Works -->
        <section class="section-py">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h4 font-weight-bold mb-2">{{ $t('home.threeSimpleSteps') }}</h2>
                </div>
                <v-row justify="center">
                    <v-col v-for="(step, i) in steps" :key="i" cols="12" md="4">
                        <v-card class="text-center pa-8 h-100">
                            <v-avatar color="primary" size="56" class="mb-4">
                                <span class="text-h5 font-weight-bold">{{ i + 1 }}</span>
                            </v-avatar>
                            <h3 class="text-h6 font-weight-bold mb-2">{{ $t(step.titleKey) }}</h3>
                            <p class="text-body-2" style="opacity: 0.6;">{{ $t(step.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- FAQ -->
        <section class="section-py" style="background: rgb(var(--v-theme-surface-variant));">
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
        <section class="section-py">
            <v-container>
                <v-card class="pa-8 pa-md-12 text-center" variant="flat" :style="{ background: store.gradientStyle }">
                    <h2 class="text-h4 text-md-h3 font-weight-bold text-white mb-3">{{ $t('home.readyToGrow') }}</h2>
                    <p class="text-body-1 text-white mb-6" style="opacity: 0.8; max-width: 480px; margin: 0 auto;">
                        {{ $t('home.readyToGrowDesc') }}
                    </p>
                    <v-btn size="large" color="white" href="/register" class="px-8">
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
import ServiceCard from '../components/ServiceCard.vue'

const store = useAppStore()
const showPassword = ref(false)
const loginLoading = ref(false)
const loginForm = ref({ email: '', password: '' })

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
    { value: '10K+', labelKey: 'home.activeUsers' },
    { value: '500+', labelKey: 'home.servicesCount' },
    { value: '1M+', labelKey: 'home.ordersCompleted' },
    { value: '24/7', labelKey: 'home.support' },
]

const platforms = [
    { key: 'instagram', name: 'Instagram', icon: 'mdi-instagram', color: '#E4405F' },
    { key: 'tiktok', name: 'TikTok', icon: 'mdi-music-note', color: '#ff0050' },
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
