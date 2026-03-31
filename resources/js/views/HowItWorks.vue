<template>
    <div>
        <!-- Hero -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center">
                    <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('howItWorks.guide') }}</v-chip>
                    <h1 class="text-h2 font-weight-bold mb-4">{{ $t('howItWorks.title') }}</h1>
                    <p class="text-h6 text-medium-emphasis mx-auto" style="max-width: 600px;">
                        {{ $t('howItWorks.subtitle') }}
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
                            <h3 class="text-h5 font-weight-bold mb-4">{{ $t(step.titleKey) }}</h3>
                            <p class="text-body-1 text-medium-emphasis">{{ $t(step.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Features -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center mb-12">
                    <h2 class="text-h3 font-weight-bold mb-4">{{ $t('howItWorks.whyChooseUs') }}</h2>
                </div>
                <v-row>
                    <v-col v-for="feature in features" :key="feature.titleKey" cols="12" sm="6" lg="3">
                        <v-card class="pa-6 text-center h-100" variant="flat">
                            <v-icon :color="feature.color" size="48" class="mb-4">{{ feature.icon }}</v-icon>
                            <h4 class="text-h6 font-weight-bold mb-2">{{ $t(feature.titleKey) }}</h4>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(feature.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="py-16">
            <v-container>
                <v-card class="pa-12 text-center" color="primary">
                    <h2 class="text-h3 font-weight-bold text-white mb-4">{{ $t('howItWorks.readyToStart') }}</h2>
                    <p class="text-h6 text-white mb-8" style="opacity: 0.9;">{{ $t('howItWorks.createFreeAccount') }}</p>
                    <div class="d-flex justify-center ga-4 flex-wrap">
                        <v-btn size="x-large" color="white" href="/register">
                            <v-icon start>mdi-account-plus</v-icon>
                            {{ $t('howItWorks.signUpFree') }}
                        </v-btn>
                        <v-btn size="x-large" variant="outlined" color="white" to="/all-services">
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
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { locale } = useI18n()

// SEO Configuration
const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.howItWorks, ...seoConfigsAr.howItWorks } : seoConfigs.howItWorks
    return config
})
useSeo(seoConfig.value)

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

<style scoped>
.step-card {
    transition: all 0.3s ease;
}

.step-card:hover {
    transform: translateY(-8px);
    border-color: rgba(99, 102, 241, 0.5) !important;
}
</style>
