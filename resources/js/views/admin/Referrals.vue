<template>
    <div>
        <PageHeader title="Referrals" subtitle="Your referral program" icon="mdi-account-group">
        </PageHeader>

        <!-- Stats -->
        <v-row class="mb-4">
            <v-col cols="12" sm="4">
                <v-card color="primary" variant="flat">
                    <v-card-text class="text-white text-center">
                        <v-icon size="40" class="mb-2">mdi-account-multiple</v-icon>
                        <div class="text-h4 font-weight-bold">{{ stats.totalReferrals }}</div>
                        <div class="text-caption">Total Referrals</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4">
                <v-card color="success" variant="flat">
                    <v-card-text class="text-white text-center">
                        <v-icon size="40" class="mb-2">mdi-account-check</v-icon>
                        <div class="text-h4 font-weight-bold">{{ stats.activeReferrals }}</div>
                        <div class="text-caption">Active Referrals</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4">
                <v-card color="warning" variant="flat">
                    <v-card-text class="text-white text-center">
                        <v-icon size="40" class="mb-2">mdi-cash</v-icon>
                        <div class="text-h4 font-weight-bold">${{ formatNumber(stats.totalEarnings) }}</div>
                        <div class="text-caption">Total Earnings</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Referral Link Card -->
        <v-card class="mb-4">
            <v-card-title>
                <v-icon class="mr-2" size="small">mdi-link</v-icon>
                Your Referral Link
            </v-card-title>
            <v-card-text>
                <v-text-field
                    :model-value="referralLink"
                    variant="outlined"
                    readonly
                    prepend-inner-icon="mdi-link"
                >
                    <template v-slot:append-inner>
                        <v-btn color="primary" variant="flat" @click="copyLink">
                            <v-icon start>mdi-content-copy</v-icon>
                            Copy
                        </v-btn>
                    </template>
                </v-text-field>
                <v-row class="mt-2">
                    <v-col cols="auto">
                        <v-btn
                            color="success"
                            variant="tonal"
                            :href="`https://wa.me/?text=${encodeURIComponent('Join using my referral link: ' + referralLink)}`"
                            target="_blank"
                        >
                            <v-icon start>mdi-whatsapp</v-icon>
                            Share on WhatsApp
                        </v-btn>
                    </v-col>
                    <v-col cols="auto">
                        <v-btn
                            color="info"
                            variant="tonal"
                            :href="`https://telegram.me/share/url?url=${encodeURIComponent(referralLink)}&text=${encodeURIComponent('Join using my referral link!')}`"
                            target="_blank"
                        >
                            <v-icon start>mdi-telegram</v-icon>
                            Share on Telegram
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Referrals List -->
        <v-card>
            <v-card-title>
                <v-icon class="mr-2" size="small">mdi-format-list-bulleted</v-icon>
                Your Referrals
            </v-card-title>
            <v-data-table
                :headers="headers"
                :items="referrals"
                :loading="loading"
                class="elevation-0"
            >
                <!-- User Column -->
                <template v-slot:item.user="{ item }">
                    <div class="d-flex align-center py-2">
                        <v-avatar size="36" color="primary" class="mr-2">
                            <span class="text-white">{{ item.name?.charAt(0) || 'U' }}</span>
                        </v-avatar>
                        <div>
                            <div class="font-weight-medium">{{ item.name }}</div>
                            <div class="text-caption text-grey">{{ item.email }}</div>
                        </div>
                    </div>
                </template>

                <!-- Status Column -->
                <template v-slot:item.status="{ item }">
                    <v-chip
                        :color="item.email_verified_at ? 'success' : 'warning'"
                        size="small"
                        variant="flat"
                    >
                        {{ item.email_verified_at ? 'Verified' : 'Pending' }}
                    </v-chip>
                </template>

                <!-- Orders Column -->
                <template v-slot:item.orders_count="{ item }">
                    <v-chip size="small" variant="tonal">
                        {{ item.orders_count || 0 }}
                    </v-chip>
                </template>

                <!-- Earnings Column -->
                <template v-slot:item.earnings="{ item }">
                    <span class="font-weight-bold text-success">
                        ${{ formatNumber(item.earnings || 0) }}
                    </span>
                </template>

                <!-- Date Column -->
                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <!-- Empty State -->
                <template v-slot:no-data>
                    <div class="text-center py-8">
                        <v-icon size="64" color="grey">mdi-account-group-outline</v-icon>
                        <div class="text-h6 mt-4 text-grey">No referrals yet</div>
                        <div class="text-body-2 text-grey">Share your referral link to start earning!</div>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <!-- How It Works -->
        <v-card class="mt-4">
            <v-card-title>
                <v-icon class="mr-2" size="small">mdi-help-circle</v-icon>
                How It Works
            </v-card-title>
            <v-card-text>
                <v-timeline density="compact" side="end">
                    <v-timeline-item dot-color="primary" size="small">
                        <template v-slot:icon>
                            <span class="text-white">1</span>
                        </template>
                        <div class="font-weight-bold">Share Your Link</div>
                        <div class="text-grey">Copy and share your unique referral link with friends.</div>
                    </v-timeline-item>
                    <v-timeline-item dot-color="success" size="small">
                        <template v-slot:icon>
                            <span class="text-white">2</span>
                        </template>
                        <div class="font-weight-bold">Friend Signs Up</div>
                        <div class="text-grey">Your friend creates an account using your link.</div>
                    </v-timeline-item>
                    <v-timeline-item dot-color="warning" size="small">
                        <template v-slot:icon>
                            <span class="text-white">3</span>
                        </template>
                        <div class="font-weight-bold">Friend Makes Purchase</div>
                        <div class="text-grey">When your friend makes their first purchase, you both earn rewards!</div>
                    </v-timeline-item>
                    <v-timeline-item dot-color="info" size="small">
                        <template v-slot:icon>
                            <span class="text-white">4</span>
                        </template>
                        <div class="font-weight-bold">Get Rewarded</div>
                        <div class="text-grey">Earn commission on all future purchases made by your referrals.</div>
                    </v-timeline-item>
                </v-timeline>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'
import PageHeader from '../../components/PageHeader.vue'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const referrals = ref([])
const loading = ref(false)

const stats = ref({
    totalReferrals: 0,
    activeReferrals: 0,
    totalEarnings: 0,
})

const referralLink = computed(() => {
    const code = authStore.user?.referral_code
    if (!code) return ''
    return `${window.location.origin}/register?ref=${code}`
})

// Table headers
const headers = [
    { title: 'User', key: 'user', sortable: false },
    { title: 'Status', key: 'status' },
    { title: 'Orders', key: 'orders_count', align: 'center' },
    { title: 'Your Earnings', key: 'earnings', align: 'end' },
    { title: 'Joined', key: 'created_at' },
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

const copyLink = () => {
    navigator.clipboard.writeText(referralLink.value)
    showSnackbar?.('Referral link copied!', 'success')
}

const fetchReferrals = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/referrals')
        referrals.value = response.data.referrals || []
        const apiStats = response.data.stats || {}
        stats.value = {
            totalReferrals: apiStats.total_referrals || 0,
            activeReferrals: apiStats.active_referrals || 0,
            totalEarnings: apiStats.total_earnings || 0,
        }
    } catch (error) {
        console.error('Error fetching referrals:', error)
    } finally {
        loading.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchReferrals()
})
</script>
