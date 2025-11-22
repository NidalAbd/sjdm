<template>
    <v-container class="py-16">
        <v-row>
            <!-- Contact Info -->
            <v-col cols="12" lg="5">
                <v-chip color="primary" variant="tonal" class="mb-4">Get in Touch</v-chip>
                <h1 class="text-h3 font-weight-bold mb-4">Contact Us</h1>
                <p class="text-body-1 text-medium-emphasis mb-8">
                    Have questions? We're here to help! Reach out to us and we'll respond as soon as possible.
                </p>

                <v-card class="mb-4 pa-4" variant="outlined">
                    <div class="d-flex align-center">
                        <v-avatar color="primary" size="48" class="mr-4">
                            <v-icon>mdi-email</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-caption text-medium-emphasis">Email Us</div>
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
                            <div class="text-caption text-medium-emphasis">Support Hours</div>
                            <span class="text-body-1 font-weight-medium">24/7 Available</span>
                        </div>
                    </div>
                </v-card>

                <v-card class="mb-4 pa-4" variant="outlined">
                    <div class="d-flex align-center">
                        <v-avatar color="info" size="48" class="mr-4">
                            <v-icon>mdi-ticket</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-caption text-medium-emphasis">Support Ticket</div>
                            <a href="/support" class="text-body-1 font-weight-medium text-primary text-decoration-none">
                                Open a Ticket
                            </a>
                        </div>
                    </div>
                </v-card>
            </v-col>

            <!-- Contact Form -->
            <v-col cols="12" lg="7">
                <v-card class="pa-8" variant="outlined">
                    <h2 class="text-h5 font-weight-bold mb-6">Send us a Message</h2>
                    <v-form @submit.prevent="submitForm">
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.name" label="Your Name" prepend-inner-icon="mdi-account" required></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="form.email" label="Email Address" type="email" prepend-inner-icon="mdi-email" required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="form.subject" label="Subject" prepend-inner-icon="mdi-text" required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="form.message" label="Your Message" prepend-inner-icon="mdi-message" rows="5" required></v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-btn type="submit" color="primary" size="x-large" block :loading="loading">
                                    <v-icon start>mdi-send</v-icon>
                                    Send Message
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
import { ref, reactive } from 'vue'

const loading = ref(false)
const form = reactive({ name: '', email: '', subject: '', message: '' })

const submitForm = async () => {
    loading.value = true
    await new Promise(r => setTimeout(r, 1500))
    alert('Message sent successfully!')
    Object.keys(form).forEach(k => form[k] = '')
    loading.value = false
}
</script>
