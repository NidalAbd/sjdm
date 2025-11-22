<template>
    <div>
        <!-- Hero -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center">
                    <v-chip color="primary" variant="tonal" class="mb-4">About Us</v-chip>
                    <h1 class="text-h2 font-weight-bold mb-4">Who We Are</h1>
                    <p class="text-h6 text-medium-emphasis mx-auto" style="max-width: 700px;">
                        We are a leading SMM panel providing high-quality social media marketing services at the most affordable prices.
                    </p>
                </div>
            </v-container>
        </section>

        <!-- Story -->
        <section class="py-16">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" md="6">
                        <h2 class="text-h3 font-weight-bold mb-4">Our Story</h2>
                        <p class="text-body-1 text-medium-emphasis mb-4">
                            Founded with a mission to democratize social media growth, we've helped thousands of businesses, influencers, and individuals achieve their online goals.
                        </p>
                        <p class="text-body-1 text-medium-emphasis mb-4">
                            Our platform connects you with the most reliable and cost-effective SMM services in the market, backed by cutting-edge technology and dedicated support.
                        </p>
                        <v-btn color="primary" to="/all-services">
                            Explore Services
                            <v-icon end>mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col v-for="stat in aboutStats" :key="stat.label" cols="6">
                                <v-card class="pa-6 text-center" variant="outlined">
                                    <div class="text-h3 font-weight-bold text-primary">{{ stat.value }}</div>
                                    <div class="text-body-2 text-medium-emphasis">{{ stat.label }}</div>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Values -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center mb-12">
                    <h2 class="text-h3 font-weight-bold mb-4">Our Values</h2>
                </div>
                <v-row>
                    <v-col v-for="value in values" :key="value.title" cols="12" md="4">
                        <v-card class="pa-8 text-center h-100" variant="flat">
                            <v-icon :color="value.color" size="56" class="mb-4">{{ value.icon }}</v-icon>
                            <h3 class="text-h6 font-weight-bold mb-2">{{ value.title }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ value.description }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="py-16">
            <v-container>
                <v-card class="pa-12 text-center" color="primary">
                    <h2 class="text-h3 font-weight-bold text-white mb-4">Ready to Grow?</h2>
                    <p class="text-h6 text-white mb-8" style="opacity: 0.9;">Join thousands of satisfied customers today</p>
                    <v-btn size="x-large" color="white" href="/register">Get Started Free</v-btn>
                </v-card>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const aboutStats = ref([
    { value: '10K+', label: 'Happy Customers' },
    { value: '500+', label: 'Services' },
    { value: '1M+', label: 'Orders Delivered' },
    { value: '24/7', label: 'Support' },
])

const values = ref([
    { title: 'Quality', icon: 'mdi-star', color: 'warning', description: 'We deliver only the highest quality services that meet your expectations.' },
    { title: 'Speed', icon: 'mdi-lightning-bolt', color: 'info', description: 'Fast delivery times with most orders starting within minutes.' },
    { title: 'Support', icon: 'mdi-headset', color: 'success', description: '24/7 customer support to help you with any questions or issues.' },
])
</script>
