<template>
    <div>
        <!-- Hero -->
        <section class="section-padding" :style="{ background: heroBg }">
            <v-container>
                <h1 class="text-h4 font-weight-bold mb-2">{{ $t('contact.title') }}</h1>
                <p class="text-body-2 text-medium-emphasis">{{ $t('contact.getInTouchDesc') }}</p>
            </v-container>
        </section>

        <!-- Content -->
        <section class="section-padding">
            <v-container>
                <v-row>
                    <!-- Contact Info -->
                    <v-col cols="12" lg="5">
                        <v-card class="mb-3 pa-4 hover-lift" variant="outlined">
                            <div class="d-flex align-center">
                                <v-avatar color="primary" size="40" class="mr-3">
                                    <v-icon size="20">mdi-email</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-caption text-medium-emphasis">{{ $t('contact.emailUs') }}</div>
                                    <span class="text-body-2 font-weight-medium">support@smmjd.com</span>
                                </div>
                            </div>
                        </v-card>

                        <v-card class="mb-3 pa-4 hover-lift" variant="outlined">
                            <div class="d-flex align-center">
                                <v-avatar color="success" size="40" class="mr-3">
                                    <v-icon size="20">mdi-clock</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-caption text-medium-emphasis">{{ $t('contact.supportHours') }}</div>
                                    <span class="text-body-2 font-weight-medium">{{ $t('contact.available247') }}</span>
                                </div>
                            </div>
                        </v-card>

                        <v-card class="mb-3 pa-4 hover-lift" variant="outlined">
                            <div class="d-flex align-center">
                                <v-avatar color="info" size="40" class="mr-3">
                                    <v-icon size="20">mdi-ticket</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-caption text-medium-emphasis">{{ $t('contact.supportTicket') }}</div>
                                    <a href="/support" class="text-body-2 font-weight-medium text-primary text-decoration-none">
                                        {{ $t('contact.openTicket') }}
                                    </a>
                                </div>
                            </div>
                        </v-card>
                    </v-col>

                    <!-- Contact Form -->
                    <v-col cols="12" lg="7">
                        <v-card class="pa-6" variant="outlined">
                            <h2 class="text-h6 font-weight-bold mb-4">{{ $t('contact.sendMessage') }}</h2>
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
                                        <v-textarea v-model="form.message" :label="$t('contact.message')" prepend-inner-icon="mdi-message" rows="4" required></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-btn type="submit" color="primary" size="large" block :loading="loading">
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
        </section>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '../stores/app'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { t, locale } = useI18n()
const store = useAppStore()

const seoConfig = computed(() => {
    const config = locale.value === 'ar' ? { ...seoConfigs.contact, ...seoConfigsAr.contact } : seoConfigs.contact
    return config
})
useSeo(seoConfig.value)

const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    const r = parseInt(p.slice(1,3),16), g = parseInt(p.slice(3,5),16), b = parseInt(p.slice(5,7),16)
    return `linear-gradient(135deg, rgba(${r},${g},${b}, 0.06) 0%, transparent 60%)`
})

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
