<template>
    <div>
        <!-- Hero -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center">
                    <v-chip color="primary" variant="tonal" class="mb-4">Guide</v-chip>
                    <h1 class="text-h2 font-weight-bold mb-4">How It Works</h1>
                    <p class="text-h6 text-medium-emphasis mx-auto" style="max-width: 600px;">
                        Get started in just 3 simple steps
                    </p>
                </div>
            </v-container>
        </section>

        <!-- Steps -->
        <section class="py-16">
            <v-container>
                <v-row>
                    <v-col v-for="(step, i) in steps" :key="i" cols="12" md="4">
                        <v-card class="pa-8 text-center h-100 step-card" variant="outlined">
                            <v-avatar color="primary" size="80" class="mb-6">
                                <span class="text-h3 font-weight-bold">{{ i + 1 }}</span>
                            </v-avatar>
                            <v-icon :color="step.color" size="48" class="mb-4">{{ step.icon }}</v-icon>
                            <h3 class="text-h5 font-weight-bold mb-4">{{ step.title }}</h3>
                            <p class="text-body-1 text-medium-emphasis">{{ step.description }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Features -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center mb-12">
                    <h2 class="text-h3 font-weight-bold mb-4">Why Choose Us</h2>
                </div>
                <v-row>
                    <v-col v-for="feature in features" :key="feature.title" cols="12" sm="6" lg="3">
                        <v-card class="pa-6 text-center h-100" variant="flat">
                            <v-icon :color="feature.color" size="48" class="mb-4">{{ feature.icon }}</v-icon>
                            <h4 class="text-h6 font-weight-bold mb-2">{{ feature.title }}</h4>
                            <p class="text-body-2 text-medium-emphasis">{{ feature.description }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="py-16">
            <v-container>
                <v-card class="pa-12 text-center" color="primary">
                    <h2 class="text-h3 font-weight-bold text-white mb-4">Ready to Get Started?</h2>
                    <p class="text-h6 text-white mb-8" style="opacity: 0.9;">Create your free account and boost your social media today</p>
                    <div class="d-flex justify-center ga-4 flex-wrap">
                        <v-btn size="x-large" color="white" href="/register">
                            <v-icon start>mdi-account-plus</v-icon>
                            Sign Up Free
                        </v-btn>
                        <v-btn size="x-large" variant="outlined" color="white" to="/all-services">
                            Browse Services
                        </v-btn>
                    </div>
                </v-card>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const steps = ref([
    {
        title: 'Create Account',
        description: 'Sign up for free in seconds. No credit card required to get started.',
        icon: 'mdi-account-plus',
        color: 'primary'
    },
    {
        title: 'Add Funds & Order',
        description: 'Add funds using your preferred payment method and place your order.',
        icon: 'mdi-cart',
        color: 'success'
    },
    {
        title: 'Watch Growth',
        description: 'Sit back and watch your social media grow with real engagement.',
        icon: 'mdi-chart-line',
        color: 'info'
    },
])

const features = ref([
    { title: 'Instant Start', icon: 'mdi-lightning-bolt', color: 'warning', description: 'Orders start within minutes' },
    { title: 'Secure Payments', icon: 'mdi-shield-check', color: 'success', description: 'SSL encrypted transactions' },
    { title: '24/7 Support', icon: 'mdi-headset', color: 'info', description: 'Always here to help' },
    { title: 'Best Prices', icon: 'mdi-tag', color: 'error', description: 'Cheapest rates guaranteed' },
])
</script>

<style scoped>
.step-card {
    transition: all 0.3s ease;
}

.step-card:hover {
    transform: translateY(-8px);
    border-color: rgba(99, 102, 241, 0.5) !important;
}
</style>
