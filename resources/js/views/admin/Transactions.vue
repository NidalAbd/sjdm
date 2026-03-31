<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center flex-wrap ga-2">
                <v-icon class="mr-2">mdi-cash-multiple</v-icon>
                <span>Transactions</span>
                <v-spacer></v-spacer>
                <v-btn color="success" to="/admin/transactions/create" prepend-icon="mdi-plus">
                    Add Balance
                </v-btn>
            </v-card-title>
        </v-card>

        <!-- Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="3">
                        <v-text-field
                            v-model="filters.search"
                            label="Search transactions"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                            hide-details
                            @keyup.enter="fetchTransactions"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.status"
                            :items="statusOptions"
                            label="Status"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchTransactions"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.type"
                            :items="typeOptions"
                            label="Type"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchTransactions"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-btn color="primary" block @click="fetchTransactions" prepend-icon="mdi-magnify">
                            Search
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Transactions Table -->
        <v-card>
            <v-data-table-server
                v-model:items-per-page="itemsPerPage"
                v-model:page="page"
                :headers="headers"
                :items="transactions"
                :items-length="totalTransactions"
                :loading="loading"
                class="elevation-0"
                @update:options="onTableUpdate"
            >
                <!-- ID Column -->
                <template v-slot:item.id="{ item }">
                    <span class="font-weight-bold">#{{ item.id }}</span>
                </template>

                <!-- User Column -->
                <template v-slot:item.user="{ item }">
                    <div>
                        <div class="font-weight-medium">{{ item.user?.name || 'Unknown' }}</div>
                        <div class="text-caption text-grey">{{ item.user?.email || '-' }}</div>
                    </div>
                </template>

                <!-- Amount Column -->
                <template v-slot:item.amount="{ item }">
                    <span :class="item.type === 'credit' ? 'text-success' : 'text-error'" class="font-weight-bold">
                        {{ item.type === 'credit' ? '+' : '-' }}${{ formatNumber(item.amount) }}
                    </span>
                </template>

                <!-- Type Column -->
                <template v-slot:item.type="{ item }">
                    <v-chip
                        :color="item.type === 'credit' ? 'success' : 'error'"
                        size="small"
                        variant="flat"
                    >
                        <v-icon start size="small">
                            {{ item.type === 'credit' ? 'mdi-arrow-down' : 'mdi-arrow-up' }}
                        </v-icon>
                        {{ item.type === 'credit' ? 'Credit' : 'Debit' }}
                    </v-chip>
                </template>

                <!-- Status Column -->
                <template v-slot:item.status="{ item }">
                    <v-chip
                        :color="getStatusColor(item.status)"
                        size="small"
                        variant="flat"
                    >
                        {{ item.status }}
                    </v-chip>
                </template>

                <!-- Payment Method Column -->
                <template v-slot:item.payment_method="{ item }">
                    <v-chip v-if="item.payment_method" size="small" variant="outlined">
                        {{ item.payment_method }}
                    </v-chip>
                    <span v-else class="text-grey">-</span>
                </template>

                <!-- Date Column -->
                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex ga-1">
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="info"
                            @click="viewTransaction(item)"
                        >
                            <v-icon>mdi-eye</v-icon>
                            <v-tooltip activator="parent" location="top">View Details</v-tooltip>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- Transaction Details Dialog -->
        <v-dialog v-model="transactionDialog" max-width="600">
            <v-card v-if="selectedTransaction">
                <v-card-title class="bg-primary text-white">
                    <v-icon class="mr-2">mdi-cash</v-icon>
                    Transaction #{{ selectedTransaction.id }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-row>
                        <v-col cols="12" md="6">
                            <div class="text-caption text-grey">User</div>
                            <div class="font-weight-medium">{{ selectedTransaction.user?.name || 'Unknown' }}</div>
                            <div class="text-caption">{{ selectedTransaction.user?.email || '-' }}</div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="text-caption text-grey">Date</div>
                            <div class="font-weight-medium">{{ formatDateTime(selectedTransaction.created_at) }}</div>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <div class="text-h5 font-weight-bold" :class="selectedTransaction.type === 'credit' ? 'text-success' : 'text-error'">
                                    {{ selectedTransaction.type === 'credit' ? '+' : '-' }}${{ formatNumber(selectedTransaction.amount) }}
                                </div>
                                <div class="text-caption text-grey">Amount</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <v-chip :color="selectedTransaction.type === 'credit' ? 'success' : 'error'" class="mb-1">
                                    {{ selectedTransaction.type }}
                                </v-chip>
                                <div class="text-caption text-grey">Type</div>
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <v-chip :color="getStatusColor(selectedTransaction.status)" class="mb-1">
                                    {{ selectedTransaction.status }}
                                </v-chip>
                                <div class="text-caption text-grey">Status</div>
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="6" v-if="selectedTransaction.payment_method">
                            <div class="text-caption text-grey">Payment Method</div>
                            <div class="font-weight-medium">{{ selectedTransaction.payment_method }}</div>
                        </v-col>
                        <v-col cols="12" md="6" v-if="selectedTransaction.transaction_id">
                            <div class="text-caption text-grey">Transaction ID</div>
                            <div class="font-weight-medium">{{ selectedTransaction.transaction_id }}</div>
                        </v-col>
                        <v-col cols="12" v-if="selectedTransaction.description">
                            <div class="text-caption text-grey">Description</div>
                            <div>{{ selectedTransaction.description }}</div>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="transactionDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const transactions = ref([])
const totalTransactions = ref(0)
const loading = ref(false)
const page = ref(1)
const itemsPerPage = ref(15)

// Filters
const filters = ref({
    search: '',
    status: null,
    type: null,
})

const statusOptions = [
    { title: 'All', value: null },
    { title: 'Completed', value: 'completed' },
    { title: 'Pending', value: 'pending' },
    { title: 'Failed', value: 'failed' },
    { title: 'Canceled', value: 'canceled' },
]

const typeOptions = [
    { title: 'All', value: null },
    { title: 'Credit', value: 'credit' },
    { title: 'Debit', value: 'debit' },
]

// Dialogs
const transactionDialog = ref(false)
const selectedTransaction = ref(null)

// Table headers
const headers = [
    { title: '#', key: 'id', width: '80px' },
    { title: 'User', key: 'user', sortable: false },
    { title: 'Amount', key: 'amount', align: 'end' },
    { title: 'Type', key: 'type' },
    { title: 'Status', key: 'status' },
    { title: 'Payment Method', key: 'payment_method' },
    { title: 'Date', key: 'created_at' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

// Methods
const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (date) => {
    return new Date(date).toLocaleString()
}

const getStatusColor = (status) => {
    const colors = {
        'completed': 'success',
        'pending': 'warning',
        'failed': 'error',
        'canceled': 'grey',
    }
    return colors[status?.toLowerCase()] || 'grey'
}

const fetchTransactions = async () => {
    loading.value = true
    try {
        const params = {
            page: page.value,
            per_page: itemsPerPage.value,
            search: filters.value.search,
            status: filters.value.status,
            type: filters.value.type,
        }

        // Use admin endpoint if admin
        const endpoint = authStore.isAdmin ? '/api/admin/transactions' : '/api/transactions'
        const response = await axios.get(endpoint, { params })
        transactions.value = response.data.transactions || []
        totalTransactions.value = response.data.pagination?.total || 0
    } catch (error) {
        console.error('Error fetching transactions:', error)
        showSnackbar?.('Failed to load transactions', 'error')
    } finally {
        loading.value = false
    }
}

const onTableUpdate = ({ page: newPage, itemsPerPage: newItemsPerPage }) => {
    page.value = newPage
    itemsPerPage.value = newItemsPerPage
    fetchTransactions()
}

const viewTransaction = (transaction) => {
    selectedTransaction.value = transaction
    transactionDialog.value = true
}

// Lifecycle
onMounted(() => {
    fetchTransactions()
})
</script>
