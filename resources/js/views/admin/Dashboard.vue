<template>
    <div>
        <!-- Welcome -->
        <v-card class="mb-6" variant="flat" :style="{ background: store.gradientStyle }">
            <v-card-text class="pa-5 d-flex align-center">
                <div class="flex-grow-1">
                    <div class="text-h5 font-weight-bold text-white mb-1">
                        {{ greeting }}, {{ authStore.userName }}
                    </div>
                    <div class="text-body-2 text-white" style="opacity: 0.8;">
                        {{ $t('dashboard.welcomeMessage') }}
                    </div>
                </div>
                <v-avatar size="56" class="d-none d-sm-flex" color="rgba(255,255,255,0.2)">
                    <v-img v-if="authStore.userAvatar" :src="authStore.userAvatar"></v-img>
                    <v-icon v-else size="32" color="white">mdi-account</v-icon>
                </v-avatar>
            </v-card-text>
        </v-card>

        <!-- Balance Row -->
        <v-row class="mb-2">
            <v-col cols="12" sm="4">
                <v-card variant="outlined" class="h-100">
                    <v-card-text class="pa-4 text-center">
                        <div class="text-caption text-medium-emphasis mb-1">{{ $t('dashboard.currentBalance') }}</div>
                        <div class="text-h4 font-weight-bold text-primary stat-value">${{ formatBalance(authStore.userBalance) }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4">
                <v-card variant="outlined" class="h-100">
                    <v-card-text class="pa-4 text-center">
                        <div class="text-caption text-medium-emphasis mb-1">{{ $t('dashboard.totalOrders') }}</div>
                        <div class="text-h4 font-weight-bold text-success stat-value">{{ stats.ordersCount || 0 }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4">
                <v-card variant="outlined" class="h-100">
                    <v-card-text class="pa-4 text-center">
                        <div class="text-caption text-medium-emphasis mb-1">{{ $t('dashboard.referrals') }}</div>
                        <div class="text-h4 font-weight-bold text-info stat-value">{{ stats.referralsCount || 0 }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Admin: Financial Overview -->
        <template v-if="authStore.isAdmin">
            <div class="text-subtitle-2 font-weight-bold text-uppercase mt-6 mb-3" style="letter-spacing: 0.05em;">
                <v-icon size="16" class="mr-1">mdi-chart-line</v-icon>
                {{ $t('dashboard.financialOverview') }}
            </div>
            <v-row class="mb-2">
                <v-col v-for="period in timePeriods" :key="period.key" cols="6" md="3">
                    <v-card variant="outlined" class="h-100">
                        <v-card-text class="pa-4">
                            <div class="d-flex align-center justify-space-between mb-2">
                                <v-chip :color="period.color" size="x-small" variant="tonal">{{ $t(period.labelKey) }}</v-chip>
                                <v-icon size="16" class="text-medium-emphasis">{{ period.icon }}</v-icon>
                            </div>
                            <div class="text-h6 font-weight-bold stat-value">${{ formatNumber(adminStats.totals?.[period.key]?.cost || 0) }}</div>
                            <div class="text-caption text-medium-emphasis">{{ $t('dashboard.totalCost') }}</div>
                            <v-divider class="my-2"></v-divider>
                            <div class="text-subtitle-1 font-weight-bold text-success stat-value">${{ formatNumber(adminStats.totals?.[period.key]?.profit || 0) }}</div>
                            <div class="text-caption text-medium-emphasis">{{ $t('dashboard.totalProfit') }}</div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Admin Metrics -->
            <div class="text-subtitle-2 font-weight-bold text-uppercase mt-6 mb-3" style="letter-spacing: 0.05em;">
                <v-icon size="16" class="mr-1">mdi-account-group</v-icon>
                {{ $t('dashboard.userMetrics') }}
            </div>
            <v-row class="mb-2">
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.totalUsers')" :value="adminStats.userCount || 0" icon="mdi-account-multiple" color="info" to="/admin/users" />
                </v-col>
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.totalUserBalance')" :value="'$' + formatNumber(adminStats.totalUserBalance || 0)" icon="mdi-wallet" color="primary" to="/admin/users" />
                </v-col>
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.verifiedUsers')" :value="adminStats.verifiedUsersCount || 0" icon="mdi-account-check" color="success" to="/admin/users" />
                </v-col>
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.nonVerifiedUsers')" :value="adminStats.nonVerifiedUsersCount || 0" icon="mdi-account-cancel" color="error" to="/admin/users" />
                </v-col>
            </v-row>

            <!-- Transaction + Service Metrics -->
            <v-row class="mb-2">
                <v-col cols="6" md="4">
                    <StatCard :title="$t('dashboard.completedTransactions')" :value="adminStats.completedTransactionsCount || 0" icon="mdi-check-circle" color="success" to="/admin/transactions" />
                </v-col>
                <v-col cols="6" md="4">
                    <StatCard :title="$t('dashboard.totalServices')" :value="adminStats.serviceCount || 0" icon="mdi-cog" color="warning" to="/admin/services" />
                </v-col>
                <v-col cols="12" md="4">
                    <StatCard :title="$t('dashboard.totalOrders')" :value="adminStats.orderCount || 0" icon="mdi-shopping" color="info" to="/admin/orders" />
                </v-col>
            </v-row>
        </template>

        <!-- User Stats (Non-Admin) -->
        <template v-else>
            <div class="text-subtitle-2 font-weight-bold text-uppercase mt-6 mb-3" style="letter-spacing: 0.05em;">
                <v-icon size="16" class="mr-1">mdi-chart-box</v-icon>
                {{ $t('dashboard.yourStatistics') }}
            </div>
            <v-row class="mb-2">
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.myReferrals')" :value="stats.referralsCount || 0" icon="mdi-account-group" color="info" to="/admin/referrals" />
                </v-col>
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.myOrders')" :value="stats.ordersCount || 0" icon="mdi-shopping" color="warning" to="/admin/orders" />
                </v-col>
                <v-col cols="6" md="3">
                    <StatCard :title="$t('dashboard.supportTickets')" :value="stats.supportTicketsCount || 0" icon="mdi-headset" color="primary" to="/admin/support" />
                </v-col>
                <v-col cols="6" md="3">
                    <StatCard :title="$t('nav.transactions')" :value="stats.transactionsCount || 0" icon="mdi-cash-multiple" color="success" to="/admin/transactions" />
                </v-col>
            </v-row>

            <!-- Order Status -->
            <v-row v-if="ordersByStatus && Object.keys(ordersByStatus).length > 0" class="mb-2">
                <v-col cols="12">
                    <div class="text-subtitle-2 font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.05em;">
                        <v-icon size="16" class="mr-1">mdi-format-list-checks</v-icon>
                        {{ $t('dashboard.ordersByStatus') }}
                    </div>
                </v-col>
                <v-col v-for="(count, status) in ordersByStatus" :key="status" cols="4" sm="3" md="2">
                    <v-card variant="outlined" class="text-center pa-3">
                        <v-icon :color="getStatusColor(status)" size="24" class="mb-1">{{ getStatusIcon(status) }}</v-icon>
                        <div class="text-h6 font-weight-bold stat-value">{{ count }}</div>
                        <div class="text-caption text-capitalize text-medium-emphasis">{{ getStatusText(status) }}</div>
                    </v-card>
                </v-col>
            </v-row>
        </template>

        <!-- Quick Actions -->
        <div class="text-subtitle-2 font-weight-bold text-uppercase mt-6 mb-3" style="letter-spacing: 0.05em;">
            <v-icon size="16" class="mr-1">mdi-lightning-bolt</v-icon>
            {{ $t('dashboard.quickActions') }}
        </div>
        <v-row>
            <v-col v-for="action in quickActions" :key="action.key" cols="4" sm="4" md="2">
                <v-card :to="action.to" class="text-center pa-3 h-100 hover-lift" variant="outlined" hover>
                    <v-icon :color="action.color" size="28" class="mb-1">{{ action.icon }}</v-icon>
                    <div class="text-caption font-weight-medium">{{ $t(action.titleKey) }}</div>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'
import { useAppStore } from '../../stores/app'
import axios from 'axios'
import StatCard from '../../components/StatCard.vue'

const { t } = useI18n()
const authStore = useAuthStore()
const store = useAppStore()

const stats = ref({ ordersCount: 0, referralsCount: 0, supportTicketsCount: 0, transactionsCount: 0 })
const adminStats = ref({ totals: {}, userCount: 0, totalUserBalance: 0, verifiedUsersCount: 0, nonVerifiedUsersCount: 0, completedTransactionsCount: 0, newCreditTransactionsCount: 0, canceledTransactionsCount: 0, serviceCount: 0, orderCount: 0, startingPrice: 0 })
const ordersByStatus = ref({})

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return t('dashboard.welcome')
    return t('dashboard.welcome')
})

const timePeriods = [
    { key: '24h', labelKey: 'dashboard.hours24', color: 'info', icon: 'mdi-clock' },
    { key: '7d', labelKey: 'dashboard.days7', color: 'success', icon: 'mdi-calendar-week' },
    { key: '30d', labelKey: 'dashboard.days30', color: 'warning', icon: 'mdi-calendar-month' },
    { key: 'lifetime', labelKey: 'dashboard.lifetime', color: 'error', icon: 'mdi-infinity' },
]

const quickActions = [
    { key: 'newOrder', titleKey: 'dashboard.newOrder', icon: 'mdi-cart-plus', color: 'primary', to: '/admin/orders/create' },
    { key: 'addBalance', titleKey: 'dashboard.addBalance', icon: 'mdi-credit-card-plus', color: 'success', to: '/admin/transactions/create' },
    { key: 'support', titleKey: 'dashboard.support', icon: 'mdi-headset', color: 'info', to: '/admin/support' },
    { key: 'services', titleKey: 'dashboard.services', icon: 'mdi-view-list', color: 'warning', to: '/admin/services' },
    { key: 'referrals', titleKey: 'dashboard.referrals', icon: 'mdi-account-group', color: 'secondary', to: '/admin/referrals' },
    { key: 'settings', titleKey: 'dashboard.settings', icon: 'mdi-cog', color: 'secondary', to: '/admin/profile' },
]

const formatNumber = (num) => new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num || 0)
const formatBalance = (balance) => (parseFloat(balance) || 0).toFixed(2)

const getStatusColor = (status) => ({ pending: 'warning', waiting: 'warning', processing: 'info', in_progress: 'info', completed: 'success', partial: 'orange', canceled: 'error', refunded: 'secondary' })[status.toLowerCase()] || 'grey'
const getStatusIcon = (status) => ({ pending: 'mdi-clock-outline', waiting: 'mdi-clock-outline', processing: 'mdi-cog-sync', in_progress: 'mdi-progress-clock', completed: 'mdi-check-circle', partial: 'mdi-circle-half-full', canceled: 'mdi-close-circle', refunded: 'mdi-cash-refund' })[status.toLowerCase()] || 'mdi-help-circle'
const getStatusText = (status) => { const key = `orders.${status.toLowerCase()}`; const tr = t(key); return tr === key ? status : tr }

const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/dashboard')
        const data = response.data
        stats.value = { ordersCount: data.ordersCount || 0, referralsCount: data.referralsCount || 0, supportTicketsCount: data.supportTicketsCount || 0, transactionsCount: data.transactionsCount || 0 }
        ordersByStatus.value = data.ordersByStatus || {}
        if (data.admin) {
            adminStats.value = { totals: data.admin.totals || {}, userCount: data.admin.userCount || 0, totalUserBalance: data.admin.totalUserBalance || 0, verifiedUsersCount: data.admin.verifiedUsersCount || 0, nonVerifiedUsersCount: data.admin.nonVerifiedUsersCount || 0, completedTransactionsCount: data.admin.completedTransactionsCount || 0, newCreditTransactionsCount: data.admin.newCreditTransactionsCount || 0, canceledTransactionsCount: data.admin.canceledTransactionsCount || 0, serviceCount: data.admin.serviceCount || 0, orderCount: data.admin.orderCount || 0, startingPrice: data.admin.startingPrice || 0 }
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error)
    }
}

onMounted(() => { fetchDashboardData() })
</script>
