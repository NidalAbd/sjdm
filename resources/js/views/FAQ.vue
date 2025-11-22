<template>
    <v-container class="py-16">
        <div class="text-center mb-12">
            <v-chip color="primary" variant="tonal" class="mb-4">
                <v-icon start>mdi-help-circle</v-icon>
                FAQ
            </v-chip>
            <h1 class="text-h2 font-weight-bold mb-4">Frequently Asked Questions</h1>
            <p class="text-h6 text-medium-emphasis mx-auto" style="max-width: 600px;">
                Find answers to the most common questions about our services
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
                                <h3 class="text-subtitle-1 font-weight-bold">{{ faq.question }}</h3>
                            </div>
                            <p class="text-body-2 text-medium-emphasis" style="padding-left: 56px;">{{ faq.answer }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <div class="text-center mt-12">
            <p class="text-body-1 text-medium-emphasis mb-4">Still have questions?</p>
            <v-btn color="primary" size="large" to="/contact-us">
                <v-icon start>mdi-message-question</v-icon>
                Contact Support
            </v-btn>
        </div>
    </v-container>
</template>

<script setup>
import { ref } from 'vue'

const faqs = ref([
    { question: 'How fast is the delivery?', answer: 'Most orders start within 0-15 minutes and complete within a few hours depending on the quantity ordered.' },
    { question: 'Is it safe to use SMM services?', answer: 'Yes! We use safe delivery methods and never ask for your password. Your account security is our top priority.' },
    { question: 'What payment methods do you accept?', answer: 'We accept credit/debit cards, PayPal, cryptocurrency (Bitcoin, USDT), and various local payment methods.' },
    { question: 'Do you offer refunds?', answer: 'Yes, we offer full refunds if we fail to deliver your order. Partial refunds are available for partially completed orders.' },
    { question: 'What is the minimum order?', answer: 'Minimum orders vary by service, typically starting from 10-100 depending on the service type.' },
    { question: 'How do I track my order?', answer: 'You can track all your orders from your dashboard. Each order shows real-time status and progress.' },
    { question: 'Do followers/likes drop?', answer: 'Some drop is normal (5-10%). Many of our services include refill guarantees to compensate for any drops.' },
    { question: 'Can I get a custom order?', answer: 'Yes! Contact our support team for custom orders or bulk discounts on large quantities.' },
    { question: 'Is my information secure?', answer: 'Absolutely. We use SSL encryption and never share your data with third parties.' },
    { question: 'How do I add funds?', answer: 'Go to Add Funds in your dashboard, select amount and payment method, then complete the payment.' },
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
