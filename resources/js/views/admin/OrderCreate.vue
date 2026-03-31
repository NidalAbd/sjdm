<template>
    <div class="order-page">
        <v-row>
            <!-- Main Content -->
            <v-col cols="12" lg="8">
                <!-- Platform Selection Card -->
                <v-card class="mb-4 hover-lift" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="primary" size="42" class="mr-3">
                                <v-icon color="white">mdi-apps</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">Select Platform</div>
                                <div class="text-caption text-medium-emphasis">Choose your target platform</div>
                            </div>
                        </div>
                        <div class="platform-grid">
                            <div
                                v-for="platform in platforms"
                                :key="platform.id"
                                class="platform-item"
                                :class="{ 'active': selectedPlatform === platform.id }"
                                @click="selectPlatform(platform.id)"
                            >
                                <v-icon :color="selectedPlatform === platform.id ? 'white' : platform.color" size="24">
                                    {{ platform.icon }}
                                </v-icon>
                                <span class="platform-name">{{ platform.name }}</span>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Service Selection Card -->
                <v-card class="mb-4 hover-lift" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="secondary" size="42" class="mr-3">
                                <v-icon color="white">mdi-cog</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">Choose Service</div>
                                <div class="text-caption text-medium-emphasis">Select category and service</div>
                            </div>
                        </div>

                        <v-row>
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="selectedCategory"
                                    :items="categories"
                                    label="Category"
                                    variant="solo-filled"
                                    flat
                                    rounded="lg"
                                    :loading="loadingCategories"
                                    :disabled="!selectedPlatform"
                                    hide-details
                                    @update:model-value="onCategoryChange"
                                >
                                    <template v-slot:prepend-inner>
                                        <v-icon color="primary" size="20">mdi-folder</v-icon>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="selectedService"
                                    :items="services"
                                    item-title="name"
                                    item-value="service_id"
                                    label="Service"
                                    variant="solo-filled"
                                    flat
                                    rounded="lg"
                                    :loading="loadingServices"
                                    :disabled="!selectedCategory"
                                    return-object
                                    hide-details
                                    @update:model-value="onServiceChange"
                                >
                                    <template v-slot:prepend-inner>
                                        <v-icon color="primary" size="20">mdi-star</v-icon>
                                    </template>
                                    <template v-slot:selection="{ item }">
                                        <span class="text-truncate">{{ item.raw.name }}</span>
                                    </template>
                                    <template v-slot:item="{ item, props }">
                                        <v-list-item v-bind="props" :title="item.raw.name">
                                            <template v-slot:append>
                                                <v-chip color="success" size="small" variant="tonal">
                                                    ${{ formatRate(item.raw.rate) }}
                                                </v-chip>
                                            </template>
                                        </v-list-item>
                                    </template>
                                </v-select>
                            </v-col>
                        </v-row>

                        <!-- Service Info Chips -->
                        <div v-if="selectedService" class="service-info-bar mt-4">
                            <div class="info-chip">
                                <v-icon size="16">mdi-arrow-collapse-down</v-icon>
                                <span>Min: <strong>{{ selectedService.min?.toLocaleString() }}</strong></span>
                            </div>
                            <div class="info-chip">
                                <v-icon size="16">mdi-arrow-collapse-up</v-icon>
                                <span>Max: <strong>{{ selectedService.max?.toLocaleString() }}</strong></span>
                            </div>
                            <div class="info-chip success">
                                <v-icon size="16">mdi-cash</v-icon>
                                <span>Rate: <strong>${{ formatRate(selectedService.rate) }}</strong>/1k</span>
                            </div>
                            <div v-if="selectedService.refill" class="info-chip refill">
                                <v-icon size="16">mdi-refresh</v-icon>
                                <span>Refill</span>
                            </div>
                            <div v-if="selectedService.cancel" class="info-chip cancel">
                                <v-icon size="16">mdi-close-circle</v-icon>
                                <span>Cancel</span>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Order Form Card -->
                <v-card class="mb-4 hover-lift" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="info" size="42" class="mr-3">
                                <v-icon color="white">mdi-text-box-edit</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">Order Details</div>
                                <div class="text-caption text-medium-emphasis">Fill in your order information</div>
                            </div>
                        </div>

                        <!-- Description Box -->
                        <div class="description-box mb-5">
                            <div class="description-header">
                                <span>Service Description</span>
                                <v-chip v-if="selectedService" size="small" color="primary" variant="flat">
                                    ID: {{ selectedService.service_id }}
                                </v-chip>
                            </div>
                            <div class="description-content">
                                {{ descriptionText || 'Select a service to see description' }}
                            </div>
                        </div>

                        <!-- Link Input -->
                        <div class="input-group mb-4">
                            <label class="input-label">
                                <v-icon size="18" class="mr-1">mdi-link</v-icon>
                                Link / URL
                            </label>
                            <v-text-field
                                v-model="form.link"
                                variant="solo-filled"
                                flat
                                rounded="lg"
                                placeholder="https://example.com/your-link"
                                :rules="[v => !!v || 'Link is required']"
                                hide-details="auto"
                            >
                                <template v-slot:append-inner>
                                    <v-btn v-if="form.link" icon size="small" variant="text" @click="form.link = ''">
                                        <v-icon size="18">mdi-close-circle</v-icon>
                                    </v-btn>
                                </template>
                            </v-text-field>
                        </div>

                        <!-- Quantity Input -->
                        <div class="input-group mb-4">
                            <label class="input-label">
                                <v-icon size="18" class="mr-1">mdi-numeric</v-icon>
                                Quantity
                                <span v-if="selectedService" class="text-medium-emphasis ml-2">
                                    ({{ selectedService.min?.toLocaleString() }} - {{ selectedService.max?.toLocaleString() }})
                                </span>
                            </label>
                            <v-text-field
                                v-model.number="form.quantity"
                                type="number"
                                variant="solo-filled"
                                flat
                                rounded="lg"
                                :placeholder="`Enter quantity`"
                                :min="selectedService?.min || 100"
                                :max="selectedService?.max || 10000"
                                :rules="quantityRules"
                                hide-details="auto"
                                @update:model-value="calculateCharge"
                            >
                                <template v-slot:append-inner>
                                    <div class="quantity-controls">
                                        <v-btn icon size="small" variant="tonal" color="error" @click="adjustQuantity(-100)">
                                            <v-icon size="18">mdi-minus</v-icon>
                                        </v-btn>
                                        <v-btn icon size="small" variant="tonal" color="success" @click="adjustQuantity(100)">
                                            <v-icon size="18">mdi-plus</v-icon>
                                        </v-btn>
                                    </div>
                                </template>
                            </v-text-field>
                            <!-- Quick Select -->
                            <div class="quick-select mt-3">
                                <v-btn
                                    v-for="qty in quickQuantities"
                                    :key="qty"
                                    size="small"
                                    :variant="form.quantity === qty ? 'flat' : 'tonal'"
                                    :color="form.quantity === qty ? 'primary' : 'default'"
                                    rounded="lg"
                                    @click="setQuantity(qty)"
                                >
                                    {{ qty >= 1000 ? (qty/1000) + 'K' : qty }}
                                </v-btn>
                            </div>
                        </div>

                        <!-- Charge Display -->
                        <div class="charge-display mb-4">
                            <div class="charge-label">
                                <v-icon size="20" class="mr-2">mdi-calculator</v-icon>
                                Total Charge
                                <v-chip v-if="selectedService" size="x-small" color="info" variant="tonal" class="ml-2">
                                    ${{ formatRate(selectedService.rate) }}/1000
                                </v-chip>
                            </div>
                            <div class="charge-value">
                                <span class="currency">$</span>
                                <span class="amount">{{ charge ? charge.toFixed(5) : '0.00000' }}</span>
                            </div>
                        </div>

                        <!-- Average Time Display -->
                        <div class="time-display">
                            <div class="time-item">
                                <v-icon color="info" size="20">mdi-clock-start</v-icon>
                                <div>
                                    <div class="time-label">Start Time</div>
                                    <div class="time-value">{{ startTime }}</div>
                                </div>
                            </div>
                            <div class="time-divider"></div>
                            <div class="time-item">
                                <v-icon color="success" size="20">mdi-speedometer</v-icon>
                                <div>
                                    <div class="time-label">Speed</div>
                                    <div class="time-value">{{ speed }}</div>
                                </div>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Sidebar -->
            <v-col cols="12" lg="4">
                <!-- Order Summary Card -->
                <v-card class="summary-card mb-4" rounded="xl">
                    <div class="summary-header">
                        <v-icon class="mr-2">mdi-receipt-text-outline</v-icon>
                        Order Summary
                    </div>
                    <v-card-text class="pa-5">
                        <div class="summary-items">
                            <div class="summary-row">
                                <span class="label">Platform</span>
                                <span class="value">
                                    <v-icon size="16" :color="currentPlatform?.color" class="mr-1">{{ currentPlatform?.icon }}</v-icon>
                                    {{ currentPlatform?.name || '-' }}
                                </span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Service ID</span>
                                <v-chip v-if="selectedService" size="x-small" color="primary">#{{ selectedService.service_id }}</v-chip>
                                <span v-else class="value">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Quantity</span>
                                <span class="value">{{ (form.quantity || 0).toLocaleString() }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Rate</span>
                                <span class="value">${{ selectedService ? formatRate(selectedService.rate) : '0.00' }}/1k</span>
                            </div>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <div class="total-row">
                            <span>Total</span>
                            <span class="total-amount">${{ formatBalance(charge) }}</span>
                        </div>

                        <v-btn
                            color="primary"
                            size="x-large"
                            block
                            rounded="lg"
                            class="mt-5 submit-btn"
                            :loading="submitting"
                            :disabled="!canSubmit"
                            @click="submitOrder"
                        >
                            <v-icon start>mdi-cart-check</v-icon>
                            Place Order
                        </v-btn>
                    </v-card-text>
                </v-card>

                <!-- Balance Card -->
                <v-card class="balance-card mb-4" rounded="xl" :class="{ 'insufficient': insufficientBalance }">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center justify-space-between mb-3">
                            <div class="d-flex align-center">
                                <v-avatar :color="insufficientBalance ? 'error' : 'success'" size="38" class="mr-3">
                                    <v-icon color="white" size="20">mdi-wallet</v-icon>
                                </v-avatar>
                                <span class="text-subtitle-2">Your Balance</span>
                            </div>
                            <span class="balance-amount" :class="insufficientBalance ? 'text-error' : 'text-success'">
                                ${{ formatBalance(authStore.userBalance) }}
                            </span>
                        </div>

                        <div class="balance-bar">
                            <div class="balance-fill" :style="{ width: balancePercentage + '%' }"></div>
                        </div>

                        <div class="balance-info mt-3">
                            <span>Order: ${{ formatBalance(charge) }}</span>
                            <span>After: ${{ formatBalance(Math.max(authStore.userBalance - charge, 0)) }}</span>
                        </div>

                        <v-btn
                            v-if="insufficientBalance"
                            color="error"
                            variant="tonal"
                            block
                            rounded="lg"
                            class="mt-4"
                            to="/admin/transactions/create"
                        >
                            <v-icon start>mdi-plus</v-icon>
                            Add Funds
                        </v-btn>
                    </v-card-text>
                </v-card>

                <!-- Tips Card -->
                <v-card class="tips-card" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center mb-3">
                            <v-avatar color="warning" size="38" class="mr-3">
                                <v-icon color="white" size="20">mdi-lightbulb-on</v-icon>
                            </v-avatar>
                            <span class="text-subtitle-2 font-weight-bold">Pro Tips</span>
                        </div>
                        <div class="tips-list">
                            <div class="tip-item">
                                <v-icon color="success" size="16">mdi-check-circle</v-icon>
                                <span>Make sure your profile is public</span>
                            </div>
                            <div class="tip-item">
                                <v-icon color="success" size="16">mdi-check-circle</v-icon>
                                <span>Use the correct link format</span>
                            </div>
                            <div class="tip-item">
                                <v-icon color="success" size="16">mdi-check-circle</v-icon>
                                <span>Avoid duplicate orders</span>
                            </div>
                            <div class="tip-item">
                                <v-icon color="info" size="16">mdi-information</v-icon>
                                <span>🔥 Top service &bull; ♻ Refill &bull; 🛑 Cancel</span>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Platforms
const platforms = [
    { id: 'all', name: 'All', icon: 'mdi-apps', color: 'grey' },
    { id: 'instagram', name: 'Instagram', icon: 'mdi-instagram', color: 'pink' },
    { id: 'tiktok', name: 'TikTok', icon: 'mdi-music-note', color: 'purple' },
    { id: 'youtube', name: 'YouTube', icon: 'mdi-youtube', color: 'red' },
    { id: 'facebook', name: 'Facebook', icon: 'mdi-facebook', color: 'blue' },
    { id: 'twitter', name: 'Twitter', icon: 'mdi-twitter', color: 'light-blue' },
    { id: 'telegram', name: 'Telegram', icon: 'mdi-send', color: 'cyan' },
    { id: 'snapchat', name: 'Snapchat', icon: 'mdi-snapchat', color: 'amber' },
    { id: 'linkedin', name: 'LinkedIn', icon: 'mdi-linkedin', color: 'indigo' },
    { id: 'spotify', name: 'Spotify', icon: 'mdi-spotify', color: 'green' },
    { id: 'discord', name: 'Discord', icon: 'mdi-discord', color: 'deep-purple' },
    { id: 'twitch', name: 'Twitch', icon: 'mdi-twitch', color: 'purple' },
    { id: 'threads', name: 'Threads', icon: 'mdi-at', color: 'grey-darken-2' },
    { id: 'reviews', name: 'Reviews', icon: 'mdi-star', color: 'orange' },
    { id: 'traffic', name: 'Traffic', icon: 'mdi-web', color: 'teal' },
]

// State
const categories = ref([])
const services = ref([])
const loadingCategories = ref(false)
const loadingServices = ref(false)
const submitting = ref(false)
const selectedPlatform = ref('all')
const selectedCategory = ref(null)
const selectedService = ref(null)
const form = ref({ link: '', quantity: null })
const charge = ref(0)

// Quick quantities
const quickQuantities = [100, 500, 1000, 5000, 10000]

// Computed
const currentPlatform = computed(() => platforms.find(p => p.id === selectedPlatform.value))
const insufficientBalance = computed(() => charge.value > authStore.userBalance)

const balancePercentage = computed(() => {
    if (!charge.value || !authStore.userBalance) return 0
    return Math.min((charge.value / authStore.userBalance) * 100, 100)
})

const quantityRules = computed(() => {
    const min = selectedService.value?.min || 1
    const max = selectedService.value?.max || 100000
    return [
        v => !!v || 'Required',
        v => v >= min || `Min: ${min}`,
        v => v <= max || `Max: ${max}`,
    ]
})

const canSubmit = computed(() => {
    const s = selectedService.value
    return s && form.value.link && form.value.quantity >= (s?.min || 1) &&
           form.value.quantity <= (s?.max || 100000) && charge.value > 0 && !insufficientBalance.value
})

const descriptionText = computed(() => {
    if (!selectedService.value) return ''
    return `${selectedService.value.name}\n\n• Use the correct link format for this service\n• Avoid placing duplicate orders on the same link`
})

const startTime = computed(() => {
    if (!selectedService.value) return 'N/A'
    const name = selectedService.value.name || ''
    const match = name.match(/\[Start time: ([^\]]+)\]|\[وقت البدا: ([^\]]+)\]/i)
    return match ? (match[1] || match[2]) : 'Instant'
})

const speed = computed(() => {
    if (!selectedService.value) return 'N/A'
    const name = selectedService.value.name || ''
    const match = name.match(/\[Speed: ([^\]]+)\]|\[السرعة: ([^\]]+)\]/i)
    return match ? (match[1] || match[2]) : 'Fast'
})

// Format helpers
const formatRate = (rate) => Number(rate || 0).toFixed(4)
const formatBalance = (val) => Number(val || 0).toFixed(2)

// Methods
const selectPlatform = (id) => {
    selectedPlatform.value = id
    loadCategories(id)
}

const adjustQuantity = (amount) => {
    const min = selectedService.value?.min || 100
    const max = selectedService.value?.max || 10000
    form.value.quantity = Math.max(min, Math.min(max, (form.value.quantity || min) + amount))
    calculateCharge()
}

const setQuantity = (qty) => {
    const max = selectedService.value?.max || 10000
    form.value.quantity = Math.min(qty, max)
    calculateCharge()
}

const calculateCharge = () => {
    charge.value = selectedService.value && form.value.quantity
        ? (selectedService.value.rate / 1000) * form.value.quantity
        : 0
}

const loadCategories = async (platform) => {
    loadingCategories.value = true
    categories.value = []
    selectedCategory.value = null
    services.value = []
    selectedService.value = null
    charge.value = 0

    try {
        const { data } = await axios.get(`/api/orders/getCategories?platform=${platform}`)
        categories.value = data || []
        if (categories.value.length) {
            selectedCategory.value = categories.value[0]
            loadServices(platform, categories.value[0])
        }
    } catch (e) {
        showSnackbar?.('Failed to load categories', 'error')
    } finally {
        loadingCategories.value = false
    }
}

const loadServices = async (platform, category) => {
    loadingServices.value = true
    services.value = []
    selectedService.value = null

    try {
        const { data } = await axios.get(`/api/orders/getServices?platform=${platform}&category=${encodeURIComponent(category)}`)
        services.value = data || []
        if (services.value.length) {
            selectedService.value = services.value[0]
            onServiceChange()
        }
    } catch (e) {
        showSnackbar?.('Failed to load services', 'error')
    } finally {
        loadingServices.value = false
    }
}

const onCategoryChange = () => {
    if (selectedCategory.value) {
        loadServices(selectedPlatform.value, selectedCategory.value)
    } else {
        services.value = []
        selectedService.value = null
        charge.value = 0
    }
}

const onServiceChange = () => {
    if (selectedService.value) {
        form.value.quantity = selectedService.value.min || 100
        calculateCharge()
    } else {
        form.value.quantity = null
        charge.value = 0
    }
}

const submitOrder = async () => {
    if (!canSubmit.value) return
    submitting.value = true
    try {
        await axios.post('/api/orders', {
            service_id: selectedService.value.service_id,
            link: form.value.link,
            quantity: form.value.quantity,
        })
        showSnackbar?.('Order placed successfully!', 'success')
        await authStore.fetchUser()
        router.push('/admin/orders')
    } catch (e) {
        showSnackbar?.(e.response?.data?.message || 'Failed to place order', 'error')
    } finally {
        submitting.value = false
    }
}

onMounted(() => loadCategories('all'))
</script>

