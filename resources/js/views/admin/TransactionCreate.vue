<template>
    <div class="order-page">
        <PageHeader title="Add Balance" icon="mdi-credit-card-plus" subtitle="Top up your account balance">
            <template #actions>
                <v-btn icon variant="text" to="/admin/transactions">
                    <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
            </template>
        </PageHeader>

        <v-row>
            <!-- Main Content -->
            <v-col cols="12" lg="8">
                <!-- Amount Selection -->
                <v-card class="mb-4 hover-lift" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="primary" size="42" class="mr-3">
                                <v-icon color="white">mdi-cash-plus</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">Select Amount</div>
                                <div class="text-caption text-medium-emphasis">Choose or enter how much to add</div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <v-btn
                                v-for="amount in quickAmounts"
                                :key="amount"
                                :color="selectedAmount === amount ? 'primary' : undefined"
                                :variant="selectedAmount === amount ? 'flat' : 'outlined'"
                                rounded="lg"
                                @click="selectAmount(amount)"
                            >
                                ${{ amount }}
                            </v-btn>
                        </div>

                        <v-text-field
                            v-model="customAmount"
                            label="Custom Amount"
                            type="number"
                            :min="selectedMethodData?.min_amount || 5"
                            :max="selectedMethodData?.max_amount || undefined"
                            step="1"
                            prefix="$"
                            variant="solo-filled"
                            flat
                            rounded="lg"
                            hide-details="auto"
                            :hint="amountHint"
                            persistent-hint
                            @update:model-value="onCustomAmountChange"
                        ></v-text-field>
                    </v-card-text>
                </v-card>

                <!-- Payment Method Selection -->
                <v-card class="hover-lift" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="secondary" size="42" class="mr-3">
                                <v-icon color="white">mdi-wallet-outline</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">Select Payment Method</div>
                                <div class="text-caption text-medium-emphasis">Choose how you'd like to pay</div>
                            </div>
                        </div>

                        <div v-if="loadingMethods" class="d-flex justify-center py-6">
                            <v-progress-circular indeterminate color="primary"></v-progress-circular>
                        </div>

                        <v-radio-group v-else v-model="selectedMethod" hide-details class="method-group">
                            <div
                                v-for="method in paymentMethods"
                                :key="method.id"
                                class="method-item"
                                :class="{ 'method-item-active': selectedMethod === method.id }"
                                @click="selectedMethod = method.id"
                            >
                                <v-radio :value="method.id" hide-details class="mr-3"></v-radio>
                                <v-avatar size="40" class="mr-3" color="grey-darken-3">
                                    <v-img v-if="method.logo" :src="`/storage/${method.logo}`" :alt="method.name"></v-img>
                                    <v-icon v-else color="white">{{ methodIcon(method) }}</v-icon>
                                </v-avatar>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold">{{ method.name }}</div>
                                    <div class="text-caption text-medium-emphasis method-desc">{{ method.description }}</div>
                                    <div class="text-caption mt-1">
                                        <span class="opacity-60">Limits:</span> ${{ formatNumber(method.min_amount) }} &ndash; ${{ formatNumber(method.max_amount) }}
                                    </div>
                                </div>
                                <v-chip v-if="method.bonus_percentage > 0" color="success" size="small">
                                    +{{ method.bonus_percentage }}% bonus
                                </v-chip>
                            </div>
                            <div v-if="!paymentMethods.length" class="text-center py-6 text-medium-emphasis">
                                No payment methods are available right now.
                            </div>
                        </v-radio-group>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Sidebar -->
            <v-col cols="12" lg="4">
                <v-card class="summary-card mb-4" rounded="xl">
                    <div class="summary-header">
                        <v-icon class="mr-2">mdi-receipt-text-outline</v-icon>
                        Summary
                    </div>
                    <v-card-text class="pa-5">
                        <div class="summary-items">
                            <div class="summary-row">
                                <span class="label">Amount</span>
                                <span class="value">${{ formatNumber(finalAmount) }}</span>
                            </div>
                            <div v-if="bonusAmount > 0" class="summary-row text-success">
                                <span class="label">Bonus</span>
                                <span class="value">+${{ formatNumber(bonusAmount) }}</span>
                            </div>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <div class="total-row">
                            <span>You'll receive</span>
                            <span class="total-amount">${{ formatNumber(finalAmount + bonusAmount) }}</span>
                        </div>

                        <v-btn
                            color="primary"
                            size="x-large"
                            block
                            rounded="lg"
                            class="mt-5 submit-btn"
                            :loading="processing"
                            :disabled="!canProceed"
                            @click="proceedToPayment"
                        >
                            <v-icon start>mdi-lock</v-icon>
                            Proceed to Payment
                        </v-btn>

                        <div class="text-center mt-3 text-caption text-medium-emphasis">
                            <v-icon size="small">mdi-shield-check</v-icon>
                            Secure payment
                        </div>
                    </v-card-text>
                </v-card>

                <v-card class="balance-card" rounded="xl">
                    <v-card-text class="pa-5">
                        <div class="d-flex align-center justify-space-between">
                            <div class="d-flex align-center">
                                <v-avatar color="success" size="38" class="mr-3">
                                    <v-icon color="white" size="20">mdi-wallet</v-icon>
                                </v-avatar>
                                <span class="text-subtitle-2">Current Balance</span>
                            </div>
                            <span class="balance-amount text-success">${{ formatNumber(authStore.userBalance) }}</span>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth'
import PageHeader from '../../components/PageHeader.vue'
import axios from 'axios'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

const quickAmounts = [10, 25, 50, 100, 250, 500]
const selectedAmount = ref(null)
const customAmount = ref('')
const selectedMethod = ref(null)
const processing = ref(false)
const loadingMethods = ref(true)
const paymentMethods = ref([])

const finalAmount = computed(() => selectedAmount.value || parseFloat(customAmount.value) || 0)

const selectedMethodData = computed(() => paymentMethods.value.find(m => m.id === selectedMethod.value))

const bonusAmount = computed(() => {
    if (!selectedMethodData.value?.bonus_percentage) return 0
    return (finalAmount.value * selectedMethodData.value.bonus_percentage) / 100
})

const amountHint = computed(() => {
    if (!selectedMethodData.value) return ''
    return `Limits for ${selectedMethodData.value.name}: $${formatNumber(selectedMethodData.value.min_amount)} - $${formatNumber(selectedMethodData.value.max_amount)}`
})

const canProceed = computed(() => {
    if (!selectedMethodData.value) return false
    const min = parseFloat(selectedMethodData.value.min_amount) || 5
    const max = parseFloat(selectedMethodData.value.max_amount) || Infinity
    return finalAmount.value >= min && finalAmount.value <= max
})

const formatNumber = (num) => new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num || 0)

const methodIcon = (method) => {
    const type = (method.type || '').toLowerCase()
    if (type.includes('crypto')) return 'mdi-bitcoin'
    if (type.includes('card') || type.includes('stripe')) return 'mdi-credit-card'
    if (type.includes('paypal')) return 'mdi-alpha-p-box'
    return 'mdi-wallet'
}

const selectAmount = (amount) => {
    selectedAmount.value = amount
    customAmount.value = ''
}

const onCustomAmountChange = () => { selectedAmount.value = null }

const proceedToPayment = async () => {
    if (!canProceed.value) return
    processing.value = true
    try {
        const response = await axios.post('/transactions/checkout', {
            amount: finalAmount.value,
            payment_method: selectedMethod.value,
        })
        if (response.data.checkout_url) {
            window.location.href = response.data.checkout_url
        } else {
            showSnackbar?.('Payment initiated', 'success')
        }
    } catch (error) {
        console.error('Error initiating payment:', error)
        showSnackbar?.(error.response?.data?.message || 'Failed to initiate payment', 'error')
    } finally {
        processing.value = false
    }
}

const fetchPaymentMethods = async () => {
    loadingMethods.value = true
    try {
        const response = await axios.get('/api/payment-methods')
        paymentMethods.value = response.data.methods || []
        if (paymentMethods.value.length) selectedMethod.value = paymentMethods.value[0].id
    } catch (error) {
        console.error('Error fetching payment methods:', error)
    } finally {
        loadingMethods.value = false
    }
}

onMounted(() => { fetchPaymentMethods() })
</script>

<style scoped>
.method-group { display: flex; flex-direction: column; gap: 10px; }
.method-item {
    display: flex; align-items: center; padding: 14px; border-radius: 14px; cursor: pointer;
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    transition: border-color 0.15s ease, background 0.15s ease;
}
.method-item:hover { border-color: rgba(var(--v-theme-primary), 0.3); }
.method-item-active { border-color: rgb(var(--v-theme-primary)); background: rgba(var(--v-theme-primary), 0.06); }
.method-desc { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
