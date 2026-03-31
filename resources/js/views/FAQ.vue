<template>
    <div>
        <!-- Hero -->
        <section class="section-padding" :style="{ background: heroBg }">
            <v-container>
                <h1 class="text-h4 font-weight-bold mb-2">{{ $t('faq.title') }}</h1>
                <p class="text-body-2 text-medium-emphasis">{{ $t('faq.subtitle') }}</p>
            </v-container>
        </section>

        <!-- FAQ Accordion -->
        <section class="section-padding">
            <v-container>
                <v-row justify="center">
                    <v-col cols="12" lg="8">
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel v-for="(faq, i) in faqs" :key="i">
                                <v-expansion-panel-title>
                                    <div class="d-flex align-center">
                                        <v-avatar color="primary" variant="tonal" size="28" class="mr-3 flex-shrink-0">
                                            <span class="text-caption font-weight-bold">{{ i + 1 }}</span>
                                        </v-avatar>
                                        <span class="text-body-1 font-weight-bold">{{ $t(faq.questionKey) }}</span>
                                    </div>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <p class="text-body-2 text-medium-emphasis">{{ $t(faq.answerKey) }}</p>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-col>
                </v-row>

                <div class="text-center mt-8">
                    <p class="text-body-2 text-medium-emphasis mb-3">{{ $t('faq.stillHaveQuestions') }}</p>
                    <v-btn color="primary" size="default" to="/contact-us">
                        <v-icon start>mdi-message-question</v-icon>
                        {{ $t('faq.contactSupport') }}
                    </v-btn>
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
    const config = locale.value === 'ar' ? { ...seoConfigs.faq, ...seoConfigsAr.faq } : seoConfigs.faq
    return config
})
useSeo(seoConfig.value)

const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    const r = parseInt(p.slice(1,3),16), g = parseInt(p.slice(3,5),16), b = parseInt(p.slice(5,7),16)
    return `linear-gradient(135deg, rgba(${r},${g},${b}, 0.06) 0%, transparent 60%)`
})

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
