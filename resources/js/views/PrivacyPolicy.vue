<template>
    <v-container class="py-16">
        <div class="text-center mb-12">
            <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('privacyPage.legal') }}</v-chip>
            <h1 class="text-h2 font-weight-bold mb-4">{{ $t('privacyPage.title') }}</h1>
            <p class="text-body-1 text-medium-emphasis">{{ $t('privacyPage.lastUpdated') }}: {{ new Date().toLocaleDateString() }}</p>
        </div>

        <v-row justify="center">
            <v-col cols="12" lg="8">
                <v-card class="pa-8">
                    <div v-for="(section, i) in sections" :key="i" class="mb-8">
                        <h2 class="text-h5 font-weight-bold mb-4">{{ $t(section.titleKey) }}</h2>
                        <p class="text-body-1 text-medium-emphasis" style="line-height: 1.8;">{{ $t(section.contentKey) }}</p>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { locale } = useI18n()

// SEO Configuration
const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.privacy, ...seoConfigsAr.privacy } : seoConfigs.privacy
    return config
})
useSeo(seoConfig.value)

const sections = ref([
    { titleKey: 'privacyPage.section1Title', contentKey: 'privacyPage.section1Content' },
    { titleKey: 'privacyPage.section2Title', contentKey: 'privacyPage.section2Content' },
    { titleKey: 'privacyPage.section3Title', contentKey: 'privacyPage.section3Content' },
    { titleKey: 'privacyPage.section4Title', contentKey: 'privacyPage.section4Content' },
    { titleKey: 'privacyPage.section5Title', contentKey: 'privacyPage.section5Content' },
    { titleKey: 'privacyPage.section6Title', contentKey: 'privacyPage.section6Content' },
    { titleKey: 'privacyPage.section7Title', contentKey: 'privacyPage.section7Content' },
    { titleKey: 'privacyPage.section8Title', contentKey: 'privacyPage.section8Content' },
])
</script>
