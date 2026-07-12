<template>
    <div>
        <PageHero icon="mdi-information-outline" :badge="$t('howItWorks.title')" :title="$t('howItWorks.title')" :subtitle="$t('howItWorks.subtitle')" />

        <!-- Steps -->
        <section class="section">
            <v-container>
                <div class="steps-grid">
                    <div v-for="(step, i) in steps" :key="i" class="step-card">
                        <div class="step-num">{{ i + 1 }}</div>
                        <v-icon :color="step.color" size="36" style="margin-bottom: 12px;">{{ step.icon }}</v-icon>
                        <h3 class="step-title">{{ $t(step.titleKey) }}</h3>
                        <p class="step-desc">{{ $t(step.descKey) }}</p>
                    </div>
                </div>
            </v-container>
        </section>

        <!-- Features -->
        <section class="section section-alt">
            <v-container>
                <div class="section-header">
                    <h2 class="heading-lg">{{ $t('howItWorks.whyChooseUs') }}</h2>
                </div>
                <v-row>
                    <v-col v-for="feature in features" :key="feature.titleKey" cols="12" sm="6" lg="3">
                        <div class="card" style="padding: 28px; text-align: center; height: 100%;">
                            <v-icon :color="feature.color" size="32" style="margin-bottom: 12px;">{{ feature.icon }}</v-icon>
                            <h4 class="heading-md" style="margin-bottom: 6px;">{{ $t(feature.titleKey) }}</h4>
                            <p class="text-muted">{{ $t(feature.descKey) }}</p>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="section">
            <v-container>
                <CtaCard :title="$t('howItWorks.readyToStart')" :description="$t('howItWorks.createFreeAccount')">
                    <v-btn size="large" color="white" href="/register">
                        <v-icon start>mdi-account-plus</v-icon>
                        {{ $t('howItWorks.signUpFree') }}
                    </v-btn>
                    <v-btn size="large" variant="outlined" color="white" to="/all-services">
                        {{ $t('howItWorks.browseServices') }}
                    </v-btn>
                </CtaCard>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '../stores/app'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'
import PageHero from '../components/public/PageHero.vue'
import CtaCard from '../components/public/CtaCard.vue'

const { locale } = useI18n()
const store = useAppStore()

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
