<template>
    <div v-if="ticket">
        <!-- Ticket Header -->
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center flex-wrap ga-2">
                <v-btn icon variant="text" to="/admin/support">
                    <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
                <v-icon class="mr-2">mdi-ticket</v-icon>
                <span>Ticket #{{ ticket.id }}</span>
                <v-chip :color="getStatusColor(ticket.status?.name || ticket.status)" class="ml-2" size="small">
                    {{ ticket.status?.name || ticket.status }}
                </v-chip>
                <v-chip :color="getPriorityColor(ticket.priority)" class="ml-1" size="small" variant="outlined">
                    {{ ticket.priority || 'normal' }}
                </v-chip>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="(ticket.status?.name || ticket.status) !== 'closed'"
                    color="success"
                    variant="tonal"
                    @click="closeTicket"
                    prepend-icon="mdi-check"
                >
                    Close Ticket
                </v-btn>
            </v-card-title>
        </v-card>

        <v-row>
            <!-- Ticket Info -->
            <v-col cols="12" md="4">
                <v-card class="mb-4">
                    <v-card-title class="text-subtitle-1">
                        <v-icon class="mr-2" size="small">mdi-information</v-icon>
                        Ticket Information
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon size="small">mdi-text-subject</v-icon>
                                </template>
                                <v-list-item-title class="text-caption">Subject</v-list-item-title>
                                <v-list-item-subtitle class="font-weight-medium">{{ ticket.subject }}</v-list-item-subtitle>
                            </v-list-item>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon size="small">mdi-account</v-icon>
                                </template>
                                <v-list-item-title class="text-caption">User</v-list-item-title>
                                <v-list-item-subtitle class="font-weight-medium">
                                    {{ ticket.user?.name || 'Unknown' }}
                                </v-list-item-subtitle>
                            </v-list-item>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon size="small">mdi-email</v-icon>
                                </template>
                                <v-list-item-title class="text-caption">Email</v-list-item-title>
                                <v-list-item-subtitle>{{ ticket.user?.email || '-' }}</v-list-item-subtitle>
                            </v-list-item>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon size="small">mdi-calendar</v-icon>
                                </template>
                                <v-list-item-title class="text-caption">Created</v-list-item-title>
                                <v-list-item-subtitle>{{ formatDateTime(ticket.created_at) }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-list>

                        <!-- Related Item -->
                        <v-divider class="my-3" v-if="ticket.ticketable_type"></v-divider>
                        <div v-if="ticket.ticketable_type">
                            <div class="text-caption text-grey mb-2">Related To</div>
                            <v-chip color="info" variant="tonal">
                                <v-icon start size="small">{{ getRelatedIcon(ticket.ticketable_type) }}</v-icon>
                                {{ getRelatedLabel(ticket.ticketable_type) }} #{{ ticket.ticketable_id }}
                            </v-chip>
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Status Change -->
                <v-card v-if="authStore.isAdmin">
                    <v-card-title class="text-subtitle-1">
                        <v-icon class="mr-2" size="small">mdi-cog</v-icon>
                        Update Status
                    </v-card-title>
                    <v-card-text>
                        <v-select
                            v-model="selectedStatus"
                            :items="['open', 'in_progress', 'waiting', 'answered', 'closed']"
                            variant="outlined"
                            density="compact"
                            @update:model-value="updateStatus"
                        ></v-select>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Messages -->
            <v-col cols="12" md="8">
                <v-card>
                    <v-card-title class="d-flex align-center">
                        <v-icon class="mr-2" size="small">mdi-message</v-icon>
                        Conversation
                        <v-spacer></v-spacer>
                        <v-btn icon size="small" @click="fetchMessages" :loading="loadingMessages">
                            <v-icon>mdi-refresh</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>

                    <!-- Messages List -->
                    <div class="messages-container pa-4" ref="messagesContainer">
                        <template v-if="messages.length > 0">
                            <div
                                v-for="message in messages"
                                :key="message.id"
                                class="message-wrapper mb-4"
                                :class="{ 'message-own': message.user_id === authStore.user?.id }"
                            >
                                <div class="d-flex" :class="message.user_id === authStore.user?.id ? 'justify-end' : 'justify-start'">
                                    <v-avatar
                                        v-if="message.user_id !== authStore.user?.id"
                                        size="36"
                                        class="mr-2"
                                        color="primary"
                                    >
                                        <span class="text-white">{{ message.user?.name?.charAt(0) || 'U' }}</span>
                                    </v-avatar>
                                    <v-card
                                        :color="message.user_id === authStore.user?.id ? 'primary' : 'grey-lighten-3'"
                                        :class="message.user_id === authStore.user?.id ? 'text-white' : ''"
                                        max-width="70%"
                                        class="pa-3"
                                    >
                                        <div class="d-flex align-center mb-1">
                                            <span class="font-weight-bold text-caption">
                                                {{ message.user?.name || 'Unknown' }}
                                            </span>
                                            <v-chip
                                                v-if="message.user?.roles?.includes('admin')"
                                                size="x-small"
                                                color="warning"
                                                class="ml-1"
                                            >
                                                Admin
                                            </v-chip>
                                        </div>
                                        <div class="message-content">{{ message.message }}</div>
                                        <div class="text-caption mt-1 opacity-70">
                                            {{ formatTime(message.created_at) }}
                                        </div>
                                    </v-card>
                                    <v-avatar
                                        v-if="message.user_id === authStore.user?.id"
                                        size="36"
                                        class="ml-2"
                                        color="primary"
                                    >
                                        <span class="text-white">{{ authStore.user?.name?.charAt(0) || 'U' }}</span>
                                    </v-avatar>
                                </div>
                            </div>
                        </template>
                        <div v-else class="text-center py-8 text-grey">
                            <v-icon size="48" class="mb-2">mdi-message-off</v-icon>
                            <div>No messages yet</div>
                        </div>
                    </div>

                    <v-divider></v-divider>

                    <!-- Reply Form -->
                    <v-card-text v-if="(ticket.status?.name || ticket.status) !== 'closed'">
                        <v-textarea
                            v-model="newMessage"
                            label="Type your message..."
                            variant="outlined"
                            rows="3"
                            hide-details
                            @keydown.ctrl.enter="sendMessage"
                        ></v-textarea>
                        <div class="d-flex justify-space-between align-center mt-3">
                            <span class="text-caption text-grey">Press Ctrl+Enter to send</span>
                            <v-btn
                                color="primary"
                                @click="sendMessage"
                                :loading="sending"
                                :disabled="!newMessage.trim()"
                                prepend-icon="mdi-send"
                            >
                                Send
                            </v-btn>
                        </div>
                    </v-card-text>
                    <v-alert v-else type="info" variant="tonal" class="ma-4">
                        This ticket is closed. No new messages can be sent.
                    </v-alert>
                </v-card>
            </v-col>
        </v-row>
    </div>

    <!-- Loading -->
    <div v-else-if="loading" class="text-center py-8">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
        <div class="mt-2">Loading ticket...</div>
    </div>

    <!-- Not Found -->
    <v-card v-else class="text-center py-8">
        <v-icon size="64" color="grey">mdi-ticket-off</v-icon>
        <div class="text-h6 mt-4">Ticket not found</div>
        <v-btn color="primary" to="/admin/support" class="mt-4">
            Back to Support
        </v-btn>
    </v-card>
</template>

<script setup>
import { ref, onMounted, nextTick, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const route = useRoute()
const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const ticket = ref(null)
const messages = ref([])
const loading = ref(true)
const loadingMessages = ref(false)
const sending = ref(false)
const newMessage = ref('')
const messagesContainer = ref(null)
const selectedStatus = ref('open')

// Methods
const formatDateTime = (date) => {
    return new Date(date).toLocaleString()
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

const fetchTicket = async () => {
    loading.value = true
    try {
        // Use admin endpoint for admin users
        const endpoint = authStore.isAdmin
            ? `/api/admin/tickets/${route.params.id}`
            : `/api/support/${route.params.id}`
        const response = await axios.get(endpoint)
        ticket.value = response.data.ticket || response.data
        messages.value = response.data.messages || ticket.value.messages || []
        selectedStatus.value = ticket.value.status?.name || ticket.value.status || 'open'
        scrollToBottom()
    } catch (error) {
        console.error('Error fetching ticket:', error)
        ticket.value = null
    } finally {
        loading.value = false
    }
}

const fetchMessages = async () => {
    loadingMessages.value = true
    try {
        // Refetch full ticket to get updated messages
        const endpoint = authStore.isAdmin
            ? `/api/admin/tickets/${route.params.id}`
            : `/api/support/${route.params.id}`
        const response = await axios.get(endpoint)
        const ticketData = response.data.ticket || response.data
        messages.value = ticketData.messages || []
        scrollToBottom()
    } catch (error) {
        console.error('Error fetching messages:', error)
    } finally {
        loadingMessages.value = false
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim() || sending.value) return

    sending.value = true
    try {
        // Use admin endpoint for admin users
        const endpoint = authStore.isAdmin
            ? `/api/admin/tickets/${route.params.id}/reply`
            : `/api/support/${route.params.id}/messages`
        const response = await axios.post(endpoint, {
            message: newMessage.value
        })

        // Add the new message
        if (response.data.data) {
            messages.value.push(response.data.data)
        } else if (response.data.message && typeof response.data.message === 'object') {
            messages.value.push(response.data.message)
        } else {
            // Refresh messages if response doesn't include the new message
            await fetchMessages()
        }

        newMessage.value = ''
        scrollToBottom()
    } catch (error) {
        console.error('Error sending message:', error)
        showSnackbar?.('Failed to send message', 'error')
    } finally {
        sending.value = false
    }
}

const updateStatus = async (newStatus) => {
    try {
        // Use admin endpoint for admin users
        const endpoint = authStore.isAdmin
            ? `/api/admin/tickets/${route.params.id}`
            : `/api/support/${route.params.id}`
        await axios.put(endpoint, { status: newStatus })
        ticket.value.status = { name: newStatus }
        showSnackbar?.('Status updated', 'success')
    } catch (error) {
        console.error('Error updating status:', error)
        showSnackbar?.('Failed to update status', 'error')
    }
}

const closeTicket = async () => {
    if (!confirm('Are you sure you want to close this ticket?')) return

    try {
        // Use admin endpoint for admin users
        const endpoint = authStore.isAdmin
            ? `/api/admin/tickets/${route.params.id}/close`
            : `/api/support/${route.params.id}/close`
        await axios.post(endpoint)
        ticket.value.status = { name: 'closed' }
        selectedStatus.value = 'closed'
        showSnackbar?.('Ticket closed', 'success')
    } catch (error) {
        console.error('Error closing ticket:', error)
        showSnackbar?.('Failed to close ticket', 'error')
    }
}

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

// Lifecycle
onMounted(() => {
    fetchTicket()

    // Poll for new messages every 15 seconds
    const interval = setInterval(fetchMessages, 15000)

    // Cleanup on unmount
    return () => clearInterval(interval)
})
</script>

<style scoped>
.messages-container {
    max-height: 500px;
    overflow-y: auto;
}

.message-content {
    white-space: pre-wrap;
    word-break: break-word;
}
</style>
