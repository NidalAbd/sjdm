<template>
    <div>
        <PageHeader title="Notifications" subtitle="Your notifications" icon="mdi-bell">
            <template #actions>
                <v-chip v-if="unreadCount > 0" color="error" size="small" class="mr-2">
                    {{ unreadCount }} unread
                </v-chip>
                <v-btn
                    v-if="unreadCount > 0"
                    color="primary"
                    variant="tonal"
                    @click="markAllAsRead"
                    :loading="marking"
                    prepend-icon="mdi-check-all"
                >
                    Mark All as Read
                </v-btn>
            </template>
        </PageHeader>

        <!-- Notifications List -->
        <v-card>
            <v-list v-if="notifications.length > 0">
                <template v-for="(notification, index) in notifications" :key="notification.id">
                    <v-list-item
                        :to="getNotificationUrl(notification)"
                        @click="markAsRead(notification)"
                        :class="{ 'bg-blue-lighten-5': !notification.read_at }"
                        class="py-4"
                    >
                        <template v-slot:prepend>
                            <v-avatar :color="getNotificationColor(notification)" size="48">
                                <v-icon color="white">{{ getNotificationIcon(notification) }}</v-icon>
                            </v-avatar>
                        </template>

                        <v-list-item-title class="font-weight-bold">
                            {{ getNotificationTitle(notification) }}
                            <v-chip
                                v-if="!notification.read_at"
                                color="primary"
                                size="x-small"
                                class="ml-2"
                            >
                                New
                            </v-chip>
                        </v-list-item-title>

                        <v-list-item-subtitle class="mt-1">
                            {{ notification.data?.message_content || notification.data?.message || 'New notification' }}
                        </v-list-item-subtitle>

                        <template v-slot:append>
                            <div class="text-right">
                                <div class="text-caption text-grey">{{ formatTime(notification.created_at) }}</div>
                                <v-btn
                                    icon
                                    size="small"
                                    variant="text"
                                    color="error"
                                    @click.stop.prevent="deleteNotification(notification)"
                                >
                                    <v-icon size="small">mdi-delete</v-icon>
                                </v-btn>
                            </div>
                        </template>
                    </v-list-item>
                    <v-divider v-if="index < notifications.length - 1"></v-divider>
                </template>
            </v-list>

            <div v-else class="text-center py-12">
                <v-icon size="64" color="grey">mdi-bell-off</v-icon>
                <div class="text-h6 mt-4 text-grey">No notifications</div>
                <div class="text-body-2 text-grey">You're all caught up!</div>
            </div>

            <!-- Pagination -->
            <v-card-actions v-if="totalPages > 1" class="justify-center">
                <v-pagination
                    v-model="page"
                    :length="totalPages"
                    :total-visible="5"
                    @update:model-value="fetchNotifications"
                ></v-pagination>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import axios from 'axios'
import PageHeader from '../../components/PageHeader.vue'

const showSnackbar = inject('showSnackbar')

// Data
const notifications = ref([])
const total = ref(0)
const loading = ref(false)
const marking = ref(false)
const page = ref(1)
const perPage = 15

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.read_at).length
})

const totalPages = computed(() => {
    return Math.ceil(total.value / perPage)
})

// Methods
const formatTime = (timestamp) => {
    const date = new Date(timestamp)
    const now = new Date()
    const diffInSeconds = Math.floor((now - date) / 1000)

    if (diffInSeconds < 60) return 'Just now'
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`
    return date.toLocaleDateString()
}

const getNotificationUrl = (notification) => {
    const data = notification.data
    if (data?.support_ticket_id) return `/admin/support/${data.support_ticket_id}`
    if (data?.ticket_id) return `/admin/support/${data.ticket_id}`
    if (data?.transaction_id) return `/admin/transactions`
    if (data?.order_id) return `/admin/orders`
    return '/admin/notifications'
}

const getNotificationIcon = (notification) => {
    const data = notification.data
    if (data?.support_ticket_id || data?.ticket_id) return 'mdi-message-text'
    if (data?.transaction_id) return 'mdi-cash'
    if (data?.order_id) return 'mdi-shopping'
    return 'mdi-bell'
}

const getNotificationColor = (notification) => {
    const data = notification.data
    if (data?.support_ticket_id || data?.ticket_id) return 'info'
    if (data?.transaction_id) return 'success'
    if (data?.order_id) return 'warning'
    return 'primary'
}

const getNotificationTitle = (notification) => {
    const data = notification.data
    if (data?.support_ticket_id) return 'New Support Message'
    if (data?.ticket_id) return 'Support Ticket Update'
    if (data?.transaction_id) return 'Transaction Update'
    if (data?.order_id) return 'Order Update'
    return 'Notification'
}

const fetchNotifications = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/notifications', {
            params: { page: page.value, per_page: perPage }
        })
        // API returns { notifications: [...], pagination: {...} }
        notifications.value = response.data.notifications || response.data.data || []
        total.value = response.data.pagination?.total || response.data.total || 0
    } catch (error) {
        console.error('Error fetching notifications:', error)
    } finally {
        loading.value = false
    }
}

const markAsRead = async (notification) => {
    if (notification.read_at) return

    try {
        await axios.get(`/notifications/${notification.id}/markAsRead`)
        notification.read_at = new Date().toISOString()
    } catch (error) {
        console.error('Error marking notification as read:', error)
    }
}

const markAllAsRead = async () => {
    marking.value = true
    try {
        await axios.post('/notifications/markAllAsRead')
        notifications.value.forEach(n => n.read_at = new Date().toISOString())
        showSnackbar?.('All notifications marked as read', 'success')
    } catch (error) {
        console.error('Error marking all as read:', error)
        showSnackbar?.('Failed to mark notifications as read', 'error')
    } finally {
        marking.value = false
    }
}

const deleteNotification = async (notification) => {
    try {
        await axios.delete(`/notifications/${notification.id}`)
        notifications.value = notifications.value.filter(n => n.id !== notification.id)
        showSnackbar?.('Notification deleted', 'success')
    } catch (error) {
        console.error('Error deleting notification:', error)
        showSnackbar?.('Failed to delete notification', 'error')
    }
}

// Lifecycle
onMounted(() => {
    fetchNotifications()
})
</script>
