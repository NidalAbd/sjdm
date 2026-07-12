<template>
    <div>
        <PageHero icon="mdi-shield-lock-outline" :badge="$t('privacyPage.title')" :title="$t('privacyPage.title')" :subtitle="`${$t('privacyPage.lastUpdated')}: ${new Date().toLocaleDateString()}`" />

        <!-- Policy Content -->
        <section class="section">
            <v-container>
                <v-row justify="center">
                    <v-col cols="12" lg="8">
                        <div class="card" style="padding: 32px;">
                            <div v-for="(section, i) in sections" :key="i" :style="{ 'margin-bottom': i < sections.length - 1 ? '32px' : '0' }">
                                <h2 class="heading-md" style="margin-bottom: 10px;">{{ $t(section.titleKey) }}</h2>
                                <p class="text-muted" style="line-height: 1.8;">{{ $t(section.contentKey) }}</p>
                                <v-divider v-if="i < sections.length - 1" style="margin-top: 32px;"></v-divider>
                            </div>
                        </div>
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
import PageHero from '../components/public/PageHero.vue'

const { locale } = useI18n()
const store = useAppStore()

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
