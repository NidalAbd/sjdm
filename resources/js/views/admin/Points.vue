<template>
    <div>
        <PageHeader title="Points" subtitle="Your points & rewards" icon="mdi-star">
        </PageHeader>

        <!-- Points Overview -->
        <StatsRow :stats="statsData" />

        <!-- Redeem Points -->
        <v-card class="mb-4">
            <v-card-title>
                <v-icon class="mr-2" size="small">mdi-gift-open</v-icon>
                Redeem Points
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="redeemAmount"
                            label="Points to Redeem"
                            type="number"
                            min="0"
                            :max="stats.redeemablePoints"
                            variant="outlined"
                            :hint="`Value: $${formatNumber(redeemAmount * pointsRate)}`"
                            persistent-hint
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" class="d-flex align-center">
                        <v-btn
                            color="success"
                            size="large"
                            @click="redeemPoints"
                            :loading="redeeming"
                            :disabled="redeemAmount <= 0 || redeemAmount > stats.redeemablePoints"
                        >
                            <v-icon start>mdi-check</v-icon>
                            Redeem for ${{ formatNumber(redeemAmount * pointsRate) }}
                        </v-btn>
                    </v-col>
                </v-row>
                <v-alert type="info" variant="tonal" class="mt-4">
                    <strong>Points Conversion Rate:</strong> 1 point = ${{ pointsRate }}
                </v-alert>
            </v-card-text>
        </v-card>

        <!-- Points History -->
        <v-card>
            <v-card-title>
                <v-icon class="mr-2" size="small">mdi-history</v-icon>
                Points History
            </v-card-title>
            <v-data-table
                :headers="headers"
                :items="history"
                :loading="loading"
                class="elevation-0"
            >
                <!-- Type Column -->
                <template v-slot:item.type="{ item }">
                    <v-chip
                        :color="item.type === 'earned' ? 'success' : item.type === 'redeemed' ? 'info' : 'warning'"
                        size="small"
                        variant="flat"
                    >
                        <v-icon start size="small">
                            {{ item.type === 'earned' ? 'mdi-plus' : item.type === 'redeemed' ? 'mdi-gift' : 'mdi-minus' }}
                        </v-icon>
                        {{ item.type }}
                    </v-chip>
                </template>

                <!-- Points Column -->
                <template v-slot:item.points="{ item }">
                    <span
                        class="font-weight-bold"
                        :class="item.type === 'earned' ? 'text-success' : 'text-error'"
                    >
                        {{ item.type === 'earned' ? '+' : '-' }}{{ item.points }}
                    </span>
                </template>

                <!-- Description Column -->
                <template v-slot:item.description="{ item }">
                    {{ item.description || 'Points transaction' }}
                </template>

                <!-- Date Column -->
                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <!-- Empty State -->
                <template v-slot:no-data>
                    <div class="text-center py-8">
                        <v-icon size="64" color="grey">mdi-star-off</v-icon>
                        <div class="text-h6 mt-4 text-grey">No points history</div>
                        <div class="text-body-2 text-grey">Start earning points by placing orders!</div>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <!-- How to Earn Points -->
        <v-card class="mt-4">
            <v-card-title>
                <v-icon class="mr-2" size="small">mdi-information</v-icon>
                How to Earn Points
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-card variant="outlined" class="pa-4 text-center h-100">
                            <v-icon size="48" color="primary" class="mb-2">mdi-shopping</v-icon>
                            <div class="text-h6">Place Orders</div>
                            <div class="text-body-2 text-grey">
                                Earn 1 point for every $1 spent on orders.
                            </div>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card variant="outlined" class="pa-4 text-center h-100">
                            <v-icon size="48" color="success" class="mb-2">mdi-account-plus</v-icon>
                            <div class="text-h6">Refer Friends</div>
                            <div class="text-body-2 text-grey">
                                Earn bonus points when your referrals make purchases.
                            </div>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card variant="outlined" class="pa-4 text-center h-100">
                            <v-icon size="48" color="warning" class="mb-2">mdi-calendar-star</v-icon>
                            <div class="text-h6">Special Events</div>
                            <div class="text-body-2 text-grey">
                                Earn bonus points during promotional periods.
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import axios from 'axios'
import PageHeader from '../../components/PageHeader.vue'
import StatsRow from '../../components/StatsRow.vue'

const showSnackbar = inject('showSnackbar')

// Data
const history = ref([])
const loading = ref(false)
const redeeming = ref(false)
const redeemAmount = ref(0)
const pointsRate = 0.01 // 1 point = $0.01

const stats = ref({
    totalPoints: 0,
    pointsValue: 0,
    redeemablePoints: 0,
})

const statsData = computed(() => [
    { value: stats.value.totalPoints, label: 'Total Points', icon: 'mdi-star', color: 'warning' },
    { value: '$' + formatNumber(stats.value.pointsValue), label: 'Points Value', icon: 'mdi-cash', color: 'success' },
    { value: stats.value.redeemablePoints, label: 'Redeemable', icon: 'mdi-gift', color: 'info' },
])

// Table headers
const headers = [
    { title: 'Type', key: 'type' },
    { title: 'Points', key: 'points', align: 'end' },
    { title: 'Description', key: 'description' },
    { title: 'Date', key: 'created_at' },
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

const fetchPoints = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/points')
        history.value = response.data.transactions || response.data.history || []
        const apiStats = response.data.stats || {}
        stats.value = {
            totalPoints: apiStats.points || apiStats.totalPoints || 0,
            pointsValue: apiStats.points_value || apiStats.pointsValue || 0,
            redeemablePoints: apiStats.points || apiStats.redeemablePoints || 0,
        }
    } catch (error) {
        console.error('Error fetching points:', error)
    } finally {
        loading.value = false
    }
}

const redeemPoints = async () => {
    if (redeemAmount.value <= 0 || redeemAmount.value > stats.value.redeemablePoints) {
        showSnackbar?.('Invalid points amount', 'error')
        return
    }

    redeeming.value = true
    try {
        await axios.post('/api/points/redeem', {
            points: redeemAmount.value
        })
        showSnackbar?.(`Successfully redeemed ${redeemAmount.value} points!`, 'success')
        redeemAmount.value = 0
        fetchPoints()
    } catch (error) {
        console.error('Error redeeming points:', error)
        showSnackbar?.('Failed to redeem points', 'error')
    } finally {
        redeeming.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchPoints()
})
</script>
