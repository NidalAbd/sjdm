<template>
    <div>
        <PageHeader title="Support" subtitle="Manage support tickets" icon="mdi-headset">
            <template #actions>
                <v-btn color="primary" @click="openCreateDialog" prepend-icon="mdi-plus">
                    New Ticket
                </v-btn>
            </template>
        </PageHeader>

        <!-- Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            v-model="filters.search"
                            label="Search tickets"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                            hide-details
                            @keyup.enter="fetchTickets"
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
                            @update:model-value="fetchTickets"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.priority"
                            :items="priorityOptions"
                            label="Priority"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchTickets"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                        <v-btn color="primary" block @click="fetchTickets" prepend-icon="mdi-magnify">
                            Search
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Tickets List -->
        <v-card>
            <!-- Empty State when no tickets -->
            <div v-if="!loading && tickets.length === 0" class="text-center py-12">
                <v-icon size="80" color="grey-lighten-1">mdi-ticket-outline</v-icon>
                <div class="text-h5 mt-4 text-grey-darken-1">No support tickets</div>
                <div class="text-body-1 text-grey mb-6">Create a new ticket to get started</div>
                <v-btn color="primary" size="large" @click="openCreateDialog" prepend-icon="mdi-plus">
                    New Ticket
                </v-btn>
            </div>

            <v-data-table-server
                v-else
                v-model:items-per-page="itemsPerPage"
                v-model:page="page"
                :headers="headers"
                :items="tickets"
                :items-length="totalTickets"
                :loading="loading"
                class="elevation-0"
                @update:options="onTableUpdate"
            >
                <!-- ID Column -->
                <template v-slot:item.id="{ item }">
                    <div class="d-flex align-center">
                        <span class="font-weight-bold">#{{ item.id }}</span>
                        <v-badge
                            v-if="item.unread_count > 0"
                            :content="item.unread_count"
                            color="error"
                            class="ml-2"
                        ></v-badge>
                    </div>
                </template>

                <!-- Subject Column -->
                <template v-slot:item.subject="{ item }">
                    <div>
                        <div class="font-weight-medium">{{ item.subject }}</div>
                        <div class="text-caption text-grey">
                            {{ item.user?.name || 'Unknown' }} - {{ item.user?.email || '-' }}
                        </div>
                    </div>
                </template>

                <!-- Status Column -->
                <template v-slot:item.status="{ item }">
                    <v-chip
                        :color="getStatusColor(item.status?.name || item.status)"
                        size="small"
                        variant="flat"
                    >
                        {{ item.status?.name || item.status || 'Unknown' }}
                    </v-chip>
                </template>

                <!-- Priority Column -->
                <template v-slot:item.priority="{ item }">
                    <v-chip
                        :color="getPriorityColor(item.priority)"
                        size="small"
                        variant="outlined"
                    >
                        {{ item.priority || 'normal' }}
                    </v-chip>
                </template>

                <!-- Related To Column -->
                <template v-slot:item.related="{ item }">
                    <v-chip v-if="item.ticketable_type" size="small" variant="tonal" color="info">
                        <v-icon start size="small">{{ getRelatedIcon(item.ticketable_type) }}</v-icon>
                        {{ getRelatedLabel(item.ticketable_type) }} #{{ item.ticketable_id }}
                    </v-chip>
                    <span v-else class="text-grey">General</span>
                </template>

                <!-- Messages Column -->
                <template v-slot:item.messages_count="{ item }">
                    <v-chip size="small" variant="tonal">
                        <v-icon start size="small">mdi-message</v-icon>
                        {{ item.messages_count || 0 }}
                    </v-chip>
                </template>

                <!-- Date Column -->
                <template v-slot:item.created_at="{ item }">
                    <div>
                        <div>{{ formatDate(item.created_at) }}</div>
                        <div class="text-caption text-grey">{{ formatTime(item.created_at) }}</div>
                    </div>
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex ga-1">
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            :color="item.unread_count > 0 ? 'warning' : 'info'"
                            :to="`/admin/support/${item.id}`"
                        >
                            <v-badge v-if="item.unread_count > 0" :content="item.unread_count" color="error">
                                <v-icon>mdi-message-text</v-icon>
                            </v-badge>
                            <v-icon v-else>mdi-message-text</v-icon>
                            <v-tooltip activator="parent" location="top">View Conversation</v-tooltip>
                        </v-btn>
                        <v-btn
                            v-if="(item.status?.name || item.status) !== 'closed'"
                            icon
                            size="small"
                            variant="text"
                            color="success"
                            @click="closeTicket(item)"
                        >
                            <v-icon>mdi-check-circle</v-icon>
                            <v-tooltip activator="parent" location="top">Close Ticket</v-tooltip>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- Create Ticket Dialog -->
        <v-dialog v-model="createDialog" max-width="600">
            <v-card>
                <v-card-title>
                    <v-icon class="mr-2">mdi-plus</v-icon>
                    Create Support Ticket
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-text-field
                        v-model="newTicket.subject"
                        label="Subject"
                        variant="outlined"
                        class="mb-4"
                        :rules="[v => !!v || 'Subject is required']"
                    ></v-text-field>
                    <v-select
                        v-model="newTicket.priority"
                        :items="['low', 'normal', 'high', 'urgent']"
                        label="Priority"
                        variant="outlined"
                        class="mb-4"
                    ></v-select>
                    <v-textarea
                        v-model="newTicket.message"
                        label="Message"
                        variant="outlined"
                        rows="4"
                        :rules="[v => !!v || 'Message is required']"
                    ></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="createDialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="createTicket" :loading="creating">
                        Create Ticket
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'
import PageHeader from '../../components/PageHeader.vue'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const tickets = ref([])
const totalTickets = ref(0)
const loading = ref(false)
const creating = ref(false)
const page = ref(1)
const itemsPerPage = ref(15)

// Filters
const filters = ref({
    search: '',
    status: null,
    priority: null,
})

const statusOptions = [
    { title: 'All', value: null },
    { title: 'Open', value: 'open' },
    { title: 'In Progress', value: 'in_progress' },
    { title: 'Waiting', value: 'waiting' },
    { title: 'Closed', value: 'closed' },
]

const priorityOptions = [
    { title: 'All', value: null },
    { title: 'Low', value: 'low' },
    { title: 'Normal', value: 'normal' },
    { title: 'High', value: 'high' },
    { title: 'Urgent', value: 'urgent' },
]

// Dialogs
const createDialog = ref(false)
const newTicket = ref({
    subject: '',
    message: '',
    priority: 'normal',
})

// Table headers
const headers = [
    { title: '#', key: 'id', width: '100px' },
    { title: 'Subject', key: 'subject', sortable: false },
    { title: 'Status', key: 'status' },
    { title: 'Priority', key: 'priority' },
    { title: 'Related To', key: 'related', sortable: false },
    { title: 'Messages', key: 'messages_count', align: 'center' },
    { title: 'Date', key: 'created_at' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

// Methods
const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const getStatusColor = (status) => {
    const colors = {
        'open': 'info',
        'in_progress': 'warning',
        'waiting': 'orange',
        'closed': 'success',
    }
    return colors[status?.toLowerCase()] || 'grey'
}

const getPriorityColor = (priority) => {
    const colors = {
        'low': 'success',
        'normal': 'info',
        'high': 'warning',
        'urgent': 'error',
    }
    return colors[priority?.toLowerCase()] || 'grey'
}

const getRelatedIcon = (type) => {
    if (type?.includes('Order')) return 'mdi-shopping'
    if (type?.includes('Transaction')) return 'mdi-cash'
    return 'mdi-tag'
}

const getRelatedLabel = (type) => {
    if (type?.includes('Order')) return 'Order'
    if (type?.includes('Transaction')) return 'Transaction'
    return 'Item'
}

const fetchTickets = async () => {
    loading.value = true
    try {
        const params = {
            page: page.value,
            per_page: itemsPerPage.value,
            search: filters.value.search,
            status: filters.value.status,
            priority: filters.value.priority,
        }

        // Try admin endpoint first, fallback to regular support
        let response
        try {
            response = await axios.get('/api/admin/tickets', { params })
        } catch (e) {
            // If admin endpoint fails (403), try regular support endpoint
            response = await axios.get('/api/support', { params })
        }

        tickets.value = response.data.tickets || response.data.data || []
        totalTickets.value = response.data.pagination?.total || response.data.total || 0
    } catch (error) {
        console.error('Error fetching tickets:', error)
        showSnackbar?.('Failed to load tickets', 'error')
    } finally {
        loading.value = false
    }
}

const onTableUpdate = ({ page: newPage, itemsPerPage: newItemsPerPage }) => {
    page.value = newPage
    itemsPerPage.value = newItemsPerPage
    fetchTickets()
}

const openCreateDialog = () => {
    newTicket.value = { subject: '', message: '', priority: 'normal' }
    createDialog.value = true
}

const createTicket = async () => {
    if (!newTicket.value.subject || !newTicket.value.message) {
        showSnackbar?.('Please fill in all required fields', 'error')
        return
    }

    creating.value = true
    try {
        await axios.post('/api/support', newTicket.value)
        showSnackbar?.('Ticket created successfully', 'success')
        createDialog.value = false
        fetchTickets()
    } catch (error) {
        console.error('Error creating ticket:', error)
        showSnackbar?.('Failed to create ticket', 'error')
    } finally {
        creating.value = false
    }
}

const closeTicket = async (ticket) => {
    if (!confirm('Are you sure you want to close this ticket?')) return

    try {
        const endpoint = authStore.isAdmin ? `/api/admin/tickets/${ticket.id}/close` : `/api/support/${ticket.id}/close`
        await axios.post(endpoint)
        showSnackbar?.('Ticket closed', 'success')
        fetchTickets()
    } catch (error) {
        console.error('Error closing ticket:', error)
        showSnackbar?.('Failed to close ticket', 'error')
    }
}

// Lifecycle
onMounted(() => {
    fetchTickets()
})
</script>
