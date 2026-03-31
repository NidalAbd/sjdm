<template>
    <div class="order-page" :class="{ 'dark-mode': appStore.isDark }">
        <v-row>
            <!-- Main Content -->
            <v-col cols="12" lg="8">
                <!-- Platform Selection Card -->
                <v-card class="mb-4 card-hover" rounded="xl">
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
                <v-card class="mb-4 card-hover" rounded="xl">
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
                <v-card class="mb-4 card-hover" rounded="xl">
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
import { useAppStore } from '../../stores/app'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const appStore = useAppStore()
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

<style scoped>
.order-page {
    max-width: 1400px;
    margin: 0 auto;
}

/* Card Hover Effect */
.card-hover {
    transition: box-shadow 0.2s ease;
}
.card-hover:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
}

/* Platform Grid */
.platform-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 10px;
}

.platform-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 14px 8px;
    border-radius: 12px;
    background: #f5f5f5;
    cursor: pointer;
    transition: all 0.2s ease;
    gap: 6px;
}

.platform-item:hover {
    background: #eeeeee;
    transform: translateY(-2px);
}

.platform-item.active {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.platform-name {
    font-size: 11px;
    font-weight: 500;
    text-align: center;
}

/* Service Info Bar */
.service-info-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 12px;
}

.info-chip {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: white;
    border-radius: 8px;
    font-size: 12px;
    border: 1px solid #e2e8f0;
}

.info-chip.success { background: #ecfdf5; border-color: #a7f3d0; color: #059669; }
.info-chip.refill { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.info-chip.cancel { background: #fef3c7; border-color: #fcd34d; color: #d97706; }

/* Description Box */
.description-box {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.description-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8fafc;
    font-weight: 600;
    font-size: 13px;
}

.description-content {
    padding: 16px;
    font-size: 13px;
    color: #64748b;
    white-space: pre-line;
    line-height: 1.6;
    min-height: 80px;
}

/* Input Group */
.input-group {
    margin-bottom: 20px;
}

.input-label {
    display: flex;
    align-items: center;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

/* Quantity Controls */
.quantity-controls {
    display: flex;
    gap: 4px;
}

/* Quick Select */
.quick-select {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* Charge Display */
.charge-display {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.charge-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #065f46;
}

.charge-value {
    display: flex;
    align-items: baseline;
}

.charge-value .currency {
    font-size: 18px;
    font-weight: 600;
    color: #059669;
}

.charge-value .amount {
    font-size: 28px;
    font-weight: 700;
    color: #059669;
}

/* Time Display */
.time-display {
    display: flex;
    align-items: center;
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    gap: 20px;
}

.time-item {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.time-label {
    font-size: 11px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.time-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.time-divider {
    width: 1px;
    height: 40px;
    background: #e2e8f0;
}

/* Summary Card */
.summary-card {
    overflow: hidden;
}

.summary-header {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 16px 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.summary-items {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.summary-row .label {
    color: #64748b;
    font-size: 13px;
}

.summary-row .value {
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 16px;
    font-weight: 600;
}

.total-amount {
    font-size: 26px;
    font-weight: 700;
    color: #6366f1;
}

.submit-btn {
    font-weight: 600;
    letter-spacing: 0.5px;
    height: 52px !important;
}

/* Balance Card */
.balance-card {
    border: 2px solid #a7f3d0;
    background: linear-gradient(135deg, #f0fdf4, #ffffff);
}

.balance-card.insufficient {
    border-color: #fecaca;
    background: linear-gradient(135deg, #fef2f2, #ffffff);
}

.balance-amount {
    font-size: 22px;
    font-weight: 700;
}

.balance-bar {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.balance-fill {
    height: 100%;
    background: linear-gradient(90deg, #22c55e, #16a34a);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.balance-card.insufficient .balance-fill {
    background: linear-gradient(90deg, #ef4444, #dc2626);
}

.balance-info {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #64748b;
}

/* Tips Card */
.tips-card {
    background: linear-gradient(135deg, #fffbeb, #ffffff);
    border: 2px solid #fde68a;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.tip-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: #64748b;
}

/* Responsive */
@media (max-width: 960px) {
    .platform-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .charge-value .amount {
        font-size: 22px;
    }

    .time-display {
        flex-direction: column;
        align-items: stretch;
    }

    .time-divider {
        width: 100%;
        height: 1px;
    }
}

@media (max-width: 600px) {
    .platform-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .header-card .text-h4 {
        font-size: 1.5rem !important;
    }
}

/* ============================================
   DARK MODE STYLES
   ============================================ */
.dark-mode .platform-item {
    background: #2d2d3a;
    color: #e0e0e0;
}

.dark-mode .platform-item:hover {
    background: #3d3d4a;
}

.dark-mode .platform-item.active {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.dark-mode .service-info-bar {
    background: #1e1e2e;
}

.dark-mode .info-chip {
    background: #2d2d3a;
    border-color: #3d3d4a;
    color: #e0e0e0;
}

.dark-mode .info-chip.success {
    background: #1a3a2a;
    border-color: #2d5a3d;
    color: #4ade80;
}

.dark-mode .info-chip.refill {
    background: #1a2a3a;
    border-color: #2d4a5d;
    color: #60a5fa;
}

.dark-mode .info-chip.cancel {
    background: #3a2a1a;
    border-color: #5d4a2d;
    color: #fbbf24;
}

.dark-mode .description-box {
    border-color: #3d3d4a;
}

.dark-mode .description-header {
    background: #1e1e2e;
    color: #e0e0e0;
}

.dark-mode .description-content {
    background: #2d2d3a;
    color: #a0a0a0;
}

.dark-mode .input-label {
    color: #e0e0e0;
}

.dark-mode .charge-display {
    background: linear-gradient(135deg, #1a3a2a, #1e4030);
}

.dark-mode .charge-label {
    color: #4ade80;
}

.dark-mode .charge-value .currency,
.dark-mode .charge-value .amount {
    color: #4ade80;
}

.dark-mode .time-display {
    background: #1e1e2e;
}

.dark-mode .time-label {
    color: #888;
}

.dark-mode .time-value {
    color: #e0e0e0;
}

.dark-mode .time-divider {
    background: #3d3d4a;
}

.dark-mode .summary-row .label {
    color: #a0a0a0;
}

.dark-mode .summary-row .value {
    color: #e0e0e0;
}

.dark-mode .total-row {
    color: #e0e0e0;
}

.dark-mode .balance-card {
    border-color: #2d5a3d;
    background: linear-gradient(135deg, #1a2a1e, #1e1e2e);
}

.dark-mode .balance-card.insufficient {
    border-color: #5a2d2d;
    background: linear-gradient(135deg, #2a1a1a, #1e1e2e);
}

.dark-mode .balance-bar {
    background: #3d3d4a;
}

.dark-mode .balance-info {
    color: #a0a0a0;
}

.dark-mode .tips-card {
    background: linear-gradient(135deg, #2a2a1a, #1e1e2e);
    border-color: #5d5a2d;
}

.dark-mode .tip-item {
    color: #a0a0a0;
}
</style>
