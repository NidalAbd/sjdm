<template>
    <v-container fluid class="pa-6">
        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-space-between align-center mb-6">
            <div>
                <h1 class="text-h4 font-weight-bold mb-1">{{ $t('seo.dashboard') }}</h1>
                <p class="text-body-2 text-medium-emphasis">{{ $t('seo.dashboardDesc') }}</p>
            </div>
            <div class="d-flex ga-2">
                <v-btn color="primary" variant="outlined" @click="syncGsc" :loading="syncingGsc">
                    <v-icon start>mdi-google</v-icon>
                    {{ $t('seo.syncGsc') }}
                </v-btn>
                <v-btn color="secondary" variant="outlined" @click="syncTrends" :loading="syncingTrends">
                    <v-icon start>mdi-trending-up</v-icon>
                    {{ $t('seo.syncTrends') }}
                </v-btn>
                <v-btn color="success" @click="exportKeywords">
                    <v-icon start>mdi-download</v-icon>
                    {{ $t('seo.export') }}
                </v-btn>
            </div>
        </div>

        <!-- Stats Cards -->
        <v-row class="mb-6">
            <v-col cols="12" sm="6" md="3">
                <v-card class="hover-lift h-100">
                    <v-card-text class="text-center py-6">
                        <v-icon color="primary" size="48" class="mb-2">mdi-key-variant</v-icon>
                        <div class="text-h3 font-weight-bold">{{ stats.totalKeywords }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ $t('seo.totalKeywords') }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card class="hover-lift h-100">
                    <v-card-text class="text-center py-6">
                        <v-icon color="success" size="48" class="mb-2">mdi-cursor-default-click</v-icon>
                        <div class="text-h3 font-weight-bold">{{ formatNumber(stats.totalClicks) }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ $t('seo.totalClicks') }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card class="hover-lift h-100">
                    <v-card-text class="text-center py-6">
                        <v-icon color="info" size="48" class="mb-2">mdi-eye</v-icon>
                        <div class="text-h3 font-weight-bold">{{ formatNumber(stats.totalImpressions) }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ $t('seo.totalImpressions') }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card class="hover-lift h-100">
                    <v-card-text class="text-center py-6">
                        <v-icon color="warning" size="48" class="mb-2">mdi-chart-line</v-icon>
                        <div class="text-h3 font-weight-bold">{{ stats.averagePosition }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ $t('seo.avgPosition') }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Performance Chart -->
        <v-row class="mb-6">
            <v-col cols="12" lg="8">
                <v-card>
                    <v-card-title class="d-flex justify-space-between align-center">
                        <span>{{ $t('seo.performanceOverTime') }}</span>
                        <v-btn-toggle v-model="chartPeriod" mandatory density="compact">
                            <v-btn value="7">7D</v-btn>
                            <v-btn value="30">30D</v-btn>
                            <v-btn value="90">90D</v-btn>
                        </v-btn-toggle>
                    </v-card-title>
                    <v-card-text>
                        <div class="chart-container" style="height: 300px;">
                            <canvas ref="performanceChart"></canvas>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" lg="4">
                <v-card class="h-100">
                    <v-card-title>{{ $t('seo.deviceBreakdown') }}</v-card-title>
                    <v-card-text>
                        <div v-for="device in deviceData" :key="device.device" class="mb-4">
                            <div class="d-flex justify-space-between mb-1">
                                <span class="text-body-2">
                                    <v-icon size="18" class="mr-1">{{ getDeviceIcon(device.device) }}</v-icon>
                                    {{ device.device }}
                                </span>
                                <span class="text-body-2 font-weight-bold">{{ device.clicks }} clicks</span>
                            </div>
                            <v-progress-linear
                                :model-value="getDevicePercentage(device.clicks)"
                                :color="getDeviceColor(device.device)"
                                height="8"
                                rounded
                            ></v-progress-linear>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Keywords & Trends Tables -->
        <v-row>
            <!-- Top Keywords -->
            <v-col cols="12" lg="6">
                <v-card>
                    <v-card-title class="d-flex justify-space-between align-center">
                        <span>{{ $t('seo.topKeywords') }}</span>
                        <v-chip size="small" color="primary">GSC</v-chip>
                    </v-card-title>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="keywordHeaders"
                            :items="keywords"
                            :items-per-page="10"
                            density="compact"
                            class="elevation-0"
                        >
                            <template v-slot:item.keyword="{ item }">
                                <span class="font-weight-medium">{{ item.keyword }}</span>
                            </template>
                            <template v-slot:item.ctr="{ item }">
                                <v-chip size="small" :color="getCtrColor(item.ctr)">
                                    {{ item.ctr }}%
                                </v-chip>
                            </template>
                            <template v-slot:item.position="{ item }">
                                <v-chip size="small" :color="getPositionColor(item.position)">
                                    {{ item.position }}
                                </v-chip>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Rising Keywords (Trends) -->
            <v-col cols="12" lg="6">
                <v-card>
                    <v-card-title class="d-flex justify-space-between align-center">
                        <span>{{ $t('seo.risingKeywords') }}</span>
                        <v-chip size="small" color="success">
                            <v-icon start size="14">mdi-trending-up</v-icon>
                            Trends
                        </v-chip>
                    </v-card-title>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="trendHeaders"
                            :items="risingKeywords"
                            :items-per-page="10"
                            density="compact"
                            class="elevation-0"
                        >
                            <template v-slot:item.keyword="{ item }">
                                <span class="font-weight-medium">{{ item.keyword }}</span>
                            </template>
                            <template v-slot:item.is_trending="{ item }">
                                <v-icon v-if="item.is_trending" color="success" size="20">mdi-arrow-up-bold</v-icon>
                                <v-icon v-else color="grey" size="20">mdi-minus</v-icon>
                            </template>
                            <template v-slot:item.trend_score="{ item }">
                                <v-progress-linear
                                    :model-value="item.trend_score"
                                    color="success"
                                    height="6"
                                    rounded
                                    style="width: 60px;"
                                ></v-progress-linear>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Country Performance -->
        <v-row class="mt-6">
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{ $t('seo.performanceByCountry') }}</v-card-title>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="countryHeaders"
                            :items="countryData"
                            :items-per-page="10"
                            density="compact"
                            class="elevation-0"
                        >
                            <template v-slot:item.country="{ item }">
                                <div class="d-flex align-center">
                                    <span class="fi fi-{{ item.country?.toLowerCase() }} mr-2"></span>
                                    <span class="font-weight-medium">{{ getCountryName(item.country) }}</span>
                                </div>
                            </template>
                            <template v-slot:item.clicks="{ item }">
                                <span class="font-weight-bold text-success">{{ formatNumber(item.clicks) }}</span>
                            </template>
                            <template v-slot:item.impressions="{ item }">
                                {{ formatNumber(item.impressions) }}
                            </template>
                            <template v-slot:item.ctr="{ item }">
                                <v-chip size="small" :color="getCtrColor(item.ctr)">
                                    {{ item.ctr }}%
                                </v-chip>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Top Pages -->
        <v-row class="mt-6">
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{ $t('seo.topPages') }}</v-card-title>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="pageHeaders"
                            :items="pageData"
                            :items-per-page="10"
                            density="compact"
                            class="elevation-0"
                        >
                            <template v-slot:item.page="{ item }">
                                <a :href="item.page" target="_blank" class="text-decoration-none">
                                    {{ truncateUrl(item.page) }}
                                    <v-icon size="14" class="ml-1">mdi-open-in-new</v-icon>
                                </a>
                            </template>
                            <template v-slot:item.clicks="{ item }">
                                <span class="font-weight-bold text-success">{{ formatNumber(item.clicks) }}</span>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Snackbar for notifications -->
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000">
            {{ snackbar.message }}
        </v-snackbar>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import Chart from 'chart.js/auto'

const { t } = useI18n()

// Data
const loading = ref(false)
const syncingGsc = ref(false)
const syncingTrends = ref(false)
const chartPeriod = ref('30')
const performanceChart = ref(null)
let chartInstance = null

const stats = ref({
    totalKeywords: 0,
    totalClicks: 0,
    totalImpressions: 0,
    averageCtr: 0,
    averagePosition: 0,
})

const keywords = ref([])
const risingKeywords = ref([])
const countryData = ref([])
const deviceData = ref([])
const pageData = ref([])
const chartData = ref([])

const snackbar = ref({
    show: false,
    message: '',
    color: 'success'
})

// Table Headers
const keywordHeaders = [
    { title: t('seo.keyword'), key: 'keyword', sortable: true },
    { title: t('seo.clicks'), key: 'clicks', sortable: true },
    { title: t('seo.impressions'), key: 'impressions', sortable: true },
    { title: 'CTR', key: 'ctr', sortable: true },
    { title: t('seo.position'), key: 'position', sortable: true },
]

const trendHeaders = [
    { title: t('seo.keyword'), key: 'keyword', sortable: true },
    { title: t('seo.trending'), key: 'is_trending', sortable: false },
    { title: t('seo.score'), key: 'trend_score', sortable: true },
    { title: t('seo.language'), key: 'language', sortable: true },
]

const countryHeaders = [
    { title: t('seo.country'), key: 'country', sortable: true },
    { title: t('seo.clicks'), key: 'clicks', sortable: true },
    { title: t('seo.impressions'), key: 'impressions', sortable: true },
    { title: 'CTR', key: 'ctr', sortable: true },
    { title: t('seo.position'), key: 'position', sortable: true },
]

const pageHeaders = [
    { title: t('seo.page'), key: 'page', sortable: false },
    { title: t('seo.clicks'), key: 'clicks', sortable: true },
    { title: t('seo.impressions'), key: 'impressions', sortable: true },
    { title: 'CTR', key: 'ctr', sortable: true },
    { title: t('seo.position'), key: 'position', sortable: true },
]

// Country names map
const countryNames = {
    'USA': 'United States',
    'SAU': 'Saudi Arabia',
    'ARE': 'UAE',
    'EGY': 'Egypt',
    'JOR': 'Jordan',
    'GBR': 'United Kingdom',
    'IND': 'India',
    'PAK': 'Pakistan',
}

// Methods
const formatNumber = (num) => {
    if (!num) return '0'
    return new Intl.NumberFormat().format(num)
}

const getCountryName = (code) => {
    return countryNames[code] || code
}

const truncateUrl = (url) => {
    if (!url) return ''
    const path = url.replace(/^https?:\/\/[^\/]+/, '')
    return path || '/'
}

const getDeviceIcon = (device) => {
    const icons = {
        'MOBILE': 'mdi-cellphone',
        'DESKTOP': 'mdi-desktop-mac',
        'TABLET': 'mdi-tablet',
    }
    return icons[device] || 'mdi-devices'
}

const getDeviceColor = (device) => {
    const colors = {
        'MOBILE': 'primary',
        'DESKTOP': 'success',
        'TABLET': 'warning',
    }
    return colors[device] || 'grey'
}

const getDevicePercentage = (clicks) => {
    const total = deviceData.value.reduce((sum, d) => sum + d.clicks, 0)
    return total > 0 ? (clicks / total) * 100 : 0
}

const getCtrColor = (ctr) => {
    if (ctr >= 5) return 'success'
    if (ctr >= 2) return 'warning'
    return 'error'
}

const getPositionColor = (position) => {
    if (position <= 3) return 'success'
    if (position <= 10) return 'warning'
    return 'error'
}

// API calls
const fetchDashboard = async () => {
    try {
        const response = await fetch('/api/seo/dashboard')
        const data = await response.json()

        if (data.success) {
            stats.value = {
                totalKeywords: data.data.gsc?.total_keywords || 0,
                totalClicks: data.data.gsc?.total_clicks || 0,
                totalImpressions: data.data.gsc?.total_impressions || 0,
                averageCtr: data.data.gsc?.average_ctr || 0,
                averagePosition: data.data.gsc?.average_position || 0,
            }
        }
    } catch (error) {
        console.error('Error fetching dashboard:', error)
    }
}

const fetchKeywords = async () => {
    try {
        const response = await fetch('/api/seo/keywords?source=gsc&limit=50')
        const data = await response.json()
        if (data.success) {
            keywords.value = data.data
        }
    } catch (error) {
        console.error('Error fetching keywords:', error)
    }
}

const fetchRisingKeywords = async () => {
    try {
        const response = await fetch('/api/seo/trends/rising?limit=20')
        const data = await response.json()
        if (data.success) {
            risingKeywords.value = data.data
        }
    } catch (error) {
        console.error('Error fetching rising keywords:', error)
    }
}

const fetchCountryData = async () => {
    try {
        const response = await fetch('/api/seo/performance/country')
        const data = await response.json()
        if (data.success) {
            countryData.value = data.data
        }
    } catch (error) {
        console.error('Error fetching country data:', error)
    }
}

const fetchDeviceData = async () => {
    try {
        const response = await fetch('/api/seo/performance/device')
        const data = await response.json()
        if (data.success) {
            deviceData.value = data.data
        }
    } catch (error) {
        console.error('Error fetching device data:', error)
    }
}

const fetchPageData = async () => {
    try {
        const response = await fetch('/api/seo/performance/page')
        const data = await response.json()
        if (data.success) {
            pageData.value = data.data
        }
    } catch (error) {
        console.error('Error fetching page data:', error)
    }
}

const fetchChartData = async () => {
    try {
        const response = await fetch(`/api/seo/performance/chart?days=${chartPeriod.value}`)
        const data = await response.json()
        if (data.success) {
            chartData.value = data.data
            updateChart()
        }
    } catch (error) {
        console.error('Error fetching chart data:', error)
    }
}

const updateChart = () => {
    if (chartInstance) {
        chartInstance.destroy()
    }

    if (!performanceChart.value) return

    const ctx = performanceChart.value.getContext('2d')
    const labels = chartData.value.map(d => d.date)
    const clicks = chartData.value.map(d => d.clicks)
    const impressions = chartData.value.map(d => d.impressions)

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Clicks',
                    data: clicks,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Impressions',
                    data: impressions,
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Clicks',
                    },
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Impressions',
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                },
            },
        },
    })
}

const syncGsc = async () => {
    syncingGsc.value = true
    try {
        const response = await fetch('/api/seo/sync/gsc', { method: 'POST' })
        const data = await response.json()

        snackbar.value = {
            show: true,
            message: data.message,
            color: data.success ? 'success' : 'error'
        }

        if (data.success) {
            await fetchDashboard()
            await fetchKeywords()
        }
    } catch (error) {
        snackbar.value = {
            show: true,
            message: 'Failed to sync GSC data',
            color: 'error'
        }
    } finally {
        syncingGsc.value = false
    }
}

const syncTrends = async () => {
    syncingTrends.value = true
    try {
        const response = await fetch('/api/seo/sync/trends', { method: 'POST' })
        const data = await response.json()

        snackbar.value = {
            show: true,
            message: data.message,
            color: data.success ? 'success' : 'error'
        }

        if (data.success) {
            await fetchRisingKeywords()
        }
    } catch (error) {
        snackbar.value = {
            show: true,
            message: 'Failed to sync trends data',
            color: 'error'
        }
    } finally {
        syncingTrends.value = false
    }
}

const exportKeywords = () => {
    window.location.href = '/api/seo/keywords/export'
}

// Watch chart period changes
watch(chartPeriod, () => {
    fetchChartData()
})

// Initialize
onMounted(async () => {
    await Promise.all([
        fetchDashboard(),
        fetchKeywords(),
        fetchRisingKeywords(),
        fetchCountryData(),
        fetchDeviceData(),
        fetchPageData(),
        fetchChartData(),
    ])
})
</script>
