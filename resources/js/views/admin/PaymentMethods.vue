<template>
    <div>
        <PageHeader title="Payment Methods" subtitle="Manage payment gateways" icon="mdi-credit-card">
            <template #actions>
                <v-btn color="primary" @click="openCreateDialog" prepend-icon="mdi-plus">
                    Add Payment Method
                </v-btn>
            </template>
        </PageHeader>

        <!-- Stats Cards -->
        <StatsRow :stats="statsData" />

        <v-card>
            <v-data-table
                :headers="headers"
                :items="paymentMethods"
                :loading="loading"
                :search="search"
                class="elevation-0"
            >
                <template v-slot:top>
                    <v-text-field
                        v-model="search"
                        label="Search payment methods"
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        class="ma-4"
                        style="max-width: 400px"
                        hide-details
                    ></v-text-field>
                </template>

                <template v-slot:item.icon="{ item }">
                    <v-avatar :color="item.color || 'grey'" size="40">
                        <v-icon color="white">{{ item.icon || 'mdi-credit-card' }}</v-icon>
                    </v-avatar>
                </template>

                <template v-slot:item.is_active="{ item }">
                    <v-switch
                        v-model="item.is_active"
                        color="success"
                        hide-details
                        density="compact"
                        @change="toggleStatus(item)"
                    ></v-switch>
                </template>

                <template v-slot:item.min_amount="{ item }">
                    ${{ formatNumber(item.min_amount) }}
                </template>

                <template v-slot:item.max_amount="{ item }">
                    ${{ formatNumber(item.max_amount) }}
                </template>

                <template v-slot:item.fee="{ item }">
                    <v-chip
                        v-if="item.fee > 0"
                        size="small"
                        color="warning"
                        variant="tonal"
                    >
                        {{ item.fee_type === 'percentage' ? `${item.fee}%` : `$${item.fee}` }}
                    </v-chip>
                    <span v-else class="text-grey">No fee</span>
                </template>

                <template v-slot:item.bonus="{ item }">
                    <v-chip
                        v-if="item.bonus > 0"
                        size="small"
                        color="success"
                        variant="tonal"
                    >
                        +{{ item.bonus }}%
                    </v-chip>
                    <span v-else class="text-grey">-</span>
                </template>

                <template v-slot:item.sort_order="{ item }">
                    <div class="d-flex align-center ga-1">
                        <v-btn
                            icon
                            size="x-small"
                            variant="text"
                            @click="moveUp(item)"
                            :disabled="item.sort_order <= 1"
                        >
                            <v-icon size="small">mdi-arrow-up</v-icon>
                        </v-btn>
                        <span>{{ item.sort_order }}</span>
                        <v-btn
                            icon
                            size="x-small"
                            variant="text"
                            @click="moveDown(item)"
                        >
                            <v-icon size="small">mdi-arrow-down</v-icon>
                        </v-btn>
                    </div>
                </template>

                <template v-slot:item.actions="{ item }">
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="info"
                        @click="viewMethod(item)"
                    >
                        <v-icon>mdi-eye</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="warning"
                        @click="editMethod(item)"
                    >
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="error"
                        @click="deleteMethod(item)"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>

        <!-- Create/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="700" persistent>
            <v-card>
                <v-card-title>
                    <v-icon class="mr-2">{{ editMode ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
                    {{ editMode ? 'Edit Payment Method' : 'Add Payment Method' }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="form.name"
                                label="Name"
                                variant="outlined"
                                :rules="[v => !!v || 'Name is required']"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="form.code"
                                label="Code"
                                variant="outlined"
                                hint="Unique identifier (e.g., stripe, paypal)"
                                persistent-hint
                                :disabled="editMode"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea
                                v-model="form.description"
                                label="Description"
                                variant="outlined"
                                rows="2"
                            ></v-textarea>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="form.icon"
                                :items="availableIcons"
                                label="Icon"
                                variant="outlined"
                            >
                                <template v-slot:selection="{ item }">
                                    <v-icon class="mr-2">{{ item.value }}</v-icon>
                                    {{ item.title }}
                                </template>
                                <template v-slot:item="{ item, props }">
                                    <v-list-item v-bind="props">
                                        <template v-slot:prepend>
                                            <v-icon>{{ item.value }}</v-icon>
                                        </template>
                                    </v-list-item>
                                </template>
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="form.color"
                                :items="availableColors"
                                label="Color"
                                variant="outlined"
                            >
                                <template v-slot:selection="{ item }">
                                    <v-chip :color="item.value" size="small" class="mr-2"></v-chip>
                                    {{ item.title }}
                                </template>
                                <template v-slot:item="{ item, props }">
                                    <v-list-item v-bind="props">
                                        <template v-slot:prepend>
                                            <v-chip :color="item.value" size="small"></v-chip>
                                        </template>
                                    </v-list-item>
                                </template>
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="form.min_amount"
                                label="Minimum Amount"
                                type="number"
                                variant="outlined"
                                prefix="$"
                                min="0"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="form.max_amount"
                                label="Maximum Amount"
                                type="number"
                                variant="outlined"
                                prefix="$"
                                min="0"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field
                                v-model.number="form.fee"
                                label="Fee"
                                type="number"
                                variant="outlined"
                                min="0"
                                step="0.01"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-select
                                v-model="form.fee_type"
                                :items="[
                                    { title: 'Percentage', value: 'percentage' },
                                    { title: 'Fixed', value: 'fixed' }
                                ]"
                                label="Fee Type"
                                variant="outlined"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field
                                v-model.number="form.bonus"
                                label="Bonus %"
                                type="number"
                                variant="outlined"
                                suffix="%"
                                min="0"
                                hint="Bonus percentage for deposits"
                                persistent-hint
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="form.sort_order"
                                label="Sort Order"
                                type="number"
                                variant="outlined"
                                min="1"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-switch
                                v-model="form.is_active"
                                label="Active"
                                color="success"
                                hide-details
                            ></v-switch>
                        </v-col>
                    </v-row>

                    <v-divider class="my-4"></v-divider>

                    <div class="text-subtitle-2 mb-3">API Configuration</div>
                    <v-row>
                        <v-col cols="12">
                            <v-textarea
                                v-model="form.config"
                                label="Configuration (JSON)"
                                variant="outlined"
                                rows="4"
                                hint="Enter API keys and settings in JSON format"
                                persistent-hint
                            ></v-textarea>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="closeDialog">Cancel</v-btn>
                    <v-btn color="primary" @click="saveMethod" :loading="saving">
                        {{ editMode ? 'Update' : 'Create' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- View Dialog -->
        <v-dialog v-model="viewDialog" max-width="500">
            <v-card v-if="selectedMethod">
                <v-card-title class="d-flex align-center">
                    <v-avatar :color="selectedMethod.color || 'grey'" size="40" class="mr-3">
                        <v-icon color="white">{{ selectedMethod.icon || 'mdi-credit-card' }}</v-icon>
                    </v-avatar>
                    {{ selectedMethod.name }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-list density="compact">
                        <v-list-item>
                            <v-list-item-title>Code</v-list-item-title>
                            <template v-slot:append>
                                <code>{{ selectedMethod.code }}</code>
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Description</v-list-item-title>
                            <template v-slot:append>
                                <span class="text-grey text-truncate" style="max-width: 200px">
                                    {{ selectedMethod.description || '-' }}
                                </span>
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Status</v-list-item-title>
                            <template v-slot:append>
                                <v-chip
                                    :color="selectedMethod.is_active ? 'success' : 'error'"
                                    size="small"
                                >
                                    {{ selectedMethod.is_active ? 'Active' : 'Inactive' }}
                                </v-chip>
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Min Amount</v-list-item-title>
                            <template v-slot:append>
                                ${{ formatNumber(selectedMethod.min_amount) }}
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Max Amount</v-list-item-title>
                            <template v-slot:append>
                                ${{ formatNumber(selectedMethod.max_amount) }}
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Fee</v-list-item-title>
                            <template v-slot:append>
                                <span v-if="selectedMethod.fee > 0">
                                    {{ selectedMethod.fee_type === 'percentage' ? `${selectedMethod.fee}%` : `$${selectedMethod.fee}` }}
                                </span>
                                <span v-else class="text-grey">No fee</span>
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Bonus</v-list-item-title>
                            <template v-slot:append>
                                <v-chip v-if="selectedMethod.bonus > 0" color="success" size="small">
                                    +{{ selectedMethod.bonus }}%
                                </v-chip>
                                <span v-else class="text-grey">No bonus</span>
                            </template>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>Sort Order</v-list-item-title>
                            <template v-slot:append>
                                {{ selectedMethod.sort_order }}
                            </template>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="viewDialog = false">Close</v-btn>
                    <v-btn color="warning" @click="viewDialog = false; editMethod(selectedMethod)">
                        Edit
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import axios from 'axios'
import PageHeader from '../../components/PageHeader.vue'
import StatsRow from '../../components/StatsRow.vue'

const showSnackbar = inject('showSnackbar')

const paymentMethods = ref([])
const loading = ref(false)
const saving = ref(false)
const dialog = ref(false)
const viewDialog = ref(false)
const editMode = ref(false)
const search = ref('')
const selectedMethod = ref(null)

const defaultForm = {
    id: null,
    name: '',
    code: '',
    description: '',
    icon: 'mdi-credit-card',
    color: 'blue',
    min_amount: 5,
    max_amount: 10000,
    fee: 0,
    fee_type: 'percentage',
    bonus: 0,
    sort_order: 1,
    is_active: true,
    config: '{}',
}

const form = ref({ ...defaultForm })

const headers = [
    { title: '', key: 'icon', width: '60px', sortable: false },
    { title: 'Name', key: 'name' },
    { title: 'Code', key: 'code' },
    { title: 'Min', key: 'min_amount' },
    { title: 'Max', key: 'max_amount' },
    { title: 'Fee', key: 'fee', align: 'center' },
    { title: 'Bonus', key: 'bonus', align: 'center' },
    { title: 'Order', key: 'sort_order', align: 'center', width: '120px' },
    { title: 'Active', key: 'is_active', align: 'center' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

const availableIcons = [
    { title: 'Credit Card', value: 'mdi-credit-card' },
    { title: 'Credit Card Outline', value: 'mdi-credit-card-outline' },
    { title: 'Bank', value: 'mdi-bank' },
    { title: 'Bitcoin', value: 'mdi-bitcoin' },
    { title: 'Ethereum', value: 'mdi-ethereum' },
    { title: 'Currency USD', value: 'mdi-currency-usd' },
    { title: 'PayPal', value: 'mdi-alpha-p-box' },
    { title: 'Wallet', value: 'mdi-wallet' },
    { title: 'Cash', value: 'mdi-cash' },
    { title: 'QR Code', value: 'mdi-qrcode' },
]

const availableColors = [
    { title: 'Blue', value: 'blue' },
    { title: 'Green', value: 'green' },
    { title: 'Red', value: 'red' },
    { title: 'Orange', value: 'orange' },
    { title: 'Purple', value: 'purple' },
    { title: 'Indigo', value: 'indigo' },
    { title: 'Teal', value: 'teal' },
    { title: 'Cyan', value: 'cyan' },
    { title: 'Pink', value: 'pink' },
    { title: 'Grey', value: 'grey' },
]

const statsData = computed(() => [
    { value: paymentMethods.value.length, label: 'Total Methods', icon: 'mdi-credit-card-multiple', color: 'primary' },
    { value: activeMethods.value, label: 'Active', icon: 'mdi-check-circle', color: 'success' },
    { value: inactiveMethods.value, label: 'Inactive', icon: 'mdi-pause-circle', color: 'warning' },
    { value: methodsWithBonus.value, label: 'With Bonus', icon: 'mdi-gift', color: 'info' },
])

// Computed
const activeMethods = computed(() => paymentMethods.value.filter(m => m.is_active).length)
const inactiveMethods = computed(() => paymentMethods.value.filter(m => !m.is_active).length)
const methodsWithBonus = computed(() => paymentMethods.value.filter(m => m.bonus > 0).length)

// Methods
const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0)
}

const fetchPaymentMethods = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/admin/payment-methods')
        paymentMethods.value = response.data.payment_methods || []
    } catch (error) {
        console.error('Error fetching payment methods:', error)
        showSnackbar?.('Failed to load payment methods', 'error')
    } finally {
        loading.value = false
    }
}

const openCreateDialog = () => {
    editMode.value = false
    form.value = { ...defaultForm }
    form.value.sort_order = paymentMethods.value.length + 1
    dialog.value = true
}

const editMethod = (method) => {
    editMode.value = true
    form.value = {
        ...method,
        config: typeof method.config === 'object' ? JSON.stringify(method.config, null, 2) : method.config || '{}'
    }
    dialog.value = true
}

const viewMethod = (method) => {
    selectedMethod.value = method
    viewDialog.value = true
}

const closeDialog = () => {
    dialog.value = false
    form.value = { ...defaultForm }
}

const saveMethod = async () => {
    if (!form.value.name || !form.value.code) {
        showSnackbar?.('Please fill in required fields', 'warning')
        return
    }

    // Validate JSON config
    try {
        JSON.parse(form.value.config || '{}')
    } catch (e) {
        showSnackbar?.('Invalid JSON configuration', 'error')
        return
    }

    saving.value = true
    try {
        const payload = {
            ...form.value,
            config: JSON.parse(form.value.config || '{}')
        }

        if (editMode.value) {
            await axios.put(`/api/admin/payment-methods/${form.value.id}`, payload)
            showSnackbar?.('Payment method updated successfully', 'success')
        } else {
            await axios.post('/api/admin/payment-methods', payload)
            showSnackbar?.('Payment method created successfully', 'success')
        }
        closeDialog()
        fetchPaymentMethods()
    } catch (error) {
        console.error('Error saving payment method:', error)
        showSnackbar?.(error.response?.data?.message || 'Failed to save payment method', 'error')
    } finally {
        saving.value = false
    }
}

const deleteMethod = async (method) => {
    if (!confirm(`Are you sure you want to delete "${method.name}"?`)) return

    try {
        await axios.delete(`/api/admin/payment-methods/${method.id}`)
        showSnackbar?.('Payment method deleted successfully', 'success')
        fetchPaymentMethods()
    } catch (error) {
        console.error('Error deleting payment method:', error)
        showSnackbar?.('Failed to delete payment method', 'error')
    }
}

const toggleStatus = async (method) => {
    try {
        await axios.patch(`/api/admin/payment-methods/${method.id}/toggle`)
        showSnackbar?.(`Payment method ${method.is_active ? 'activated' : 'deactivated'}`, 'success')
    } catch (error) {
        console.error('Error toggling status:', error)
        method.is_active = !method.is_active // Revert
        showSnackbar?.('Failed to update status', 'error')
    }
}

const moveUp = async (method) => {
    try {
        await axios.post(`/api/admin/payment-methods/${method.id}/move-up`)
        fetchPaymentMethods()
    } catch (error) {
        console.error('Error moving up:', error)
    }
}

const moveDown = async (method) => {
    try {
        await axios.post(`/api/admin/payment-methods/${method.id}/move-down`)
        fetchPaymentMethods()
    } catch (error) {
        console.error('Error moving down:', error)
    }
}

onMounted(() => {
    fetchPaymentMethods()
})
</script>
