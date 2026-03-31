<template>
    <div>
        <!-- Hero -->
        <section class="section-padding" :style="{ background: heroBg }">
            <v-container>
                <h1 class="text-h4 font-weight-bold mb-2">{{ $t('privacyPage.title') }}</h1>
                <p class="text-body-2 text-medium-emphasis">{{ $t('privacyPage.lastUpdated') }}: {{ new Date().toLocaleDateString() }}</p>
            </v-container>
        </section>

        <!-- Content -->
        <section class="section-padding">
            <v-container>
                <v-row justify="center">
                    <v-col cols="12" lg="8">
                        <v-card class="pa-6" variant="outlined">
                            <div v-for="(section, i) in sections" :key="i" :class="{ 'mb-6': i < sections.length - 1 }">
                                <h2 class="text-h6 font-weight-bold mb-2">{{ $t(section.titleKey) }}</h2>
                                <p class="text-body-2 text-medium-emphasis" style="line-height: 1.8;">{{ $t(section.contentKey) }}</p>
                                <v-divider v-if="i < sections.length - 1" class="mt-6"></v-divider>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
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
    const config = locale.value === 'ar' ? { ...seoConfigs.privacy, ...seoConfigsAr.privacy } : seoConfigs.privacy
    return config
})
useSeo(seoConfig.value)

const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    const r = parseInt(p.slice(1,3),16), g = parseInt(p.slice(3,5),16), b = parseInt(p.slice(5,7),16)
    return `linear-gradient(135deg, rgba(${r},${g},${b}, 0.06) 0%, transparent 60%)`
})

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
