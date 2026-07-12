<template>
    <div>
        <PageHero icon="mdi-email-outline" :badge="$t('contact.title')" :title="$t('contact.title')" :subtitle="$t('contact.getInTouchDesc')" />

        <!-- Contact Info + Form -->
        <section class="section">
            <v-container>
                <v-row>
                    <!-- Contact Info -->
                    <v-col cols="12" lg="5">
                        <div class="card" style="padding: 20px; margin-bottom: 16px;">
                            <div class="d-flex align-center">
                                <v-avatar color="primary" size="40" style="margin-right: 16px;">
                                    <v-icon size="20">mdi-email</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $t('contact.emailUs') }}</div>
                                    <span style="font-weight: 500;">support@smmjd.com</span>
                                </div>
                            </div>
                        </div>

                        <div class="card" style="padding: 20px; margin-bottom: 16px;">
                            <div class="d-flex align-center">
                                <v-avatar color="success" size="40" style="margin-right: 16px;">
                                    <v-icon size="20">mdi-clock</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $t('contact.supportHours') }}</div>
                                    <span style="font-weight: 500;">{{ $t('contact.available247') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card" style="padding: 20px; margin-bottom: 16px;">
                            <div class="d-flex align-center">
                                <v-avatar color="info" size="40" style="margin-right: 16px;">
                                    <v-icon size="20">mdi-ticket</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $t('contact.supportTicket') }}</div>
                                    <a href="/support" style="font-weight: 500; text-decoration: none; color: rgb(var(--v-theme-primary));">
                                        {{ $t('contact.openTicket') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </v-col>

                    <!-- Contact Form -->
                    <v-col cols="12" lg="7">
                        <div class="card" style="padding: 32px;">
                            <h2 class="heading-md" style="margin-bottom: 20px;">{{ $t('contact.sendMessage') }}</h2>
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
                        </div>
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
import PageHero from '../components/public/PageHero.vue'

const { t, locale } = useI18n()
const store = useAppStore()

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
