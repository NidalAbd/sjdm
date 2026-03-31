<template>
    <div>
        <!-- Hero -->
        <section class="py-16 bg-surface">
            <v-container>
                <div class="text-center">
                    <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('about.title') }}</v-chip>
                    <h1 class="text-h2 font-weight-bold mb-4">{{ $t('about.whoWeAre') }}</h1>
                    <p class="text-h6 text-medium-emphasis mx-auto" style="max-width: 700px;">
                        {{ $t('about.whoWeAreDesc') }}
                    </p>
                </div>
            </v-container>
        </section>

        <!-- Story -->
        <section class="py-16">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" md="6">
                        <h2 class="text-h3 font-weight-bold mb-4">{{ $t('about.ourStory') }}</h2>
                        <p class="text-body-1 text-medium-emphasis mb-4">
                            {{ $t('about.ourStoryDesc1') }}
                        </p>
                        <p class="text-body-1 text-medium-emphasis mb-4">
                            {{ $t('about.ourStoryDesc2') }}
                        </p>
                        <v-btn color="primary" to="/all-services">
                            {{ $t('about.exploreServices') }}
                            <v-icon end>mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col v-for="stat in aboutStats" :key="stat.labelKey" cols="6">
                                <v-card class="pa-6 text-center" variant="outlined">
                                    <div class="text-h3 font-weight-bold text-primary">{{ stat.value }}</div>
                                    <div class="text-body-2 text-medium-emphasis">{{ $t(stat.labelKey) }}</div>
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
                    <h2 class="text-h3 font-weight-bold mb-4">{{ $t('about.ourValues') }}</h2>
                </div>
                <v-row>
                    <v-col v-for="value in values" :key="value.titleKey" cols="12" md="4">
                        <v-card class="pa-8 text-center h-100" variant="flat">
                            <v-icon :color="value.color" size="56" class="mb-4">{{ value.icon }}</v-icon>
                            <h3 class="text-h6 font-weight-bold mb-2">{{ $t(value.titleKey) }}</h3>
                            <p class="text-body-2 text-medium-emphasis">{{ $t(value.descKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA -->
        <section class="py-16">
            <v-container>
                <v-card class="pa-12 text-center" color="primary">
                    <h2 class="text-h3 font-weight-bold text-white mb-4">{{ $t('home.readyToGrow') }}</h2>
                    <p class="text-h6 text-white mb-8" style="opacity: 0.9;">{{ $t('about.joinCustomers') }}</p>
                    <v-btn size="x-large" color="white" href="/register">{{ $t('home.getStarted') }}</v-btn>
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
