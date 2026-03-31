<template>
    <v-container class="py-16">
        <v-row>
            <!-- Contact Info -->
            <v-col cols="12" lg="5">
                <v-chip color="primary" variant="tonal" class="mb-4">{{ $t('contact.getInTouch') }}</v-chip>
                <h1 class="text-h3 font-weight-bold mb-4">{{ $t('contact.title') }}</h1>
                <p class="text-body-1 text-medium-emphasis mb-8">
                    {{ $t('contact.getInTouchDesc') }}
                </p>

                <v-card class="mb-4 pa-4" variant="outlined">
                    <div class="d-flex align-center">
                        <v-avatar color="primary" size="48" class="mr-4">
                            <v-icon>mdi-email</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-caption text-medium-emphasis">{{ $t('contact.emailUs') }}</div>
                            <span class="text-body-1 font-weight-medium">support@smmjd.com</span>
                        </div>
                    </div>
                </v-card>

                <v-card class="mb-4 pa-4" variant="outlined">
                    <div class="d-flex align-center">
                        <v-avatar color="success" size="48" class="mr-4">
                            <v-icon>mdi-clock</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-caption text-medium-emphasis">{{ $t('contact.supportHours') }}</div>
                            <span class="text-body-1 font-weight-medium">{{ $t('contact.available247') }}</span>
                        </div>
                    </div>
                </v-card>

                <v-card class="mb-4 pa-4" variant="outlined">
                    <div class="d-flex align-center">
                        <v-avatar color="info" size="48" class="mr-4">
                            <v-icon>mdi-ticket</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-caption text-medium-emphasis">{{ $t('contact.supportTicket') }}</div>
                            <a href="/support" class="text-body-1 font-weight-medium text-primary text-decoration-none">
                                {{ $t('contact.openTicket') }}
                            </a>
                        </div>
                    </div>
                </v-card>
            </v-col>

            <!-- Contact Form -->
            <v-col cols="12" lg="7">
                <v-card class="pa-8" variant="outlined">
                    <h2 class="text-h5 font-weight-bold mb-6">{{ $t('contact.sendMessage') }}</h2>
                    <v-form @submit.prevent="submitForm">
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.name" :label="$t('contact.name')" prepend-inner-icon="mdi-account" required></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.email" :label="$t('contact.email')" type="email" prepend-inner-icon="mdi-email" required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="form.subject" :label="$t('contact.subject')" prepend-inner-icon="mdi-text" required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="form.message" :label="$t('contact.message')" prepend-inner-icon="mdi-message" rows="5" required></v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-btn type="submit" color="primary" size="x-large" block :loading="loading">
                                    <v-icon start>mdi-send</v-icon>
                                    {{ $t('contact.send') }}
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { t, locale } = useI18n()

// SEO Configuration
const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.contact, ...seoConfigsAr.contact } : seoConfigs.contact
    return config
})
useSeo(seoConfig.value)
const loading = ref(false)
const form = reactive({ name: '', email: '', subject: '', message: '' })

const submitForm = async () => {
    loading.value = true
    await new Promise(r => setTimeout(r, 1500))
    alert(t('contact.successMessage'))
    Object.keys(form).forEach(k => form[k] = '')
    loading.value = false
}
</script>
