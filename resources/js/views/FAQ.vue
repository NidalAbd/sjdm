<template>
    <div>
        <PageHero icon="mdi-help-circle-outline" badge="FAQ" :title="$t('faq.title')" :subtitle="$t('faq.subtitle')" />

        <!-- FAQ Accordion -->
        <section class="section">
            <v-container>
                <v-row justify="center">
                    <v-col cols="12" lg="8">
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel v-for="(faq, i) in faqs" :key="i">
                                <v-expansion-panel-title>
                                    <div class="d-flex align-center">
                                        <v-avatar color="primary" variant="tonal" size="28" class="mr-3 flex-shrink-0">
                                            <span style="font-size: 0.75rem; font-weight: 700;">{{ i + 1 }}</span>
                                        </v-avatar>
                                        <span style="font-weight: 700;">{{ $t(faq.questionKey) }}</span>
                                    </div>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <p class="text-muted">{{ $t(faq.answerKey) }}</p>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Support CTA -->
        <section class="section section-alt">
            <v-container>
                <CtaCard :title="$t('faq.stillHaveQuestions')">
                    <v-btn color="white" size="large" to="/contact-us">
                        <v-icon start>mdi-message-question</v-icon>
                        {{ $t('faq.contactSupport') }}
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
    const config = locale.value === 'ar' ? { ...seoConfigs.faq, ...seoConfigsAr.faq } : seoConfigs.faq
    return config
})
useSeo(seoConfig.value)

const faqs = ref([
    { questionKey: 'faq.q1', answerKey: 'faq.a1' },
    { questionKey: 'faq.q2', answerKey: 'faq.a2' },
    { questionKey: 'faq.q3', answerKey: 'faq.a3' },
    { questionKey: 'faq.q4', answerKey: 'faq.a4' },
    { questionKey: 'faq.q5', answerKey: 'faq.a5' },
    { questionKey: 'faq.q6', answerKey: 'faq.a6' },
    { questionKey: 'faq.q7', answerKey: 'faq.a7' },
    { questionKey: 'faq.q8', answerKey: 'faq.a8' },
    { questionKey: 'faq.q9', answerKey: 'faq.a9' },
    { questionKey: 'faq.q10', answerKey: 'faq.a10' },
])
</script>
