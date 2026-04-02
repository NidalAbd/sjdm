<template>
    <div>
        <!-- Hero -->
        <section class="hero">
            <v-container>
                <div class="hero-badge"><v-icon size="14">mdi-domain</v-icon> {{ $t('about.whoWeAre') }}</div>
                <h1 class="heading-xl hero-title">{{ $t('about.whoWeAre') }}</h1>
                <p class="hero-desc">{{ $t('about.whoWeAreDesc') }}</p>
            </v-container>
        </section>

        <!-- Our Story -->
        <section class="section">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" md="6">
                        <div class="section-header" style="text-align: start;">
                            <h2 class="heading-lg">{{ $t('about.ourStory') }}</h2>
                        </div>
                        <p class="text-muted" style="margin-bottom: 12px;">{{ $t('about.ourStoryDesc1') }}</p>
                        <p class="text-muted" style="margin-bottom: 24px;">{{ $t('about.ourStoryDesc2') }}</p>
                        <v-btn color="primary" size="default" to="/all-services">
                            {{ $t('about.exploreServices') }}
                            <v-icon end>mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col v-for="stat in aboutStats" :key="stat.labelKey" cols="6">
                                <div class="card" style="padding: 24px; text-align: center;">
                                    <div class="heading-lg" :style="{ backgroundImage: store.gradientStyle, '-webkit-background-clip': 'text', '-webkit-text-fill-color': 'transparent' }">{{ stat.value }}</div>
                                    <div class="text-muted">{{ $t(stat.labelKey) }}</div>
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Values -->
        <section class="section section-alt">
            <v-container>
                <div class="section-header">
                    <h2 class="heading-lg">{{ $t('about.ourValues') }}</h2>
                </div>
                <v-row>
                    <v-col v-for="value in values" :key="value.titleKey" cols="12" md="4">
                        <div class="card" style="padding: 32px; text-align: center; height: 100%;">
                            <v-icon :color="value.color" size="40" style="margin-bottom: 16px;">{{ value.icon }}</v-icon>
                            <h3 class="heading-md" style="margin-bottom: 8px;">{{ $t(value.titleKey) }}</h3>
                            <p class="text-muted">{{ $t(value.descKey) }}</p>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="section">
            <v-container>
                <div class="cta-card" :style="{ background: store.gradientStyle }" style="text-align: center;">
                    <h2 class="heading-lg" style="color: #fff; margin-bottom: 12px;">{{ $t('home.readyToGrow') }}</h2>
                    <p style="color: rgba(255,255,255,0.9); margin-bottom: 24px;">{{ $t('about.joinCustomers') }}</p>
                    <v-btn size="large" color="white" href="/register">{{ $t('home.getStarted') }}</v-btn>
                </div>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '../stores/app'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { locale } = useI18n()
const store = useAppStore()

const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.about, ...seoConfigsAr.about } : seoConfigs.about
    return config
})
useSeo(seoConfig.value)

const aboutStats = ref([
    { value: '10K+', labelKey: 'about.happyCustomers' },
    { value: '500+', labelKey: 'about.services' },
    { value: '1M+', labelKey: 'about.ordersDelivered' },
    { value: '24/7', labelKey: 'about.support' },
])

const values = ref([
    { titleKey: 'about.quality', icon: 'mdi-star', color: 'warning', descKey: 'about.qualityDesc' },
    { titleKey: 'about.speed', icon: 'mdi-lightning-bolt', color: 'info', descKey: 'about.speedDesc' },
    { titleKey: 'about.supportTitle', icon: 'mdi-headset', color: 'success', descKey: 'about.supportDesc' },
])
</script>
