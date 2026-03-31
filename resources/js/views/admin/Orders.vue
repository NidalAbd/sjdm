<template>
    <div>
        <!-- Page Header -->
        <div class="d-flex align-center justify-space-between mb-4">
            <div>
                <h1 class="text-h5 font-weight-bold">Orders</h1>
                <p class="text-caption text-medium-emphasis mb-0">Manage and track all orders</p>
            </div>
            <div class="d-flex ga-2">
                <v-btn v-if="authStore.isAdmin" variant="tonal" @click="syncOrders" :loading="syncing" prepend-icon="mdi-sync" size="small">
                    Sync
                </v-btn>
                <v-btn color="primary" to="/admin/orders/create" prepend-icon="mdi-plus" size="small">
                    New Order
                </v-btn>
            </div>
        </div>

        <!-- Stats Row -->
        <v-row v-if="authStore.isAdmin" class="mb-4">
            <v-col cols="6" md="4">
                <v-card variant="outlined" class="h-100">
                    <v-card-text class="pa-4">
                        <div class="d-flex align-center justify-space-between mb-2">
                            <v-avatar color="info" variant="tonal" size="36" rounded="lg"><v-icon size="18">mdi-wallet</v-icon></v-avatar>
                        </div>
                        <div class="text-h5 font-weight-bold stat-value">${{ apiBalance }}</div>
                        <div class="text-caption text-medium-emphasis">API Balance</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6" md="4">
                <v-card variant="outlined" class="h-100">
                    <v-card-text class="pa-4">
                        <div class="d-flex align-center justify-space-between mb-2">
                            <v-avatar color="warning" variant="tonal" size="36" rounded="lg"><v-icon size="18">mdi-clock</v-icon></v-avatar>
                        </div>
                        <div class="text-h5 font-weight-bold stat-value">{{ pendingCount }}</div>
                        <div class="text-caption text-medium-emphasis">Pending</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="4">
                <v-card variant="outlined" class="h-100">
                    <v-card-text class="pa-4">
                        <div class="d-flex align-center justify-space-between mb-2">
                            <v-avatar color="success" variant="tonal" size="36" rounded="lg"><v-icon size="18">mdi-check-circle</v-icon></v-avatar>
                        </div>
                        <div class="text-h5 font-weight-bold stat-value">{{ completedCount }}</div>
                        <div class="text-caption text-medium-emphasis">Completed</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Filters -->
        <v-card class="mb-4" variant="outlined">
            <v-card-text class="pa-3">
                <v-row>
                    <v-col cols="12" sm="6" md="3">
                        <v-text-field
                            v-model="filters.search"
                            label="Search orders"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                            hide-details
                            @keyup.enter="fetchOrders"
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
                            @update:model-value="fetchOrders"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.platform"
                            :items="platformOptions"
                            label="Platform"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchOrders"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-btn color="primary" block @click="fetchOrders" prepend-icon="mdi-magnify">
                            Search
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Orders Table -->
        <v-card>
            <v-data-table-server
                v-model:items-per-page="itemsPerPage"
                v-model:page="page"
                :headers="headers"
                :items="orders"
                :items-length="totalOrders"
                :loading="loading"
                class="elevation-0"
                @update:options="onTableUpdate"
            >
                <!-- ID Column -->
                <template v-slot:item.id="{ item }">
                    <div class="d-flex align-center">
                        <span class="font-weight-bold">#{{ item.id }}</span>
                        <v-badge
                            v-if="item.unread_messages > 0"
                            :content="item.unread_messages"
                            color="error"
                            class="ml-2"
                        ></v-badge>
                    </div>
                </template>

                <!-- User Column -->
                <template v-slot:item.user="{ item }">
                    <div>
                        <div class="font-weight-medium">{{ item.user?.name || 'Deleted user' }}</div>
                        <div class="text-caption text-grey">{{ item.user?.email || '-' }}</div>
                    </div>
                </template>

                <!-- Charge Column -->
                <template v-slot:item.charge="{ item }">
                    <template v-if="item.system_status === 'refunded' && item.refunded_percent">
                        <div>
                            <s class="text-grey">${{ formatNumber(item.charge) }}</s>
                            <div class="text-success text-caption">
                                ${{ formatNumber(item.charge - (item.charge * item.refunded_percent / 100)) }}
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        ${{ formatNumber(item.charge) }}
                    </template>
                </template>

                <!-- Date Column -->
                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <!-- API Status Column (Admin Only) -->
                <template v-slot:item.status="{ item }">
                    <v-chip :color="getStatusColor(item.status)" size="small" variant="flat">
                        {{ item.status }}
                    </v-chip>
                </template>

                <!-- System Status Column -->
                <template v-slot:item.system_status="{ item }">
                    <v-chip
                        :color="getSystemStatusColor(item.system_status)"
                        size="small"
                        variant="flat"
                    >
                        {{ item.system_status || '-' }}
                    </v-chip>
                    <template v-if="item.system_status === 'refunded' && item.refunded_percent">
                        <div class="text-success text-caption mt-1">
                            ${{ formatNumber(item.charge * item.refunded_percent / 100) }} ({{ item.refunded_percent }}%)
                        </div>
                    </template>
                </template>

                <!-- Support Status Column -->
                <template v-slot:item.support_status="{ item }">
                    <template v-if="item.support_ticket">
                        <v-chip
                            :color="item.unread_messages > 0 ? 'error' : 'success'"
                            size="small"
                            variant="flat"
                        >
                            <v-icon start size="small">
                                {{ item.unread_messages > 0 ? 'mdi-exclamation-thick' : 'mdi-check' }}
                            </v-icon>
                            {{ item.unread_messages > 0 ? `${item.unread_messages} new` : 'Active' }}
                        </v-chip>
                    </template>
                    <template v-else>
                        <v-chip color="grey" size="small" variant="outlined">
                            No ticket
                        </v-chip>
                    </template>
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex ga-1">
                        <!-- View -->
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            @click="viewOrder(item)"
                        >
                            <v-icon>mdi-eye</v-icon>
                            <v-tooltip activator="parent" location="top">View Details</v-tooltip>
                        </v-btn>

                        <!-- Open Link -->
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            :href="item.link"
                            target="_blank"
                        >
                            <v-icon>mdi-open-in-new</v-icon>
                            <v-tooltip activator="parent" location="top">Open Link</v-tooltip>
                        </v-btn>

                        <!-- Admin Actions -->
                        <template v-if="authStore.isAdmin">
                            <!-- Complete (waiting orders) -->
                            <v-btn
                                v-if="item.status?.toLowerCase() === 'waiting'"
                                icon
                                size="small"
                                variant="text"
                                color="primary"
                                @click="completeOrder(item)"
                            >
                                <v-icon>mdi-check</v-icon>
                                <v-tooltip activator="parent" location="top">Mark Completed</v-tooltip>
                            </v-btn>

                            <!-- Refund -->
                            <v-btn
                                v-if="item.system_status !== 'refunded'"
                                icon
                                size="small"
                                variant="text"
                                color="success"
                                @click="openRefundDialog(item)"
                            >
                                <v-icon>mdi-cash-refund</v-icon>
                                <v-tooltip activator="parent" location="top">Refund</v-tooltip>
                            </v-btn>

                            <!-- Cancel -->
                            <v-btn
                                v-if="!['completed', 'canceled'].includes(item.status?.toLowerCase())"
                                icon
                                size="small"
                                variant="text"
                                color="warning"
                                @click="cancelOrder(item)"
                            >
                                <v-icon>mdi-cancel</v-icon>
                                <v-tooltip activator="parent" location="top">Cancel</v-tooltip>
                            </v-btn>
                        </template>

                        <!-- Support Ticket -->
                        <template v-if="!item.support_ticket">
                            <v-btn
                                icon
                                size="small"
                                variant="text"
                                color="info"
                                @click="openCreateTicketDialog(item)"
                            >
                                <v-icon>mdi-headset</v-icon>
                                <v-tooltip activator="parent" location="top">Create Ticket</v-tooltip>
                            </v-btn>
                        </template>
                        <template v-else>
                            <v-btn
                                icon
                                size="small"
                                variant="text"
                                :color="item.unread_messages > 0 ? 'warning' : 'info'"
                                :to="`/admin/support/${item.support_ticket.id}`"
                            >
                                <v-badge
                                    v-if="item.unread_messages > 0"
                                    :content="item.unread_messages"
                                    color="error"
                                >
                                    <v-icon>mdi-ticket</v-icon>
                                </v-badge>
                                <v-icon v-else>mdi-ticket</v-icon>
                                <v-tooltip activator="parent" location="top">View Ticket</v-tooltip>
                            </v-btn>
                        </template>

                        <!-- Delete (waiting orders) -->
                        <v-btn
                            v-if="item.status?.toLowerCase() === 'waiting'"
                            icon
                            size="small"
                            variant="text"
                            color="error"
                            @click="deleteOrder(item)"
                        >
                            <v-icon>mdi-delete</v-icon>
                            <v-tooltip activator="parent" location="top">Delete</v-tooltip>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- Order Details Dialog -->
        <v-dialog v-model="orderDialog" max-width="700">
            <v-card v-if="selectedOrder">
                <v-card-title>
                    <v-icon class="mr-2" size="20">mdi-shopping</v-icon>
                    Order #{{ selectedOrder.id }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-row>
                        <v-col cols="12" md="6">
                            <div class="text-caption text-grey">User</div>
                            <div class="font-weight-medium">{{ selectedOrder.user?.name || 'Deleted user' }}</div>
                            <div class="text-caption">#{{ selectedOrder.user?.id }} - {{ selectedOrder.user?.email }}</div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="text-caption text-grey">Date</div>
                            <div class="font-weight-medium">{{ formatDateTime(selectedOrder.created_at) }}</div>
                        </v-col>
                        <v-col cols="12">
                            <div class="text-caption text-grey">Service</div>
                            <div class="font-weight-medium">{{ selectedOrder.service?.name_en || '-' }}</div>
                        </v-col>
                        <v-col cols="12">
                            <div class="text-caption text-grey">Link</div>
                            <a :href="selectedOrder.link" target="_blank" class="text-primary">
                                {{ selectedOrder.link }}
                            </a>
                        </v-col>
                        <v-col cols="6" md="3">
                            <div class="text-caption text-grey">Quantity</div>
                            <div class="font-weight-bold text-h6">{{ selectedOrder.quantity }}</div>
                        </v-col>
                        <v-col cols="6" md="3">
                            <div class="text-caption text-grey">Charge</div>
                            <div class="font-weight-bold text-h6">${{ formatNumber(selectedOrder.charge) }}</div>
                        </v-col>
                        <v-col cols="6" md="3">
                            <div class="text-caption text-grey">Start Count</div>
                            <div class="font-weight-bold text-h6">{{ selectedOrder.start_count }}</div>
                        </v-col>
                        <v-col cols="6" md="3">
                            <div class="text-caption text-grey">Remains</div>
                            <div class="font-weight-bold text-h6">{{ selectedOrder.remains }}</div>
                        </v-col>
                        <v-col cols="6" md="4">
                            <div class="text-caption text-grey">API Status</div>
                            <v-chip :color="getStatusColor(selectedOrder.status)" class="mt-1">
                                {{ selectedOrder.status }}
                            </v-chip>
                        </v-col>
                        <v-col cols="6" md="4">
                            <div class="text-caption text-grey">System Status</div>
                            <v-chip :color="getSystemStatusColor(selectedOrder.system_status)" class="mt-1">
                                {{ selectedOrder.system_status || '-' }}
                            </v-chip>
                        </v-col>
                        <v-col cols="6" md="4">
                            <div class="text-caption text-grey">API Order ID</div>
                            <div class="font-weight-medium">{{ selectedOrder.api_order_id || '-' }}</div>
                        </v-col>
                        <v-col cols="12" v-if="selectedOrder.system_status === 'refunded' && selectedOrder.refunded_percent">
                            <v-alert type="success" variant="tonal">
                                <div class="font-weight-bold">Refund Details</div>
                                <div>
                                    Refunded: ${{ formatNumber(selectedOrder.charge * selectedOrder.refunded_percent / 100) }}
                                    ({{ selectedOrder.refunded_percent }}% of ${{ formatNumber(selectedOrder.charge) }})
                                </div>
                            </v-alert>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="orderDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Refund Dialog -->
        <v-dialog v-model="refundDialog" max-width="500">
            <v-card v-if="selectedOrder">
                <v-card-title>
                    <v-icon class="mr-2" size="20" color="success">mdi-cash-refund</v-icon>
                    Refund Order #{{ selectedOrder.id }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <div class="mb-4">
                        <div class="text-caption text-grey">Total Paid</div>
                        <div class="text-h5 font-weight-bold">${{ formatNumber(selectedOrder.charge) }}</div>
                    </div>
                    <div class="mb-4">
                        <span class="text-caption text-grey">Quantity:</span> {{ selectedOrder.quantity }} |
                        <span class="text-caption text-grey">Remains:</span> {{ selectedOrder.remains || 0 }}
                    </div>
                    <v-alert v-if="suggestedRefund > 0" type="info" variant="tonal" class="mb-4">
                        Suggested refund: <strong>${{ formatNumber(suggestedRefund) }}</strong>
                        ({{ suggestedRefundPercent }}%) based on remains.
                    </v-alert>
                    <v-text-field
                        v-model="refundAmount"
                        label="Refund Amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :max="selectedOrder.charge"
                        prefix="$"
                        variant="outlined"
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="refundDialog = false">Cancel</v-btn>
                    <v-btn color="success" @click="processRefund" :loading="processing">
                        Issue Refund
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Create Ticket Dialog -->
        <v-dialog v-model="ticketDialog" max-width="500">
            <v-card v-if="selectedOrder">
                <v-card-title>
                    <v-icon class="mr-2" size="20" color="info">mdi-headset</v-icon>
                    Create Support Ticket
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-text-field
                        v-model="ticketForm.subject"
                        label="Subject"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-textarea
                        v-model="ticketForm.message"
                        label="Message"
                        variant="outlined"
                        rows="4"
                    ></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="ticketDialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="createTicket" :loading="processing">
                        Submit Ticket
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const orders = ref([])
const totalOrders = ref(0)
const loading = ref(false)
const syncing = ref(false)
const processing = ref(false)
const page = ref(1)
const itemsPerPage = ref(15)

// Stats
const apiBalance = ref('0.00')
const pendingCount = ref(0)
const completedCount = ref(0)

// Filters
const filters = ref({
    search: '',
    status: null,
    platform: null,
})

const statusOptions = [
    { title: 'All', value: null },
    { title: 'Waiting', value: 'waiting' },
    { title: 'Pending', value: 'pending' },
    { title: 'In Progress', value: 'in_progress' },
    { title: 'Processing', value: 'processing' },
    { title: 'Completed', value: 'completed' },
    { title: 'Partial', value: 'partial' },
    { title: 'Canceled', value: 'canceled' },
]

const platformOptions = [
    { title: 'All', value: null },
    { title: 'Instagram', value: 'instagram' },
    { title: 'TikTok', value: 'tiktok' },
    { title: 'YouTube', value: 'youtube' },
    { title: 'Facebook', value: 'facebook' },
    { title: 'Twitter', value: 'twitter' },
    { title: 'Telegram', value: 'telegram' },
]

// Dialogs
const orderDialog = ref(false)
const refundDialog = ref(false)
const ticketDialog = ref(false)
const selectedOrder = ref(null)
const refundAmount = ref(0)
const ticketForm = ref({
    subject: '',
    message: '',
})

// Table headers
const headers = computed(() => {
    const cols = [
        { title: '#', key: 'id', width: '80px' },
        { title: 'User', key: 'user', sortable: false },
        { title: 'Quantity', key: 'quantity', align: 'end' },
        { title: 'Charge', key: 'charge', align: 'end' },
        { title: 'Start', key: 'start_count', align: 'end' },
        { title: 'Remains', key: 'remains', align: 'end' },
        { title: 'Date', key: 'created_at' },
    ]

    if (authStore.isAdmin) {
        cols.push({ title: 'API Status', key: 'status' })
    }

    cols.push(
        { title: 'Status', key: 'system_status' },
        { title: 'Support', key: 'support_status', sortable: false },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center', width: '200px' }
    )

    return cols
})

// Computed
const suggestedRefund = computed(() => {
    if (!selectedOrder.value) return 0
    const order = selectedOrder.value
    if (order.status?.toLowerCase() !== 'partial') return 0
    const quantity = Math.max(1, parseInt(order.quantity) || 1)
    const remains = parseInt(order.remains) || 0
    return (order.charge * remains / quantity).toFixed(2)
})

const suggestedRefundPercent = computed(() => {
    if (!selectedOrder.value) return 0
    const order = selectedOrder.value
    const quantity = Math.max(1, parseInt(order.quantity) || 1)
    const remains = parseInt(order.remains) || 0
    return ((remains / quantity) * 100).toFixed(1)
})

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
        'waiting': 'warning',
        'pending': 'orange',
        'processing': 'info',
        'in_progress': 'blue',
        'completed': 'success',
        'partial': 'orange-darken-2',
        'canceled': 'error',
    }
    return colors[status?.toLowerCase()] || 'grey'
}

const getSystemStatusColor = (status) => {
    const colors = {
        'refunded': 'success',
        'canceled': 'error',
        'active': 'info',
    }
    return colors[status?.toLowerCase()] || 'grey'
}

const fetchOrders = async () => {
    loading.value = true
    try {
        const params = {
            page: page.value,
            per_page: itemsPerPage.value,
            search: filters.value.search,
            status: filters.value.status,
            platform: filters.value.platform,
        }

        // Use admin endpoint if admin, otherwise regular endpoint
        const endpoint = authStore.isAdmin ? '/api/admin/orders' : '/api/orders'
        const response = await axios.get(endpoint, { params })
        orders.value = response.data.orders
        totalOrders.value = response.data.pagination?.total || 0

        // Update stats if admin
        if (authStore.isAdmin && response.data.stats) {
            apiBalance.value = response.data.stats.total_charge || '0.00'
            pendingCount.value = response.data.stats.pending || 0
            completedCount.value = response.data.stats.completed || 0
        }
    } catch (error) {
        console.error('Error fetching orders:', error)
        showSnackbar?.('Failed to load orders', 'error')
    } finally {
        loading.value = false
    }
}

const onTableUpdate = ({ page: newPage, itemsPerPage: newItemsPerPage }) => {
    page.value = newPage
    itemsPerPage.value = newItemsPerPage
    fetchOrders()
}

const syncOrders = async () => {
    syncing.value = true
    try {
        await axios.post('/api/admin/orders/sync-all')
        showSnackbar?.('Orders synchronized successfully', 'success')
        fetchOrders()
    } catch (error) {
        console.error('Error syncing orders:', error)
        showSnackbar?.('Failed to sync orders', 'error')
    } finally {
        syncing.value = false
    }
}

const viewOrder = (order) => {
    selectedOrder.value = order
    orderDialog.value = true
}

const openRefundDialog = (order) => {
    selectedOrder.value = order
    refundAmount.value = suggestedRefund.value
    refundDialog.value = true
}

const processRefund = async () => {
    if (!selectedOrder.value || refundAmount.value <= 0) return

    processing.value = true
    try {
        const percentage = (refundAmount.value / selectedOrder.value.charge) * 100
        await axios.post(`/api/admin/orders/${selectedOrder.value.id}/refund`, {
            percentage: percentage
        })
        showSnackbar?.('Refund processed successfully', 'success')
        refundDialog.value = false
        fetchOrders()
    } catch (error) {
        console.error('Error processing refund:', error)
        showSnackbar?.('Failed to process refund', 'error')
    } finally {
        processing.value = false
    }
}

const completeOrder = async (order) => {
    if (!confirm('Mark this order as completed?')) return

    try {
        await axios.put(`/api/admin/orders/${order.id}`, { status: 'completed' })
        showSnackbar?.('Order marked as completed', 'success')
        fetchOrders()
    } catch (error) {
        console.error('Error completing order:', error)
        showSnackbar?.('Failed to complete order', 'error')
    }
}

const cancelOrder = async (order) => {
    if (!confirm('Cancel this order?')) return

    try {
        await axios.post(`/api/admin/orders/${order.id}/cancel`)
        showSnackbar?.('Order canceled', 'success')
        fetchOrders()
    } catch (error) {
        console.error('Error canceling order:', error)
        showSnackbar?.('Failed to cancel order', 'error')
    }
}

const deleteOrder = async (order) => {
    if (!confirm('Delete this waiting order and refund full amount?')) return

    try {
        await axios.delete(`/api/admin/orders/${order.id}`)
        showSnackbar?.('Order deleted and refunded', 'success')
        fetchOrders()
    } catch (error) {
        console.error('Error deleting order:', error)
        showSnackbar?.('Failed to delete order', 'error')
    }
}

const openCreateTicketDialog = (order) => {
    selectedOrder.value = order
    ticketForm.value = { subject: '', message: '' }
    ticketDialog.value = true
}

const createTicket = async () => {
    if (!selectedOrder.value || !ticketForm.value.subject || !ticketForm.value.message) return

    processing.value = true
    try {
        await axios.post('/support', {
            ticketable_id: selectedOrder.value.id,
            ticketable_type: 'App\\Models\\Order',
            type: 'order',
            subject: ticketForm.value.subject,
            message: ticketForm.value.message,
        })
        showSnackbar?.('Support ticket created', 'success')
        ticketDialog.value = false
        fetchOrders()
    } catch (error) {
        console.error('Error creating ticket:', error)
        showSnackbar?.('Failed to create ticket', 'error')
    } finally {
        processing.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchOrders()
})
</script>
