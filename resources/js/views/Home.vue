<template>
    <div>
        <!-- Hero -->
        <section class="hero hero-bold">
            <div class="hero-decor" aria-hidden="true">
                <span v-for="(p, i) in platforms" :key="p.key" class="hero-decor-icon" :style="decorStyle(i)">
                    <v-icon :color="p.color" size="24">{{ p.icon }}</v-icon>
                </span>
            </div>
            <v-container>
                <v-row align="center">
                    <v-col cols="12" lg="7">
                        <div class="hero-badge hero-badge-lg">
                            <v-icon size="16">mdi-star</v-icon>
                            #1 SMM Panel
                        </div>
                        <h1 class="heading-xl hero-title">
                            {{ $t('home.heroTitle') }}<br>
                            <span class="text-gradient">{{ $t('home.heroTitleHighlight') }}</span>
                        </h1>
                        <p class="hero-desc">{{ $t('home.heroDescription') }}</p>
                        <div class="features-row">
                            <span class="feature-chip" v-for="f in heroFeats" :key="f">
                                <v-icon size="12">mdi-check-circle</v-icon>
                                {{ $t(f) }}
                            </span>
                        </div>
                        <div class="hero-actions">
                            <v-btn size="large" color="primary" to="/all-services" prepend-icon="mdi-view-grid">{{ $t('home.viewServices') }}</v-btn>
                            <v-btn size="large" variant="outlined" href="/register" prepend-icon="mdi-account-plus">{{ $t('home.getStarted') }}</v-btn>
                        </div>
                    </v-col>

                    <v-col cols="12" lg="5" class="d-none d-lg-block">
                        <div class="card hero-login-card" style="padding:28px;">
                            <div style="text-align:center;margin-bottom:20px;">
                                <div style="width:52px;height:52px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:rgba(var(--v-theme-primary),0.1);margin-bottom:10px;">
                                    <v-icon size="24" color="primary">mdi-account</v-icon>
                                </div>
                                <h3 class="heading-md">{{ $t('home.quickSignIn') }}</h3>
                                <p style="font-size:0.8rem;opacity:0.4;">{{ $t('home.accessDashboard') }}</p>
                            </div>
                            <form @submit.prevent="handleLogin">
                                <v-text-field v-model="loginForm.email" :label="$t('contact.email')" type="email" prepend-inner-icon="mdi-email" class="mb-3"></v-text-field>
                                <v-text-field v-model="loginForm.password" :label="$t('home.password')" :type="showPw ? 'text' : 'password'" prepend-inner-icon="mdi-lock" :append-inner-icon="showPw ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="showPw = !showPw" class="mb-4"></v-text-field>
                                <v-btn type="submit" color="primary" size="large" block :loading="loginLoading" prepend-icon="mdi-login">{{ $t('publicNav.signIn') }}</v-btn>
                            </form>
                            <p style="text-align:center;margin-top:14px;font-size:0.8rem;opacity:0.4;">{{ $t('home.noAccount') }} <a href="/register" style="color:rgb(var(--v-theme-primary));text-decoration:none;font-weight:600;">{{ $t('publicNav.signUp') }}</a></p>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Stats -->
        <div class="stats-bar">
            <div v-for="s in homeStats" :key="s.label" class="stats-bar-item">
                <div class="stats-bar-value text-gradient">{{ s.value }}</div>
                <div class="stats-bar-label">{{ $t(s.label) }}</div>
            </div>
        </div>

        <!-- Platforms -->
        <section class="section">
            <v-container>
                <div class="section-header">
                    <h2 class="heading-lg">{{ $t('home.platformsWeSupport') }}</h2>
                    <p>{{ $t('home.platformsDesc') }}</p>
                </div>
                <div class="platforms-grid">
                    <router-link v-for="p in platforms" :key="p.key" :to="`/all-services?platform=${p.key}`" class="platform-card">
                        <v-icon :color="p.color" size="32">{{ p.icon }}</v-icon>
                        <span class="platform-card-name">{{ p.name }}</span>
                    </router-link>
                </div>
            </v-container>
        </section>

        <!-- Featured Services -->
        <section class="section section-alt">
            <v-container>
                <div class="section-header">
                    <h2 class="heading-lg">{{ $t('home.popularServices') }}</h2>
                    <p>{{ $t('home.popularServicesDesc') }}</p>
                </div>
                <div v-if="!store.loading" class="services-grid">
                    <router-link v-for="svc in featuredServices" :key="svc.service_id" :to="`/service/${svc.service_id}`" class="svc">
                        <div class="svc-top">
                            <span class="svc-id">#{{ svc.service_id }}</span>
                            <span class="svc-price">${{ formatPrice(svc.rate) }}<small>/1K</small></span>
                        </div>
                        <div class="svc-name">{{ svc.name || svc.name_en }}</div>
                        <div class="svc-cat"><v-icon size="12">mdi-tag</v-icon>{{ svc.category || svc.category_en }}</div>
                        <div class="svc-bottom">
                            <span class="svc-range">{{ fmtNum(svc.min) }} - {{ fmtNum(svc.max) }}</span>
                            <div class="svc-badges">
                                <span v-if="svc.refill" class="svc-badge svc-badge-ok">Refill</span>
                                <span v-if="svc.cancel" class="svc-badge svc-badge-warn">Cancel</span>
                            </div>
                        </div>
                    </router-link>
                </div>
                <div v-else style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
                    <v-skeleton-loader v-for="n in 6" :key="n" type="card" height="160"></v-skeleton-loader>
                </div>
                <div style="text-align:center;margin-top:40px;">
                    <v-btn size="large" color="primary" to="/all-services" append-icon="mdi-arrow-right">{{ $t('home.viewServices') }}</v-btn>
                </div>
            </v-container>
        </section>

        <!-- How It Works -->
        <section class="section">
            <v-container>
                <div class="section-header">
                    <h2 class="heading-lg">{{ $t('home.threeSimpleSteps') }}</h2>
                </div>
                <div class="steps-grid">
                    <div v-for="(step, i) in steps" :key="i" class="card step-card">
                        <div class="step-num">{{ i + 1 }}</div>
                        <div class="step-title">{{ $t(step.t) }}</div>
                        <div class="step-desc">{{ $t(step.d) }}</div>
                    </div>
                </div>
            </v-container>
        </section>

        <!-- FAQ -->
        <section class="section section-alt">
            <v-container>
                <div class="section-header">
                    <h2 class="heading-lg">{{ $t('home.commonQuestions') }}</h2>
                </div>
                <v-row justify="center">
                    <v-col cols="12" lg="8">
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel v-for="(faq, i) in faqs" :key="i" :title="$t(faq.q)" :text="$t(faq.a)"></v-expansion-panel>
                        </v-expansion-panels>
                    </v-col>
                </v-row>
                <div style="text-align:center;margin-top:28px;">
                    <v-btn variant="outlined" to="/faq" append-icon="mdi-arrow-right">{{ $t('home.viewAllFaqs') }}</v-btn>
                </div>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="section">
            <v-container>
                <CtaCard :title="$t('home.readyToGrow')" :description="$t('home.readyToGrowDesc')">
                    <v-btn size="large" color="white" href="/register" prepend-icon="mdi-rocket-launch">{{ $t('home.getStartedNow') }}</v-btn>
                </CtaCard>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAppStore } from '../stores/app'
import CtaCard from '../components/public/CtaCard.vue'

const store = useAppStore()
const showPw = ref(false)
const loginLoading = ref(false)
const loginForm = ref({ email: '', password: '' })

const handleLogin = () => {
    loginLoading.value = true
    const f = document.createElement('form'); f.method = 'POST'; f.action = '/login'
    const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = document.querySelector('meta[name="csrf-token"]').content; f.appendChild(c)
    const e = document.createElement('input'); e.type = 'hidden'; e.name = 'email'; e.value = loginForm.value.email; f.appendChild(e)
    const p = document.createElement('input'); p.type = 'hidden'; p.name = 'password'; p.value = loginForm.value.password; f.appendChild(p)
    document.body.appendChild(f); f.submit()
}

const featuredServices = computed(() => store.services.slice(0, 6))
const fmtNum = (n) => new Intl.NumberFormat().format(n)
const formatPrice = (r) => { const n = Number(r); if (n < 0.001) return n.toFixed(7); if (n < 0.01) return n.toFixed(5); if (n < 1) return n.toFixed(4); return n.toFixed(2) }

const heroFeats = ['home.instantDelivery', 'home.support247', 'home.bestPrices']
const homeStats = [
    { value: '10K+', label: 'home.activeUsers' },
    { value: '500+', label: 'home.servicesCount' },
    { value: '1M+', label: 'home.ordersCompleted' },
    { value: '24/7', label: 'home.support' },
]
const platforms = [
    { key: 'instagram', name: 'Instagram', icon: 'mdi-instagram', color: '#E4405F' },
    { key: 'tiktok', name: 'TikTok', icon: 'mdi-music-note', color: '#ff0050' },
    { key: 'youtube', name: 'YouTube', icon: 'mdi-youtube', color: '#FF0000' },
    { key: 'facebook', name: 'Facebook', icon: 'mdi-facebook', color: '#1877F2' },
    { key: 'twitter', name: 'Twitter', icon: 'mdi-twitter', color: '#1DA1F2' },
    { key: 'telegram', name: 'Telegram', icon: 'mdi-telegram', color: '#0088cc' },
]

// Scattered positions for the hero's floating platform-icon decoration (desktop only, see .hero-decor CSS).
// Kept within the clear strip above the headline/login-card content (top < 15%) so icons
// never render behind the opaque login card.
const decorPositions = [
    { top: '2%', left: '8%', rotate: '-8deg' },
    { top: '9%', left: '23%', rotate: '6deg' },
    { top: '0%', left: '39%', rotate: '-5deg' },
    { top: '11%', left: '54%', rotate: '9deg' },
    { top: '3%', left: '70%', rotate: '-6deg' },
    { top: '10%', left: '86%', rotate: '5deg' },
]
const decorStyle = (i) => {
    const p = decorPositions[i % decorPositions.length]
    return { top: p.top, left: p.left, transform: `rotate(${p.rotate})` }
}
const steps = [
    { t: 'howItWorks.step1Title', d: 'howItWorks.step1Desc' },
    { t: 'howItWorks.step2Title', d: 'howItWorks.step2Desc' },
    { t: 'home.getResultsTitle', d: 'home.getResultsDesc' },
]
const faqs = [
    { q: 'home.faq1Question', a: 'home.faq1Answer' },
    { q: 'home.faq2Question', a: 'home.faq2Answer' },
    { q: 'home.faq3Question', a: 'home.faq3Answer' },
    { q: 'home.faq4Question', a: 'home.faq4Answer' },
]
</script>
