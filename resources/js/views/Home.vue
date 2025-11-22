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
                            Grow Your <span class="text-primary">Social Media</span> Instantly
                        </h1>

                        <p class="text-h6 text-medium-emphasis mb-6" style="line-height: 1.7;">
                            The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more. Starting at just <strong class="text-primary">$0.01</strong>
                        </p>

                        <div class="d-flex flex-wrap ga-3 mb-8">
                            <div class="d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                <span>Instant Delivery</span>
                            </div>
                            <div class="d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                <span>24/7 Support</span>
                            </div>
                            <div class="d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                <span>Cheapest Prices</span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap ga-3">
                            <v-btn size="x-large" color="primary" to="/all-services" class="px-8">
                                <v-icon start>mdi-view-grid</v-icon>
                                View All Services
                            </v-btn>
                            <v-btn size="x-large" variant="outlined" href="/register" class="px-8">
                                <v-icon start>mdi-account-plus</v-icon>
                                Get Started Free
                            </v-btn>
                        </div>
                    </v-col>

                    <!-- Quick Sign In Card -->
                    <v-col cols="12" lg="5">
                        <v-card class="pa-6 quick-signin-card">
                            <div class="text-center mb-6">
                                <v-icon size="48" color="primary" class="mb-2">mdi-account-circle</v-icon>
                                <h3 class="text-h5 font-weight-bold">Quick Sign In</h3>
                                <p class="text-body-2 text-medium-emphasis">Access your dashboard instantly</p>
                            </div>

                            <v-form @submit.prevent="handleLogin">
                                <v-text-field
                                    v-model="loginForm.email"
                                    label="Email Address"
                                    type="email"
                                    prepend-inner-icon="mdi-email"
                                    class="mb-2"
                                    required
                                ></v-text-field>

                                <v-text-field
                                    v-model="loginForm.password"
                                    label="Password"
                                    :type="showPassword ? 'text' : 'password'"
                                    prepend-inner-icon="mdi-lock"
                                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="showPassword = !showPassword"
                                    class="mb-4"
                                    required
                                ></v-text-field>

                                <v-btn type="submit" color="primary" size="large" block :loading="loginLoading">
                                    <v-icon start>mdi-login</v-icon>
                                    Sign In
                                </v-btn>
                            </v-form>

                            <div class="text-center mt-4">
                                <span class="text-body-2 text-medium-emphasis">Don't have an account?</span>
                                <a href="/register" class="text-primary text-decoration-none ml-1 font-weight-medium">Sign Up</a>
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
                            <div class="text-body-2 text-medium-emphasis">{{ stat.label }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Platform Cards -->
        <section class="py-16">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">Platforms</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">All Social Media Platforms</h2>
                    <p class="text-h6 text-medium-emphasis">Boost your presence on any platform</p>
                </div>

                <v-row>
                    <v-col v-for="platform in platforms" :key="platform.name" cols="6" sm="4" md="2">
                        <v-card class="platform-card text-center pa-4" hover :to="`/all-services?platform=${platform.name}`">
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
                    <v-chip color="primary" variant="tonal" class="mb-4">Services</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">Popular Services</h2>
                    <p class="text-h6 text-medium-emphasis">Most ordered services by our customers</p>
                </div>

                <v-row v-if="!store.loading">
                    <v-col v-for="service in featuredServices" :key="service.service_id" cols="12" sm="6" lg="4">
                        <v-card class="service-card h-100" hover :to="`/service/${service.service_id}`">
                            <v-card-text class="pa-6">
                                <div class="d-flex justify-space-between align-start mb-4">
                                    <v-chip size="small" color="primary" variant="tonal">
                                        {{ service.category_en }}
                                    </v-chip>
                                    <v-chip size="small" color="success">
                                        ${{ service.rate }}/1K
                                    </v-chip>
                                </div>
                                <h3 class="text-subtitle-1 font-weight-bold mb-2 service-name">
                                    {{ service.name_en }}
                                </h3>
                                <div class="d-flex justify-space-between text-caption text-medium-emphasis">
                                    <span>Min: {{ service.min }}</span>
                                    <span>Max: {{ service.max }}</span>
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
                        View All Services
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- How It Works -->
        <section class="py-16">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">How It Works</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">3 Simple Steps</h2>
                </div>

                <v-row>
                    <v-col v-for="(step, i) in steps" :key="i" cols="12" md="4">
                        <v-card class="step-card text-center pa-8 h-100" variant="outlined">
                            <v-avatar color="primary" size="64" class="mb-4">
                                <span class="text-h4 font-weight-bold">{{ i + 1 }}</span>
                            </v-avatar>
                            <h3 class="text-h6 font-weight-bold mb-2">{{ step.title }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ step.description }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- FAQ Preview -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center mb-12">
                    <v-chip color="primary" variant="tonal" class="mb-4">FAQ</v-chip>
                    <h2 class="text-h3 font-weight-bold mb-4">Common Questions</h2>
                </div>

                <v-row justify="center">
                    <v-col cols="12" lg="10">
                        <v-row>
                            <v-col v-for="(faq, i) in faqs.slice(0, 4)" :key="i" cols="12" md="6">
                                <v-card class="faq-card h-100 pa-6" variant="outlined" hover>
                                    <div class="d-flex align-start mb-4">
                                        <v-avatar color="primary" variant="tonal" size="40" class="mr-4 flex-shrink-0">
                                            <v-icon>mdi-help</v-icon>
                                        </v-avatar>
                                        <h3 class="text-subtitle-1 font-weight-bold">{{ faq.question }}</h3>
                                    </div>
                                    <p class="text-body-2 text-medium-emphasis pl-14">{{ faq.answer }}</p>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <div class="text-center mt-8">
                    <v-btn size="large" variant="outlined" to="/faq">
                        View All FAQs
                        <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- CTA Section -->
        <section class="py-16">
            <v-container>
                <v-card class="pa-12 text-center cta-card" color="primary">
                    <h2 class="text-h3 font-weight-bold text-white mb-4">Ready to Grow?</h2>
                    <p class="text-h6 text-white mb-8" style="opacity: 0.9;">
                        Join thousands of satisfied customers and boost your social media today
                    </p>
                    <v-btn size="x-large" color="white" href="/register">
                        <v-icon start>mdi-rocket-launch</v-icon>
                        Get Started Now
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

const stats = ref([
    { value: '10K+', label: 'Active Users', icon: 'mdi-account-group', color: 'primary' },
    { value: '500+', label: 'Services', icon: 'mdi-view-grid', color: 'success' },
    { value: '1M+', label: 'Orders Completed', icon: 'mdi-check-circle', color: 'warning' },
    { value: '24/7', label: 'Support', icon: 'mdi-headset', color: 'info' },
])

const platforms = ref([
    { name: 'Instagram', icon: 'mdi-instagram', color: '#E4405F' },
    { name: 'TikTok', icon: 'mdi-music-note', color: '#000000' },
    { name: 'YouTube', icon: 'mdi-youtube', color: '#FF0000' },
    { name: 'Facebook', icon: 'mdi-facebook', color: '#1877F2' },
    { name: 'Twitter', icon: 'mdi-twitter', color: '#1DA1F2' },
    { name: 'Telegram', icon: 'mdi-telegram', color: '#0088cc' },
])

const steps = ref([
    { title: 'Create Account', description: 'Sign up for free and add funds to your account using your preferred payment method.' },
    { title: 'Choose Service', description: 'Browse our wide range of services and select the one that fits your needs.' },
    { title: 'Get Results', description: 'Place your order and watch your social media grow instantly!' },
])

const faqs = ref([
    { question: 'How fast is the delivery?', answer: 'Most orders start within 0-15 minutes and complete within a few hours depending on the quantity.' },
    { question: 'Is it safe to use?', answer: 'Yes! We use safe methods and never ask for your password. Your account security is our priority.' },
    { question: 'What payment methods do you accept?', answer: 'We accept credit cards, PayPal, cryptocurrency, and various local payment methods.' },
    { question: 'Do you offer refunds?', answer: 'Yes, we offer full refunds if we fail to deliver your order. Contact our 24/7 support for assistance.' },
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
}

.stat-card:hover, .platform-card:hover, .step-card:hover {
    transform: translateY(-4px);
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
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
