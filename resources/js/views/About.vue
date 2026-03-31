<template>
    <div>
        <!-- Hero -->
        <section class="section-padding" :style="{ background: heroBg }">
            <v-container>
                <h1 class="text-h4 font-weight-bold mb-2">{{ $t('about.whoWeAre') }}</h1>
                <p class="text-body-2 text-medium-emphasis" style="max-width: 600px;">
                    {{ $t('about.whoWeAreDesc') }}
                </p>
            </v-container>
        </section>

        <!-- Story + Stats -->
        <section class="section-padding">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" md="6">
                        <h2 class="text-h5 font-weight-bold mb-3">{{ $t('about.ourStory') }}</h2>
                        <p class="text-body-2 text-medium-emphasis mb-3">
                            {{ $t('about.ourStoryDesc1') }}
                        </p>
                        <p class="text-body-2 text-medium-emphasis mb-4">
                            {{ $t('about.ourStoryDesc2') }}
                        </p>
                        <v-btn color="primary" size="default" to="/all-services">
                            {{ $t('about.exploreServices') }}
                            <v-icon end>mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col v-for="stat in aboutStats" :key="stat.labelKey" cols="6">
                                <v-card class="pa-4 text-center hover-lift" variant="outlined">
                                    <div class="text-h5 font-weight-bold gradient-text" :style="{ backgroundImage: store.gradientStyle }">{{ stat.value }}</div>
                                    <div class="text-body-2 text-medium-emphasis">{{ $t(stat.labelKey) }}</div>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Values -->
        <section class="section-padding bg-surface-variant">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h5 font-weight-bold">{{ $t('about.ourValues') }}</h2>
                </div>
                <v-row>
                    <v-col v-for="value in values" :key="value.titleKey" cols="12" md="4">
                        <v-card class="pa-6 text-center h-100 hover-lift" variant="outlined">
                            <v-icon :color="value.color" size="40" class="mb-3">{{ value.icon }}</v-icon>
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">{{ $t(value.titleKey) }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(value.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="section-padding">
            <v-container>
                <v-card class="pa-8 text-center" :style="{ background: store.gradientStyle }">
                    <h2 class="text-h5 font-weight-bold text-white mb-3">{{ $t('home.readyToGrow') }}</h2>
                    <p class="text-body-2 text-white mb-6" style="opacity: 0.9;">{{ $t('about.joinCustomers') }}</p>
                    <v-btn size="large" color="white" href="/register">{{ $t('home.getStarted') }}</v-btn>
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

const { locale } = useI18n()
const store = useAppStore()

const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.about, ...seoConfigsAr.about } : seoConfigs.about
    return config
})
useSeo(seoConfig.value)

const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    const r = parseInt(p.slice(1,3),16), g = parseInt(p.slice(3,5),16), b = parseInt(p.slice(5,7),16)
    return `linear-gradient(135deg, rgba(${r},${g},${b}, 0.06) 0%, transparent 60%)`
})

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
