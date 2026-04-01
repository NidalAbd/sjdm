<template>
    <div>
        <!-- Welcome -->
        <div class="welcome-card mb-6" :style="{ background: store.gradientStyle }">
            <div class="welcome-content">
                <div>
                    <div class="welcome-greeting">{{ $t('dashboard.welcome') }}, {{ authStore.userName }}</div>
                    <div class="welcome-sub">{{ $t('dashboard.welcomeMessage') }}</div>
                </div>
                <v-avatar size="48" color="rgba(255,255,255,0.2)" class="d-none d-sm-flex">
                    <v-img v-if="authStore.userAvatar" :src="authStore.userAvatar"></v-img>
                    <v-icon v-else size="24" color="white">mdi-account</v-icon>
                </v-avatar>
            </div>
        </div>

        <!-- Balance Stats -->
        <StatsRow :stats="balanceStats" />

        <!-- Admin Section -->
        <template v-if="authStore.isAdmin">
            <!-- Financial Overview -->
            <PageHeader :title="$t('dashboard.financialOverview')" icon="mdi-chart-line" subtitle="" />
            <div class="finance-grid mb-6">
                <div v-for="period in timePeriods" :key="period.key" class="finance-card">
                    <div class="finance-header">
                        <span class="finance-badge" :style="{ background: `rgba(var(--v-theme-${period.color}), 0.1)`, color: `rgb(var(--v-theme-${period.color}))` }">
                            {{ $t(period.labelKey) }}
                        </span>
                    </div>
                    <div class="finance-cost">${{ formatNumber(adminStats.totals?.[period.key]?.cost || 0) }}</div>
                    <div class="finance-label">{{ $t('dashboard.totalCost') }}</div>
                    <div class="finance-divider"></div>
                    <div class="finance-profit">${{ formatNumber(adminStats.totals?.[period.key]?.profit || 0) }}</div>
                    <div class="finance-label">{{ $t('dashboard.totalProfit') }}</div>
                </div>
            </div>

            <!-- User & Transaction Metrics -->
            <PageHeader :title="$t('dashboard.userMetrics')" icon="mdi-account-group" />
            <StatsRow :stats="adminUserStats" />

            <StatsRow :stats="adminServiceStats" />
        </template>

        <!-- Regular User Stats -->
        <template v-else>
            <PageHeader :title="$t('dashboard.yourStatistics')" icon="mdi-chart-box" />
            <StatsRow :stats="userStats" />

            <!-- Order Status -->
            <div v-if="ordersByStatus && Object.keys(ordersByStatus).length > 0" class="mb-6">
                <PageHeader :title="$t('dashboard.ordersByStatus')" icon="mdi-format-list-checks" />
                <div class="status-grid">
                    <div v-for="(count, status) in ordersByStatus" :key="status" class="status-item">
                        <v-icon :color="getStatusColor(status)" size="22">{{ getStatusIcon(status) }}</v-icon>
                        <div class="status-count">{{ count }}</div>
                        <div class="status-label">{{ getStatusText(status) }}</div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Quick Actions -->
        <PageHeader :title="$t('dashboard.quickActions')" icon="mdi-lightning-bolt" />
        <div class="actions-grid">
            <router-link v-for="action in quickActions" :key="action.key" :to="action.to" class="action-item">
                <div class="action-icon" :style="{ background: `rgba(var(--v-theme-${action.color}), 0.1)` }">
                    <v-icon :color="action.color" size="22">{{ action.icon }}</v-icon>
                </div>
                <span class="action-text">{{ $t(action.titleKey) }}</span>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'
import { useAppStore } from '../../stores/app'
import PageHeader from '../../components/PageHeader.vue'
import StatsRow from '../../components/StatsRow.vue'
import axios from 'axios'

const { t } = useI18n()
const authStore = useAuthStore()
const store = useAppStore()

const stats = ref({ ordersCount: 0, referralsCount: 0, supportTicketsCount: 0, transactionsCount: 0 })
const adminStats = ref({ totals: {}, userCount: 0, totalUserBalance: 0, verifiedUsersCount: 0, nonVerifiedUsersCount: 0, completedTransactionsCount: 0, newCreditTransactionsCount: 0, canceledTransactionsCount: 0, serviceCount: 0, orderCount: 0, startingPrice: 0 })
const ordersByStatus = ref({})

const balanceStats = computed(() => [
    { value: '$' + formatBalance(authStore.userBalance), label: t('dashboard.currentBalance'), icon: 'mdi-wallet', color: 'primary' },
    { value: String(stats.value.ordersCount || 0), label: t('dashboard.totalOrders'), icon: 'mdi-shopping', color: 'success' },
    { value: String(stats.value.referralsCount || 0), label: t('dashboard.referrals'), icon: 'mdi-account-group', color: 'info' },
])

const adminUserStats = computed(() => [
    { value: String(adminStats.value.userCount || 0), label: t('dashboard.totalUsers'), icon: 'mdi-account-multiple', color: 'info', to: '/admin/users' },
    { value: '$' + formatNumber(adminStats.value.totalUserBalance || 0), label: t('dashboard.totalUserBalance'), icon: 'mdi-wallet', color: 'primary', to: '/admin/users' },
    { value: String(adminStats.value.verifiedUsersCount || 0), label: t('dashboard.verifiedUsers'), icon: 'mdi-account-check', color: 'success' },
    { value: String(adminStats.value.nonVerifiedUsersCount || 0), label: t('dashboard.nonVerifiedUsers'), icon: 'mdi-account-cancel', color: 'error' },
])

const adminServiceStats = computed(() => [
    { value: String(adminStats.value.completedTransactionsCount || 0), label: t('dashboard.completedTransactions'), icon: 'mdi-check-circle', color: 'success', to: '/admin/transactions' },
    { value: String(adminStats.value.serviceCount || 0), label: t('dashboard.totalServices'), icon: 'mdi-cog', color: 'warning', to: '/admin/services' },
    { value: String(adminStats.value.orderCount || 0), label: t('dashboard.totalOrders'), icon: 'mdi-shopping', color: 'info', to: '/admin/orders' },
])

const userStats = computed(() => [
    { value: String(stats.value.referralsCount || 0), label: t('dashboard.myReferrals'), icon: 'mdi-account-group', color: 'info', to: '/admin/referrals' },
    { value: String(stats.value.ordersCount || 0), label: t('dashboard.myOrders'), icon: 'mdi-shopping', color: 'warning', to: '/admin/orders' },
    { value: String(stats.value.supportTicketsCount || 0), label: t('dashboard.supportTickets'), icon: 'mdi-headset', color: 'primary', to: '/admin/support' },
    { value: String(stats.value.transactionsCount || 0), label: t('nav.transactions'), icon: 'mdi-swap-horizontal', color: 'success', to: '/admin/transactions' },
])

const timePeriods = [
    { key: '24h', labelKey: 'dashboard.hours24', color: 'info' },
    { key: '7d', labelKey: 'dashboard.days7', color: 'success' },
    { key: '30d', labelKey: 'dashboard.days30', color: 'warning' },
    { key: 'lifetime', labelKey: 'dashboard.lifetime', color: 'error' },
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
const formatBalance = (b) => (parseFloat(b) || 0).toFixed(2)
const getStatusColor = (s) => ({ pending: 'warning', waiting: 'warning', processing: 'info', in_progress: 'info', completed: 'success', partial: 'orange', canceled: 'error', refunded: 'secondary' })[s.toLowerCase()] || 'grey'
const getStatusIcon = (s) => ({ pending: 'mdi-clock-outline', waiting: 'mdi-clock-outline', processing: 'mdi-cog-sync', in_progress: 'mdi-progress-clock', completed: 'mdi-check-circle', partial: 'mdi-circle-half-full', canceled: 'mdi-close-circle', refunded: 'mdi-cash-refund' })[s.toLowerCase()] || 'mdi-help-circle'
const getStatusText = (s) => { const k = `orders.${s.toLowerCase()}`; const r = t(k); return r === k ? s : r }

const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/dashboard')
        const data = response.data
        stats.value = { ordersCount: data.ordersCount || 0, referralsCount: data.referralsCount || 0, supportTicketsCount: data.supportTicketsCount || 0, transactionsCount: data.transactionsCount || 0 }
        ordersByStatus.value = data.ordersByStatus || {}
        if (data.admin) adminStats.value = data.admin
    } catch (error) { console.error('Dashboard error:', error) }
}

onMounted(() => { fetchDashboardData() })
</script>

<style scoped>
.welcome-card { border-radius: 16px; overflow: hidden; }
.welcome-content { display: flex; align-items: center; justify-content: space-between; padding: 28px 24px; }
.welcome-greeting { font-size: 1.3rem; font-weight: 700; color: white; }
.welcome-sub { font-size: 0.82rem; color: rgba(255,255,255,0.7); margin-top: 4px; }

.finance-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
.finance-card {
    padding: 18px; border-radius: 14px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}
.finance-header { margin-bottom: 12px; }
.finance-badge { font-size: 0.68rem; font-weight: 700; padding: 3px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
.finance-cost { font-size: 1.25rem; font-weight: 800; }
.finance-label { font-size: 0.68rem; opacity: 0.4; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 2px; }
.finance-divider { height: 1px; background: rgba(var(--v-border-color), var(--v-border-opacity)); margin: 10px 0; }
.finance-profit { font-size: 1.1rem; font-weight: 700; color: rgb(var(--v-theme-success)); }

.status-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; }
.status-item {
    text-align: center; padding: 16px 8px; border-radius: 12px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}
.status-count { font-size: 1.3rem; font-weight: 800; margin: 4px 0; }
.status-label { font-size: 0.65rem; opacity: 0.45; text-transform: capitalize; }

.actions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; }
.action-item {
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    padding: 20px 12px; border-radius: 14px; text-decoration: none; color: inherit;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    transition: border-color 0.15s ease, transform 0.15s ease;
}
.action-item:hover { border-color: rgb(var(--v-theme-primary)); transform: translateY(-2px); }
.action-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.action-text { font-size: 0.75rem; font-weight: 600; text-align: center; }

@media (max-width: 600px) {
    .finance-grid { grid-template-columns: repeat(2, 1fr); }
    .actions-grid { grid-template-columns: repeat(3, 1fr); }
    .welcome-greeting { font-size: 1.1rem; }
}
</style>
