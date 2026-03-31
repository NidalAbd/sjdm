<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center">
                <v-btn icon variant="text" to="/admin/transactions" class="mr-2">
                    <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
                <v-icon class="mr-2">mdi-credit-card-plus</v-icon>
                Add Balance
            </v-card-title>
        </v-card>

        <v-row justify="center">
            <v-col cols="12" md="8" lg="6">
                <!-- Current Balance -->
                <v-card class="mb-4" color="primary" variant="flat">
                    <v-card-text class="text-white text-center py-6">
                        <div class="text-caption mb-1">Current Balance</div>
                        <div class="text-h3 font-weight-bold">
                            ${{ formatNumber(authStore.userBalance) }}
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Add Balance Form -->
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2" size="small">mdi-cash-plus</v-icon>
                        Select Amount
                    </v-card-title>
                    <v-card-text>
                        <!-- Quick Amount Buttons -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <v-btn
                                v-for="amount in quickAmounts"
                                :key="amount"
                                :color="selectedAmount === amount ? 'primary' : 'grey-lighten-2'"
                                :variant="selectedAmount === amount ? 'flat' : 'outlined'"
                                @click="selectAmount(amount)"
                            >
                                ${{ amount }}
                            </v-btn>
                        </div>

                        <!-- Custom Amount -->
                        <v-text-field
                            v-model="customAmount"
                            label="Custom Amount"
                            type="number"
                            min="5"
                            step="1"
                            prefix="$"
                            variant="outlined"
                            class="mb-4"
                            @update:model-value="onCustomAmountChange"
                        ></v-text-field>

                        <v-divider class="my-4"></v-divider>

                        <!-- Payment Method Selection -->
                        <div class="text-subtitle-1 font-weight-bold mb-3">Select Payment Method</div>

                        <v-radio-group v-model="selectedMethod" class="mb-4">
                            <v-card
                                v-for="method in paymentMethods"
                                :key="method.id"
                                :variant="selectedMethod === method.id ? 'tonal' : 'outlined'"
                                :color="selectedMethod === method.id ? 'primary' : undefined"
                                class="mb-2 pa-3"
                                @click="selectedMethod = method.id"
                            >
                                <div class="d-flex align-center">
                                    <v-radio :value="method.id" hide-details class="mr-3"></v-radio>
                                    <v-avatar size="40" class="mr-3" :color="method.color || 'grey'">
                                        <v-icon color="white">{{ method.icon }}</v-icon>
                                    </v-avatar>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold">{{ method.name }}</div>
                                        <div class="text-caption text-grey">{{ method.description }}</div>
                                    </div>
                                    <v-chip v-if="method.bonus" color="success" size="small">
                                        +{{ method.bonus }}% bonus
                                    </v-chip>
                                </div>
                            </v-card>
                        </v-radio-group>

                        <!-- Summary -->
                        <v-card variant="tonal" color="success" class="mb-4">
                            <v-card-text>
                                <div class="d-flex justify-space-between mb-2">
                                    <span>Amount</span>
                                    <span>${{ formatNumber(finalAmount) }}</span>
                                </div>
                                <div v-if="bonusAmount > 0" class="d-flex justify-space-between mb-2 text-success">
                                    <span>Bonus</span>
                                    <span>+${{ formatNumber(bonusAmount) }}</span>
                                </div>
                                <v-divider class="my-2"></v-divider>
                                <div class="d-flex justify-space-between">
                                    <span class="font-weight-bold">You'll receive</span>
                                    <span class="text-h6 font-weight-bold">
                                        ${{ formatNumber(finalAmount + bonusAmount) }}
                                    </span>
                                </div>
                            </v-card-text>
                        </v-card>

                        <v-btn
                            color="success"
                            size="large"
                            block
                            @click="proceedToPayment"
                            :loading="processing"
                            :disabled="!canProceed"
                        >
                            <v-icon start>mdi-lock</v-icon>
                            Proceed to Payment
                        </v-btn>

                        <div class="text-center mt-3 text-caption text-grey">
                            <v-icon size="small">mdi-shield-check</v-icon>
                            Secure payment powered by Stripe
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
import axios from 'axios'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const quickAmounts = [10, 25, 50, 100, 250, 500]
const selectedAmount = ref(null)
const customAmount = ref('')
const selectedMethod = ref(null)
const processing = ref(false)

const paymentMethods = ref([
    {
        id: 'stripe',
        name: 'Credit/Debit Card',
        description: 'Visa, Mastercard, American Express',
        icon: 'mdi-credit-card',
        color: 'blue',
        bonus: 0,
    },
    {
        id: 'paypal',
        name: 'PayPal',
        description: 'Pay with your PayPal account',
        icon: 'mdi-alpha-p-box',
        color: 'indigo',
        bonus: 0,
    },
    {
        id: 'crypto',
        name: 'Cryptocurrency',
        description: 'Bitcoin, Ethereum, USDT',
        icon: 'mdi-bitcoin',
        color: 'orange',
        bonus: 5,
    },
])

// Computed
const finalAmount = computed(() => {
    return selectedAmount.value || parseFloat(customAmount.value) || 0
})

const selectedMethodData = computed(() => {
    return paymentMethods.value.find(m => m.id === selectedMethod.value)
})

const bonusAmount = computed(() => {
    if (!selectedMethodData.value || !selectedMethodData.value.bonus) return 0
    return (finalAmount.value * selectedMethodData.value.bonus) / 100
})

const canProceed = computed(() => {
    return finalAmount.value >= 5 && selectedMethod.value
})

// Methods
const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0)
}

const selectAmount = (amount) => {
    selectedAmount.value = amount
    customAmount.value = ''
}

const onCustomAmountChange = () => {
    selectedAmount.value = null
}

const proceedToPayment = async () => {
    if (!canProceed.value) return

    processing.value = true
    try {
        const response = await axios.post('/transactions/checkout', {
            amount: finalAmount.value,
            payment_method: selectedMethod.value,
        })

        if (response.data.checkout_url) {
            // Redirect to payment gateway
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
    try {
        const response = await axios.get('/api/payment-methods')
        if (response.data.methods) {
            paymentMethods.value = response.data.methods
        }
    } catch (error) {
        console.error('Error fetching payment methods:', error)
    }
}

// Lifecycle
onMounted(() => {
    fetchPaymentMethods()
})
</script>
