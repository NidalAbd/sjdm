<template>
    <v-container class="py-16">
        <div class="text-center mb-12">
            <v-chip color="primary" variant="tonal" class="mb-4">Legal</v-chip>
            <h1 class="text-h2 font-weight-bold mb-4">Privacy Policy</h1>
            <p class="text-body-1 text-medium-emphasis">Last updated: {{ new Date().toLocaleDateString() }}</p>
        </div>

        <v-row justify="center">
            <v-col cols="12" lg="8">
                <v-card class="pa-8">
                    <div v-for="(section, i) in sections" :key="i" class="mb-8">
                        <h2 class="text-h5 font-weight-bold mb-4">{{ section.title }}</h2>
                        <p class="text-body-1 text-medium-emphasis" style="line-height: 1.8;">{{ section.content }}</p>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref } from 'vue'

const sections = ref([
    {
        title: '1. Information We Collect',
        content: 'We collect information you provide directly to us, such as when you create an account, place an order, or contact us for support. This includes your name, email address, and payment information.'
    },
    {
        title: '2. How We Use Your Information',
        content: 'We use the information we collect to provide, maintain, and improve our services, process transactions, send you technical notices and support messages, and respond to your comments and questions.'
    },
    {
        title: '3. Information Sharing',
        content: 'We do not share your personal information with third parties except as described in this policy. We may share information with service providers who perform services on our behalf, or when required by law.'
    },
    {
        title: '4. Data Security',
        content: 'We take reasonable measures to help protect your personal information from loss, theft, misuse, unauthorized access, disclosure, alteration, and destruction. All data is encrypted using SSL technology.'
    },
    {
        title: '5. Cookies',
        content: 'We use cookies and similar tracking technologies to track activity on our website and hold certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.'
    },
    {
        title: '6. Your Rights',
        content: 'You have the right to access, update, or delete your personal information at any time. You can do this through your account settings or by contacting our support team.'
    },
    {
        title: '7. Changes to This Policy',
        content: 'We may update this privacy policy from time to time. We will notify you of any changes by posting the new privacy policy on this page and updating the "Last updated" date.'
    },
    {
        title: '8. Contact Us',
        content: 'If you have any questions about this Privacy Policy, please contact us at support@smmjd.com.'
    },
])
</script>
