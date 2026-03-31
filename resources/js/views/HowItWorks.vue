<template>
    <div>
        <!-- Hero -->
        <section class="section-padding" :style="{ background: heroBg }">
            <v-container>
                <h1 class="text-h4 font-weight-bold mb-2">{{ $t('howItWorks.title') }}</h1>
                <p class="text-body-2 text-medium-emphasis">{{ $t('howItWorks.subtitle') }}</p>
            </v-container>
        </section>

        <!-- Steps -->
        <section class="section-padding">
            <v-container>
                <v-row>
                    <v-col v-for="(step, i) in steps" :key="i" cols="12" md="4">
                        <v-card class="pa-6 text-center h-100 hover-lift" variant="outlined">
                            <v-avatar color="primary" variant="tonal" size="48" class="mb-4">
                                <span class="text-h6 font-weight-bold">{{ i + 1 }}</span>
                            </v-avatar>
                            <v-icon :color="step.color" size="36" class="d-block mb-3">{{ step.icon }}</v-icon>
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">{{ $t(step.titleKey) }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(step.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Features -->
        <section class="section-padding bg-surface-variant">
            <v-container>
                <div class="text-center mb-8">
                    <h2 class="text-h5 font-weight-bold">{{ $t('howItWorks.whyChooseUs') }}</h2>
                </div>
                <v-row>
                    <v-col v-for="feature in features" :key="feature.titleKey" cols="12" sm="6" lg="3">
                        <v-card class="pa-5 text-center h-100 hover-lift" variant="outlined">
                            <v-icon :color="feature.color" size="32" class="mb-3">{{ feature.icon }}</v-icon>
                            <h4 class="text-subtitle-2 font-weight-bold mb-1">{{ $t(feature.titleKey) }}</h4>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(feature.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="section-padding">
            <v-container>
                <v-card class="pa-8 text-center" :style="{ background: store.gradientStyle }">
                    <h2 class="text-h5 font-weight-bold text-white mb-3">{{ $t('howItWorks.readyToStart') }}</h2>
                    <p class="text-body-2 text-white mb-6" style="opacity: 0.9;">{{ $t('howItWorks.createFreeAccount') }}</p>
                    <div class="d-flex justify-center ga-3 flex-wrap">
                        <v-btn size="large" color="white" href="/register">
                            <v-icon start>mdi-account-plus</v-icon>
                            {{ $t('howItWorks.signUpFree') }}
                        </v-btn>
                        <v-btn size="large" variant="outlined" color="white" to="/all-services">
                            {{ $t('howItWorks.browseServices') }}
                        </v-btn>
                    </div>
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
    const config = locale.value === 'ar' ? { ...seoConfigs.howItWorks, ...seoConfigsAr.howItWorks } : seoConfigs.howItWorks
    return config
})
useSeo(seoConfig.value)

const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    const r = parseInt(p.slice(1,3),16), g = parseInt(p.slice(3,5),16), b = parseInt(p.slice(5,7),16)
    return `linear-gradient(135deg, rgba(${r},${g},${b}, 0.06) 0%, transparent 60%)`
})

const steps = ref([
    {
        titleKey: 'howItWorks.step1Title',
        descKey: 'howItWorks.step1DescFull',
        icon: 'mdi-account-plus',
        color: 'primary'
    },
    {
        titleKey: 'howItWorks.step2TitleFull',
        descKey: 'howItWorks.step2DescFull',
        icon: 'mdi-cart',
        color: 'success'
    },
    {
        titleKey: 'howItWorks.step3Title',
        descKey: 'howItWorks.step3Desc',
        icon: 'mdi-chart-line',
        color: 'info'
    },
])

const features = ref([
    { titleKey: 'howItWorks.instantStart', icon: 'mdi-lightning-bolt', color: 'warning', descKey: 'howItWorks.instantStartDesc' },
    { titleKey: 'howItWorks.securePayments', icon: 'mdi-shield-check', color: 'success', descKey: 'howItWorks.securePaymentsDesc' },
    { titleKey: 'howItWorks.support247', icon: 'mdi-headset', color: 'info', descKey: 'howItWorks.support247Desc' },
    { titleKey: 'howItWorks.bestPrices', icon: 'mdi-tag', color: 'error', descKey: 'howItWorks.bestPricesDesc' },
])
</script>
