<template>
    <div>
        <!-- Welcome Banner -->
        <v-card class="mb-6 overflow-hidden" color="primary" variant="flat">
            <div class="d-flex align-center pa-6">
                <div>
                    <h1 class="text-h4 font-weight-bold text-white mb-2">
                        {{ $t('dashboard.welcome') }}, {{ authStore.userName }}!
                    </h1>
                    <p class="text-white-darken-1 mb-0">
                        {{ $t('dashboard.welcomeMessage') }}
                    </p>
                </div>
                <v-spacer></v-spacer>
                <v-avatar size="80" class="d-none d-md-flex">
                    <v-img v-if="authStore.userAvatar" :src="authStore.userAvatar"></v-img>
                    <v-icon v-else size="80" color="white">mdi-account-circle</v-icon>
                </v-avatar>
            </div>
            <v-card class="mx-4 mb-4 pa-4" variant="tonal" color="white">
                <div class="d-flex flex-wrap gap-6 justify-center justify-md-start">
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold text-primary">${{ formatBalance(authStore.userBalance) }}</div>
                        <div class="text-caption text-grey">{{ $t('dashboard.currentBalance') }}</div>
                    </div>
                    <v-divider vertical class="d-none d-md-flex"></v-divider>
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold text-success">{{ stats.ordersCount || 0 }}</div>
                        <div class="text-caption text-grey">{{ $t('dashboard.totalOrders') }}</div>
                    </div>
                    <v-divider vertical class="d-none d-md-flex"></v-divider>
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold text-info">{{ stats.referralsCount || 0 }}</div>
                        <div class="text-caption text-grey">{{ $t('dashboard.referrals') }}</div>
                    </div>
                </div>
            </v-card>
        </v-card>

        <!-- Admin Widgets Section -->
        <template v-if="authStore.isAdmin">
            <!-- Cost/Profit Metrics -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <h2 class="text-h6 font-weight-bold mb-4">
                        <v-icon class="mr-2">mdi-chart-line</v-icon>
                        {{ $t('dashboard.financialOverview') }}
                    </h2>
                </v-col>
                <v-col v-for="period in timePeriods" :key="period.key" cols="12" sm="6" md="3">
                    <v-card :color="period.color" variant="flat" class="h-100">
                        <v-card-text class="text-white">
                            <div class="d-flex justify-space-between align-center mb-2">
                                <span class="text-h6">{{ $t(period.labelKey) }}</span>
                                <v-icon size="32" class="opacity-60">{{ period.icon }}</v-icon>
                            </div>
                            <div class="text-h4 font-weight-bold mb-1">
                                ${{ formatNumber(adminStats.totals?.[period.key]?.cost || 0) }}
                            </div>
                            <div class="text-caption opacity-80">{{ $t('dashboard.totalCost') }}</div>
                            <v-divider class="my-2 opacity-30"></v-divider>
                            <div class="text-h5 font-weight-bold text-white">
                                ${{ formatNumber(adminStats.totals?.[period.key]?.profit || 0) }}
                            </div>
                            <div class="text-caption opacity-80">{{ $t('dashboard.totalProfit') }}</div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Admin Metrics -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <h2 class="text-h6 font-weight-bold mb-4">
                        <v-icon class="mr-2">mdi-account-group</v-icon>
                        {{ $t('dashboard.userMetrics') }}
                    </h2>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.totalUsers')"
                        :value="adminStats.userCount || 0"
                        icon="mdi-account-multiple"
                        color="info"
                        to="/admin/users"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.totalUserBalance')"
                        :value="'$' + formatNumber(adminStats.totalUserBalance || 0)"
                        icon="mdi-wallet"
                        color="purple"
                        to="/admin/users"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.verifiedUsers')"
                        :value="adminStats.verifiedUsersCount || 0"
                        icon="mdi-account-check"
                        color="success"
                        to="/admin/users"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.nonVerifiedUsers')"
                        :value="adminStats.nonVerifiedUsersCount || 0"
                        icon="mdi-account-cancel"
                        color="error"
                        to="/admin/users"
                    />
                </v-col>
            </v-row>

            <!-- Transaction Metrics -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <h2 class="text-h6 font-weight-bold mb-4">
                        <v-icon class="mr-2">mdi-cash-multiple</v-icon>
                        {{ $t('dashboard.transactionMetrics') }}
                    </h2>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <StatCard
                        :title="$t('dashboard.completedTransactions')"
                        :value="adminStats.completedTransactionsCount || 0"
                        icon="mdi-check-circle"
                        color="success"
                        to="/admin/transactions"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <StatCard
                        :title="$t('dashboard.transactions24h')"
                        :value="adminStats.newCreditTransactionsCount || 0"
                        icon="mdi-clock-check"
                        color="info"
                        to="/admin/transactions"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <StatCard
                        :title="$t('dashboard.canceledTransactions')"
                        :value="adminStats.canceledTransactionsCount || 0"
                        icon="mdi-close-circle"
                        color="error"
                        to="/admin/transactions"
                    />
                </v-col>
            </v-row>

            <!-- Services and Orders -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <h2 class="text-h6 font-weight-bold mb-4">
                        <v-icon class="mr-2">mdi-store</v-icon>
                        {{ $t('dashboard.servicesOrders') }}
                    </h2>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <StatCard
                        :title="$t('dashboard.totalServices')"
                        :value="adminStats.serviceCount || 0"
                        icon="mdi-cog"
                        color="teal"
                        to="/admin/services"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <StatCard
                        :title="$t('dashboard.totalOrders')"
                        :value="adminStats.orderCount || 0"
                        icon="mdi-shopping"
                        color="warning"
                        to="/admin/orders"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <StatCard
                        :title="$t('dashboard.startingPrice')"
                        :value="'$' + formatNumber(adminStats.startingPrice || 0)"
                        icon="mdi-tag"
                        color="pink"
                        to="/admin/services"
                    />
                </v-col>
            </v-row>
        </template>

        <!-- User Widgets Section (Non-Admin) -->
        <template v-else>
            <v-row>
                <v-col cols="12">
                    <h2 class="text-h6 font-weight-bold mb-4">
                        <v-icon class="mr-2">mdi-chart-box</v-icon>
                        {{ $t('dashboard.yourStatistics') }}
                    </h2>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.myReferrals')"
                        :value="stats.referralsCount || 0"
                        icon="mdi-account-group"
                        color="info"
                        to="/admin/referrals"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.myOrders')"
                        :value="stats.ordersCount || 0"
                        icon="mdi-shopping"
                        color="warning"
                        to="/admin/orders"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('dashboard.supportTickets')"
                        :value="stats.supportTicketsCount || 0"
                        icon="mdi-headset"
                        color="primary"
                        to="/admin/support"
                    />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <StatCard
                        :title="$t('nav.transactions')"
                        :value="stats.transactionsCount || 0"
                        icon="mdi-cash-multiple"
                        color="success"
                        to="/admin/transactions"
                    />
                </v-col>
            </v-row>

            <!-- Order Status Breakdown -->
            <v-row class="mt-4" v-if="ordersByStatus && Object.keys(ordersByStatus).length > 0">
                <v-col cols="12">
                    <h2 class="text-h6 font-weight-bold mb-4">
                        <v-icon class="mr-2">mdi-format-list-checks</v-icon>
                        {{ $t('dashboard.ordersByStatus') }}
                    </h2>
                </v-col>
                <v-col
                    v-for="(count, status) in ordersByStatus"
                    :key="status"
                    cols="6"
                    sm="4"
                    md="2"
                >
                    <v-card variant="outlined" class="text-center pa-4">
                        <v-icon :color="getStatusColor(status)" size="32" class="mb-2">
                            {{ getStatusIcon(status) }}
                        </v-icon>
                        <div class="text-h5 font-weight-bold">{{ count }}</div>
                        <div class="text-caption text-capitalize">{{ getStatusText(status) }}</div>
                    </v-card>
                </v-col>
            </v-row>
        </template>

        <!-- Quick Actions -->
        <v-row class="mt-4">
            <v-col cols="12">
                <h2 class="text-h6 font-weight-bold mb-4">
                    <v-icon class="mr-2">mdi-lightning-bolt</v-icon>
                    {{ $t('dashboard.quickActions') }}
                </h2>
            </v-col>
            <v-col cols="6" sm="4" md="2">
                <v-card to="/admin/orders/create" class="text-center pa-4 h-100" variant="tonal" color="primary" hover>
                    <v-icon size="40" class="mb-2">mdi-cart-plus</v-icon>
                    <div class="text-body-2 font-weight-medium">{{ $t('dashboard.newOrder') }}</div>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4" md="2">
                <v-card to="/admin/transactions/create" class="text-center pa-4 h-100" variant="tonal" color="success" hover>
                    <v-icon size="40" class="mb-2">mdi-credit-card-plus</v-icon>
                    <div class="text-body-2 font-weight-medium">{{ $t('dashboard.addBalance') }}</div>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4" md="2">
                <v-card to="/admin/support" class="text-center pa-4 h-100" variant="tonal" color="info" hover>
                    <v-icon size="40" class="mb-2">mdi-headset</v-icon>
                    <div class="text-body-2 font-weight-medium">{{ $t('dashboard.support') }}</div>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4" md="2">
                <v-card to="/admin/services" class="text-center pa-4 h-100" variant="tonal" color="warning" hover>
                    <v-icon size="40" class="mb-2">mdi-view-list</v-icon>
                    <div class="text-body-2 font-weight-medium">{{ $t('dashboard.services') }}</div>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4" md="2">
                <v-card to="/admin/referrals" class="text-center pa-4 h-100" variant="tonal" color="purple" hover>
                    <v-icon size="40" class="mb-2">mdi-account-group</v-icon>
                    <div class="text-body-2 font-weight-medium">{{ $t('dashboard.referrals') }}</div>
                </v-card>
            </v-col>
            <v-col cols="6" sm="4" md="2">
                <v-card to="/admin/profile" class="text-center pa-4 h-100" variant="tonal" color="grey" hover>
                    <v-icon size="40" class="mb-2">mdi-cog</v-icon>
                    <div class="text-body-2 font-weight-medium">{{ $t('dashboard.settings') }}</div>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'
import StatCard from '../../components/StatCard.vue'

const { t } = useI18n()
const authStore = useAuthStore()

const stats = ref({
    ordersCount: 0,
    referralsCount: 0,
    supportTicketsCount: 0,
    transactionsCount: 0,
})

const adminStats = ref({
    totals: {},
    userCount: 0,
    totalUserBalance: 0,
    verifiedUsersCount: 0,
    nonVerifiedUsersCount: 0,
    completedTransactionsCount: 0,
    newCreditTransactionsCount: 0,
    canceledTransactionsCount: 0,
    serviceCount: 0,
    orderCount: 0,
    startingPrice: 0,
})

const ordersByStatus = ref({})

const timePeriods = [
    { key: '24h', labelKey: 'dashboard.hours24', color: 'info', icon: 'mdi-clock' },
    { key: '7d', labelKey: 'dashboard.days7', color: 'success', icon: 'mdi-calendar-week' },
    { key: '30d', labelKey: 'dashboard.days30', color: 'warning', icon: 'mdi-calendar-month' },
    { key: 'lifetime', labelKey: 'dashboard.lifetime', color: 'error', icon: 'mdi-infinity' },
]

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0)
}

const formatBalance = (balance) => {
    const num = parseFloat(balance) || 0
    return num.toFixed(2)
}

const getStatusColor = (status) => {
    const colors = {
        'pending': 'warning',
        'waiting': 'warning',
        'processing': 'info',
        'in_progress': 'info',
        'completed': 'success',
        'partial': 'orange',
        'canceled': 'error',
        'refunded': 'purple',
    }
    return colors[status.toLowerCase()] || 'grey'
}

const getStatusIcon = (status) => {
    const icons = {
        'pending': 'mdi-clock-outline',
        'waiting': 'mdi-clock-outline',
        'processing': 'mdi-cog-sync',
        'in_progress': 'mdi-progress-clock',
        'completed': 'mdi-check-circle',
        'partial': 'mdi-circle-half-full',
        'canceled': 'mdi-close-circle',
        'refunded': 'mdi-cash-refund',
    }
    return icons[status.toLowerCase()] || 'mdi-help-circle'
}

const getStatusText = (status) => {
    const key = `orders.${status.toLowerCase()}`
    const translated = t(key)
    // If translation not found, return original status
    return translated === key ? status : translated
}

const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/dashboard')
        const data = response.data

        stats.value = {
            ordersCount: data.ordersCount || 0,
            referralsCount: data.referralsCount || 0,
            supportTicketsCount: data.supportTicketsCount || 0,
            transactionsCount: data.transactionsCount || 0,
        }

        ordersByStatus.value = data.ordersByStatus || {}

        if (data.admin) {
            adminStats.value = {
                totals: data.admin.totals || {},
                userCount: data.admin.userCount || 0,
                totalUserBalance: data.admin.totalUserBalance || 0,
                verifiedUsersCount: data.admin.verifiedUsersCount || 0,
                nonVerifiedUsersCount: data.admin.nonVerifiedUsersCount || 0,
                completedTransactionsCount: data.admin.completedTransactionsCount || 0,
                newCreditTransactionsCount: data.admin.newCreditTransactionsCount || 0,
                canceledTransactionsCount: data.admin.canceledTransactionsCount || 0,
                serviceCount: data.admin.serviceCount || 0,
                orderCount: data.admin.orderCount || 0,
                startingPrice: data.admin.startingPrice || 0,
            }
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error)
    }
}

onMounted(() => {
    fetchDashboardData()
})
</script>
