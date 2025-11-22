<template>
    <v-container class="py-12">
        <v-row v-if="service && !store.loading">
            <v-col cols="12" lg="8">
                <!-- Service Info Card -->
                <v-card class="mb-6">
                    <v-card-text class="pa-8">
                        <div class="d-flex align-center mb-4">
                            <v-chip color="primary" variant="tonal" class="mr-2">
                                ID: {{ service.service_id }}
                            </v-chip>
                            <v-chip color="secondary" variant="tonal">
                                {{ store.locale === 'ar' ? service.category_ar : service.category_en }}
                            </v-chip>
                        </div>

                        <h1 class="text-h4 font-weight-bold mb-4">
                            {{ store.locale === 'ar' ? service.name_ar : service.name_en }}
                        </h1>

                        <v-divider class="my-6"></v-divider>

                        <v-row>
                            <v-col cols="6" sm="3">
                                <div class="text-caption text-medium-emphasis mb-1">Price per 1000</div>
                                <div class="text-h5 font-weight-bold text-success">${{ service.rate }}</div>
                            </v-col>
                            <v-col cols="6" sm="3">
                                <div class="text-caption text-medium-emphasis mb-1">Minimum Order</div>
                                <div class="text-h5 font-weight-bold">{{ formatNumber(service.min) }}</div>
                            </v-col>
                            <v-col cols="6" sm="3">
                                <div class="text-caption text-medium-emphasis mb-1">Maximum Order</div>
                                <div class="text-h5 font-weight-bold">{{ formatNumber(service.max) }}</div>
                            </v-col>
                            <v-col cols="6" sm="3">
                                <div class="text-caption text-medium-emphasis mb-1">Type</div>
                                <div class="text-h5 font-weight-bold text-capitalize">{{ service.type || 'Default' }}</div>
                            </v-col>
                        </v-row>

                        <v-divider class="my-6"></v-divider>

                        <div class="d-flex flex-wrap ga-3">
                            <v-chip v-if="service.refill" color="info" variant="tonal">
                                <v-icon start>mdi-refresh</v-icon>
                                Refill Available
                            </v-chip>
                            <v-chip v-if="service.cancel" color="warning" variant="tonal">
                                <v-icon start>mdi-cancel</v-icon>
                                Cancellation Available
                            </v-chip>
                            <v-chip color="success" variant="tonal">
                                <v-icon start>mdi-lightning-bolt</v-icon>
                                Instant Start
                            </v-chip>
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Description -->
                <v-card class="mb-6">
                    <v-card-title class="text-h6">Description</v-card-title>
                    <v-card-text>
                        <p class="text-body-1">
                            This service provides high-quality engagement for your social media account.
                            Orders typically start within 0-15 minutes and complete based on the quantity ordered.
                        </p>
                        <ul class="mt-4">
                            <li class="mb-2">Real and active engagement</li>
                            <li class="mb-2">Safe delivery methods</li>
                            <li class="mb-2">No password required</li>
                            <li class="mb-2">24/7 customer support</li>
                        </ul>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" lg="4">
                <!-- Order Card -->
                <v-card class="sticky-card">
                    <v-card-title class="text-h6 bg-primary text-white pa-4">
                        <v-icon start>mdi-cart</v-icon>
                        Place Order
                    </v-card-title>
                    <v-card-text class="pa-6">
                        <v-alert type="info" variant="tonal" class="mb-4">
                            Login to place orders
                        </v-alert>

                        <v-text-field
                            v-model="orderForm.link"
                            label="Link"
                            placeholder="Enter your profile/post link"
                            prepend-inner-icon="mdi-link"
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-model="orderForm.quantity"
                            label="Quantity"
                            type="number"
                            :min="service.min"
                            :max="service.max"
                            prepend-inner-icon="mdi-numeric"
                            :hint="`Min: ${formatNumber(service.min)} - Max: ${formatNumber(service.max)}`"
                            persistent-hint
                            class="mb-4"
                        ></v-text-field>

                        <v-divider class="my-4"></v-divider>

                        <div class="d-flex justify-space-between mb-2">
                            <span class="text-medium-emphasis">Price per 1000:</span>
                            <span class="font-weight-bold">${{ service.rate }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-4">
                            <span class="text-medium-emphasis">Total:</span>
                            <span class="text-h5 font-weight-bold text-primary">${{ calculatedPrice }}</span>
                        </div>

                        <v-btn color="primary" size="large" block href="/login">
                            <v-icon start>mdi-login</v-icon>
                            Login to Order
                        </v-btn>

                        <p class="text-caption text-medium-emphasis text-center mt-4">
                            Don't have an account?
                            <a href="/register" class="text-primary">Sign up</a>
                        </p>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Loading -->
        <v-row v-else-if="store.loading">
            <v-col cols="12" lg="8">
                <v-skeleton-loader type="card" height="400"></v-skeleton-loader>
            </v-col>
            <v-col cols="12" lg="4">
                <v-skeleton-loader type="card" height="400"></v-skeleton-loader>
            </v-col>
        </v-row>

        <!-- Not Found -->
        <v-alert v-else type="error" variant="tonal">
            Service not found. <router-link to="/all-services">Browse all services</router-link>
        </v-alert>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '../stores/app'

const route = useRoute()
const store = useAppStore()

const service = ref(null)
const orderForm = ref({
    link: '',
    quantity: 100
})

const calculatedPrice = computed(() => {
    if (!service.value || !orderForm.value.quantity) return '0.00'
    const price = (parseFloat(service.value.rate) * orderForm.value.quantity) / 1000
    return price.toFixed(2)
})

const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(0) + 'K'
    return num?.toString() || '0'
}

const loadService = async () => {
    const id = route.params.id
    // Try to find in store first
    service.value = store.services.find(s => s.service_id == id)

    if (!service.value) {
        // Fetch from API
        service.value = await store.fetchService(id)
    }

    if (service.value) {
        orderForm.value.quantity = service.value.min || 100
    }
}

onMounted(loadService)
watch(() => route.params.id, loadService)
</script>

<style scoped>
.sticky-card {
    position: sticky;
    top: 80px;
}
</style>
