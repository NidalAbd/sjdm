<template>
    <v-container class="py-16">
        <div class="text-center mb-12">
            <v-chip color="primary" variant="tonal" class="mb-4">
                <v-icon start>mdi-help-circle</v-icon>
                {{ $t('publicNav.faq') }}
            </v-chip>
            <h1 class="text-h2 font-weight-bold mb-4">{{ $t('faq.title') }}</h1>
            <p class="text-h6 text-medium-emphasis mx-auto" style="max-width: 600px;">
                {{ $t('faq.subtitle') }}
            </p>
        </div>

        <v-row justify="center">
            <v-col cols="12" lg="10">
                <v-row>
                    <v-col v-for="(faq, i) in faqs" :key="i" cols="12" md="6">
                        <v-card class="faq-card h-100 pa-6" variant="outlined" hover>
                            <div class="d-flex align-start mb-4">
                                <v-avatar color="primary" variant="tonal" size="40" class="mr-4 flex-shrink-0">
                                    <span class="font-weight-bold">{{ i + 1 }}</span>
                                </v-avatar>
                                <h3 class="text-subtitle-1 font-weight-bold">{{ $t(faq.questionKey) }}</h3>
                            </div>
                            <p class="text-body-2 text-medium-emphasis" style="padding-left: 56px;">{{ $t(faq.answerKey) }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <div class="text-center mt-12">
            <p class="text-body-1 text-medium-emphasis mb-4">{{ $t('faq.stillHaveQuestions') }}</p>
            <v-btn color="primary" size="large" to="/contact-us">
                <v-icon start>mdi-message-question</v-icon>
                {{ $t('faq.contactSupport') }}
            </v-btn>
        </div>
    </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { locale } = useI18n()

// SEO Configuration
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

<style scoped>
.faq-card {
    transition: all 0.3s ease;
    border-color: rgba(99, 102, 241, 0.2) !important;
}

.faq-card:hover {
    transform: translateY(-4px);
    border-color: rgba(99, 102, 241, 0.5) !important;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
}
</style>
