<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center flex-wrap ga-2">
                <v-icon class="mr-2">mdi-cog</v-icon>
                <span>Manage Services</span>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>

        <!-- Admin Bulk Rate Management -->
        <v-card v-if="authStore.isAdmin" class="mb-4 bulk-rate-card">
            <v-card-title class="bg-gradient text-white d-flex align-center">
                <v-icon class="mr-2">mdi-cog-sync</v-icon>
                Bulk Rate Management
            </v-card-title>
            <v-card-text class="pa-4">
                <!-- Digit-based Rate Management -->
                <div class="mb-6">
                    <h3 class="text-subtitle-1 font-weight-bold mb-3">
                        <v-icon class="mr-1" size="small">mdi-layers</v-icon>
                        Digit-based Rate Adjustment
                    </h3>
                    <v-row>
                        <v-col v-for="range in digitRanges" :key="range.id" cols="6" sm="4" md="2">
                            <v-text-field
                                v-model="digitPercentages[range.id]"
                                :label="range.label"
                                type="number"
                                variant="outlined"
                                density="compact"
                                hide-details
                                suffix="%"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row class="mt-3">
                        <v-col cols="12" sm="6" md="3">
                            <v-select
                                v-model="digitOperation"
                                :items="operationOptions"
                                label="Operation"
                                variant="outlined"
                                density="compact"
                                hide-details
                            ></v-select>
                        </v-col>
                        <v-col cols="6" sm="3" md="2">
                            <v-btn
                                color="success"
                                block
                                @click="updateDigitRates"
                                :loading="updatingRates"
                                prepend-icon="mdi-layers"
                            >
                                Update
                            </v-btn>
                        </v-col>
                        <v-col cols="6" sm="3" md="2">
                            <v-btn
                                color="warning"
                                block
                                @click="previewDigitRates"
                                :loading="previewing"
                                prepend-icon="mdi-eye"
                            >
                                Preview
                            </v-btn>
                        </v-col>
                        <v-col cols="6" sm="3" md="2">
                            <v-btn
                                color="info"
                                block
                                @click="getStats"
                                :loading="loadingStats"
                                prepend-icon="mdi-chart-bar"
                            >
                                Stats
                            </v-btn>
                        </v-col>
                        <v-col cols="6" sm="3" md="3">
                            <v-btn
                                color="grey"
                                block
                                @click="resetPercentages"
                                prepend-icon="mdi-refresh"
                            >
                                Reset
                            </v-btn>
                        </v-col>
                    </v-row>
                </div>

                <v-divider class="my-4"></v-divider>

                <!-- Simple Percentage Management -->
                <div>
                    <h3 class="text-subtitle-1 font-weight-bold mb-3">
                        <v-icon class="mr-1" size="small">mdi-percent</v-icon>
                        Simple Percentage Adjustment
                    </h3>
                    <v-row>
                        <v-col cols="12" sm="4" md="3">
                            <v-text-field
                                v-model="simplePercentage"
                                label="Percentage"
                                type="number"
                                variant="outlined"
                                density="compact"
                                hide-details
                                suffix="%"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="4" md="3">
                            <v-select
                                v-model="simpleOperation"
                                :items="operationOptions"
                                label="Operation"
                                variant="outlined"
                                density="compact"
                                hide-details
                            ></v-select>
                        </v-col>
                        <v-col cols="6" sm="2" md="3">
                            <v-btn
                                color="primary"
                                block
                                @click="updateAllRates"
                                :loading="updatingRates"
                                prepend-icon="mdi-sync"
                            >
                                Update All
                            </v-btn>
                        </v-col>
                    </v-row>
                </div>

                <!-- Stats Display -->
                <v-expand-transition>
                    <v-alert v-if="stats" type="info" variant="tonal" class="mt-4" closable @click:close="stats = null">
                        <v-row dense>
                            <v-col cols="6" sm="4" md="2">
                                <div class="text-caption">Total Services</div>
                                <div class="font-weight-bold">{{ stats.total_services }}</div>
                            </v-col>
                            <v-col cols="6" sm="4" md="2">
                                <div class="text-caption">Avg Rate</div>
                                <div class="font-weight-bold">${{ formatNumber(stats.avg_rate) }}</div>
                            </v-col>
                            <v-col cols="6" sm="4" md="2">
                                <div class="text-caption">Min Rate</div>
                                <div class="font-weight-bold">${{ formatNumber(stats.min_rate) }}</div>
                            </v-col>
                            <v-col cols="6" sm="4" md="2">
                                <div class="text-caption">Max Rate</div>
                                <div class="font-weight-bold">${{ formatNumber(stats.max_rate) }}</div>
                            </v-col>
                            <v-col cols="6" sm="4" md="2">
                                <div class="text-caption">Avg Cost</div>
                                <div class="font-weight-bold">${{ formatNumber(stats.avg_cost) }}</div>
                            </v-col>
                            <v-col cols="6" sm="4" md="2">
                                <div class="text-caption">Total Margin</div>
                                <div class="font-weight-bold">${{ formatNumber(stats.total_margin) }}</div>
                            </v-col>
                        </v-row>
                    </v-alert>
                </v-expand-transition>
            </v-card-text>
        </v-card>

        <!-- Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            v-model="filters.search"
                            label="Search services"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                            hide-details
                            @keyup.enter="fetchServices"
                        ></v-text-field>
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
                            @update:model-value="fetchServices"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.category"
                            :items="categoryOptions"
                            label="Category"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchServices"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                        <v-btn color="primary" block @click="fetchServices" prepend-icon="mdi-magnify">
                            Search
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Services Table -->
        <v-card>
            <v-data-table-server
                v-model:items-per-page="itemsPerPage"
                v-model:page="page"
                :headers="headers"
                :items="services"
                :items-length="totalServices"
                :loading="loading"
                class="elevation-0 services-table"
                density="compact"
                @update:options="onTableUpdate"
            >
                <!-- Service ID Column -->
                <template v-slot:item.service_id="{ item }">
                    <span class="text-grey-darken-1 text-caption">{{ item.service_id }}</span>
                </template>

                <!-- Name Column with truncation -->
                <template v-slot:item.name="{ item }">
                    <div class="service-name" :title="locale === 'ar' ? item.name_ar : item.name_en">
                        {{ locale === 'ar' ? item.name_ar : item.name_en }}
                    </div>
                </template>

                <!-- Category Column with truncation -->
                <template v-slot:item.category="{ item }">
                    <v-chip
                        size="x-small"
                        variant="tonal"
                        color="info"
                        class="category-chip"
                        :title="locale === 'ar' ? item.category_ar : item.category_en"
                    >
                        {{ truncateText(locale === 'ar' ? item.category_ar : item.category_en, 25) }}
                    </v-chip>
                </template>

                <!-- Rate Column -->
                <template v-slot:item.rate="{ item }">
                    <div v-if="authStore.isAdmin && editingRate === item.service_id" class="d-flex align-center ga-1">
                        <v-text-field
                            v-model="editRateValue"
                            type="number"
                            step="0.0001"
                            min="0"
                            variant="outlined"
                            density="compact"
                            hide-details
                            style="max-width: 80px"
                            @keyup.enter="saveRate(item)"
                            @keyup.escape="cancelEditRate"
                        ></v-text-field>
                        <v-btn icon size="x-small" color="success" @click="saveRate(item)">
                            <v-icon size="small">mdi-check</v-icon>
                        </v-btn>
                        <v-btn icon size="x-small" @click="cancelEditRate">
                            <v-icon size="small">mdi-close</v-icon>
                        </v-btn>
                    </div>
                    <div v-else class="d-flex align-center justify-end">
                        <span class="font-weight-bold text-success text-caption">${{ formatNumber(item.rate, 4) }}</span>
                        <v-btn
                            v-if="authStore.isAdmin"
                            icon
                            size="x-small"
                            variant="text"
                            class="ml-1 opacity-50 edit-btn"
                            @click="startEditRate(item)"
                        >
                            <v-icon size="x-small">mdi-pencil</v-icon>
                        </v-btn>
                    </div>
                </template>

                <!-- Cost Column (Admin Only) -->
                <template v-slot:item.cost="{ item }">
                    <span class="text-grey text-caption">${{ formatNumber(item.cost || item.api_rate, 4) }}</span>
                </template>

                <!-- Min Column -->
                <template v-slot:item.min="{ item }">
                    <span class="text-caption">{{ formatCompactNumber(item.min) }}</span>
                </template>

                <!-- Max Column -->
                <template v-slot:item.max="{ item }">
                    <span class="text-caption">{{ formatCompactNumber(item.max) }}</span>
                </template>

                <!-- Refill Column -->
                <template v-slot:item.refill="{ item }">
                    <v-icon size="small" :color="item.refill ? 'success' : 'grey-lighten-1'">
                        {{ item.refill ? 'mdi-check-circle' : 'mdi-circle-outline' }}
                    </v-icon>
                </template>

                <!-- Cancel Column -->
                <template v-slot:item.cancel="{ item }">
                    <v-icon size="small" :color="item.cancel ? 'success' : 'grey-lighten-1'">
                        {{ item.cancel ? 'mdi-check-circle' : 'mdi-circle-outline' }}
                    </v-icon>
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex ga-0">
                        <v-btn
                            icon
                            size="x-small"
                            variant="text"
                            color="info"
                            @click="viewService(item)"
                        >
                            <v-icon size="small">mdi-eye</v-icon>
                            <v-tooltip activator="parent" location="top">View</v-tooltip>
                        </v-btn>
                        <v-btn
                            v-if="authStore.isAdmin"
                            icon
                            size="x-small"
                            variant="text"
                            color="warning"
                            :to="`/admin/services/${item.service_id}/edit`"
                        >
                            <v-icon size="small">mdi-pencil</v-icon>
                            <v-tooltip activator="parent" location="top">Edit</v-tooltip>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- Service Details Dialog -->
        <v-dialog v-model="serviceDialog" max-width="700">
            <v-card v-if="selectedService">
                <v-card-title class="bg-info text-white">
                    <v-icon class="mr-2">mdi-cog</v-icon>
                    Service Details
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-row>
                        <v-col cols="12">
                            <div class="text-caption text-grey">Category</div>
                            <div class="font-weight-medium">
                                {{ locale === 'ar' ? selectedService.category_ar : selectedService.category_en }}
                            </div>
                        </v-col>
                        <v-col cols="12">
                            <div class="text-caption text-grey">Service Name</div>
                            <div class="font-weight-bold text-h6">
                                {{ locale === 'ar' ? selectedService.name_ar : selectedService.name_en }}
                            </div>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <v-icon color="primary" class="mb-1">mdi-currency-usd</v-icon>
                                <div class="text-h5 font-weight-bold text-primary">
                                    ${{ formatNumber(selectedService.rate) }}
                                </div>
                                <div class="text-caption text-grey">Rate per 1000</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <v-icon color="success" class="mb-1">mdi-arrow-down</v-icon>
                                <div class="text-h5 font-weight-bold">{{ selectedService.min }}</div>
                                <div class="text-caption text-grey">Minimum</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <v-icon color="warning" class="mb-1">mdi-arrow-up</v-icon>
                                <div class="text-h5 font-weight-bold">{{ selectedService.max }}</div>
                                <div class="text-caption text-grey">Maximum</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6">
                            <v-card variant="outlined" class="pa-3">
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <v-icon class="mr-1">mdi-refresh</v-icon>
                                        Refill
                                    </div>
                                    <v-chip :color="selectedService.refill ? 'success' : 'error'" size="small">
                                        {{ selectedService.refill ? 'Yes' : 'No' }}
                                    </v-chip>
                                </div>
                            </v-card>
                        </v-col>
                        <v-col cols="6">
                            <v-card variant="outlined" class="pa-3">
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <v-icon class="mr-1">mdi-cancel</v-icon>
                                        Cancel
                                    </div>
                                    <v-chip :color="selectedService.cancel ? 'success' : 'error'" size="small">
                                        {{ selectedService.cancel ? 'Yes' : 'No' }}
                                    </v-chip>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="serviceDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Preview Dialog -->
        <v-dialog v-model="previewDialog" max-width="1000">
            <v-card>
                <v-card-title class="bg-warning text-white">
                    <v-icon class="mr-2">mdi-eye</v-icon>
                    Preview Rate Changes
                </v-card-title>
                <v-card-text class="pa-0">
                    <v-table>
                        <thead>
                            <tr class="bg-grey-lighten-3">
                                <th>Service Name</th>
                                <th>Digit Range</th>
                                <th class="text-end">Original Cost</th>
                                <th class="text-end">Current Rate</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-end">New Rate</th>
                                <th class="text-end">Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in previewData" :key="item.service_id">
                                <td>{{ item.name }}</td>
                                <td>
                                    <v-chip size="small" color="info" variant="tonal">
                                        {{ getDigitRangeLabel(item.digit_range) }}
                                    </v-chip>
                                </td>
                                <td class="text-end">${{ formatNumber(item.original_cost, 6) }}</td>
                                <td class="text-end">${{ formatNumber(item.current_rate, 4) }}</td>
                                <td class="text-center">
                                    <v-chip size="small" color="warning">{{ item.percentage }}%</v-chip>
                                </td>
                                <td class="text-end font-weight-bold">${{ formatNumber(item.new_rate, 4) }}</td>
                                <td class="text-end" :class="item.change >= 0 ? 'text-success' : 'text-error'">
                                    {{ item.change >= 0 ? '+' : '' }}${{ formatNumber(Math.abs(item.change), 4) }}
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="previewDialog = false">Cancel</v-btn>
                    <v-btn color="success" @click="confirmDigitRates" :loading="updatingRates">
                        Confirm Changes
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useAppStore } from '../../stores/app'
import axios from 'axios'

const authStore = useAuthStore()
const appStore = useAppStore()

const locale = computed(() => appStore.locale)

// Data
const services = ref([])
const totalServices = ref(0)
const loading = ref(false)
const page = ref(1)
const itemsPerPage = ref(20)

// Filters
const filters = ref({
    search: '',
    platform: null,
    category: null,
})

const platformOptions = ref([])
const categoryOptions = ref([])

// Rate editing
const editingRate = ref(null)
const editRateValue = ref(0)

// Bulk rate management
const digitRanges = [
    { id: 0, label: '< 0.0001' },
    { id: 1, label: '0.0001 - 0.001' },
    { id: 2, label: '0.001 - 0.01' },
    { id: 3, label: '0.01 - 0.1' },
    { id: 4, label: '0.1 - 1' },
    { id: 5, label: '1+' },
]

const digitPercentages = ref({})
const digitOperation = ref('increase')
const simplePercentage = ref(null)
const simpleOperation = ref('increase')
const operationOptions = [
    { title: 'Increase', value: 'increase' },
    { title: 'Decrease', value: 'decrease' },
    { title: 'Multiply', value: 'multiply' },
]

const updatingRates = ref(false)
const previewing = ref(false)
const loadingStats = ref(false)
const stats = ref(null)

// Dialogs
const serviceDialog = ref(false)
const previewDialog = ref(false)
const selectedService = ref(null)
const previewData = ref([])

// Table headers - optimized column widths
const headers = computed(() => {
    const cols = [
        { title: 'ID', key: 'service_id', width: '70px' },
        { title: 'Name', key: 'name', sortable: false, width: '35%' },
        { title: 'Category', key: 'category', sortable: false, width: '20%' },
        { title: 'Rate', key: 'rate', align: 'end', width: '80px' },
    ]

    if (authStore.isAdmin) {
        cols.push({ title: 'Cost', key: 'cost', align: 'end', width: '70px' })
    }

    cols.push(
        { title: 'Min', key: 'min', align: 'end', width: '60px' },
        { title: 'Max', key: 'max', align: 'end', width: '70px' },
        { title: 'R', key: 'refill', align: 'center', width: '40px' },
        { title: 'C', key: 'cancel', align: 'center', width: '40px' },
        { title: '', key: 'actions', sortable: false, align: 'center', width: '80px' }
    )

    return cols
})

// Methods
const formatNumber = (num, decimals = 2) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(num || 0)
}

const formatCompactNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
    } else if (num >= 1000) {
        return (num / 1000).toFixed(0) + 'K'
    }
    return num
}

const truncateText = (text, maxLength) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    return text.substring(0, maxLength) + '...'
}

const getDigitRangeLabel = (range) => {
    const labels = {
        0: '< 0.0001',
        1: '0.0001 - 0.001',
        2: '0.001 - 0.01',
        3: '0.01 - 0.1',
        4: '0.1 - 1',
        5: '1+',
    }
    return labels[range] || 'Unknown'
}

const fetchServices = async () => {
    loading.value = true
    try {
        const params = {
            page: page.value,
            per_page: itemsPerPage.value,
            search: filters.value.search,
            platform: filters.value.platform,
            category: filters.value.category,
        }

        // Use admin endpoint if admin
        const endpoint = authStore.isAdmin ? '/api/admin/services' : '/api/services'
        const response = await axios.get(endpoint, { params })
        services.value = response.data.services || []
        totalServices.value = response.data.pagination?.total || 0

        // Update filter options
        if (response.data.platforms) {
            platformOptions.value = [
                { title: 'All Platforms', value: null },
                ...response.data.platforms.map(p => ({ title: p, value: p }))
            ]
        }
        if (response.data.categories) {
            categoryOptions.value = [
                { title: 'All Categories', value: null },
                ...response.data.categories.map(c => ({ title: c, value: c }))
            ]
        }
    } catch (error) {
        console.error('Error fetching services:', error)
    } finally {
        loading.value = false
    }
}

const onTableUpdate = ({ page: newPage, itemsPerPage: newItemsPerPage }) => {
    page.value = newPage
    itemsPerPage.value = newItemsPerPage
    fetchServices()
}

const viewService = (service) => {
    selectedService.value = service
    serviceDialog.value = true
}

const startEditRate = (service) => {
    editingRate.value = service.service_id
    editRateValue.value = service.rate
}

const cancelEditRate = () => {
    editingRate.value = null
    editRateValue.value = 0
}

const saveRate = async (service) => {
    try {
        await axios.put(`/api/admin/services/${service.id}/rate`, {
            rate: editRateValue.value
        })
        service.rate = editRateValue.value
        cancelEditRate()
    } catch (error) {
        console.error('Error updating rate:', error)
    }
}

const resetPercentages = () => {
    digitPercentages.value = {}
    simplePercentage.value = null
}

const getStats = async () => {
    loadingStats.value = true
    try {
        const response = await axios.get('/services/rate-stats')
        stats.value = response.data
    } catch (error) {
        console.error('Error fetching stats:', error)
    } finally {
        loadingStats.value = false
    }
}

const updateAllRates = async () => {
    if (!simplePercentage.value) return

    if (!confirm(`Are you sure you want to ${simpleOperation.value} all rates by ${simplePercentage.value}%?`)) {
        return
    }

    updatingRates.value = true
    try {
        await axios.post('/api/admin/services/update-all-rates', {
            percentage: simplePercentage.value,
            operation: simpleOperation.value,
        })
        fetchServices()
    } catch (error) {
        console.error('Error updating rates:', error)
    } finally {
        updatingRates.value = false
    }
}

const previewDigitRates = async () => {
    const hasValues = Object.values(digitPercentages.value).some(v => v && v !== 0)
    if (!hasValues) {
        alert('Please enter at least one percentage value')
        return
    }

    previewing.value = true
    try {
        const response = await axios.post('/services/preview-digit-rates', {
            percentages: digitPercentages.value,
            operation: digitOperation.value,
        })
        previewData.value = response.data.preview_data
        previewDialog.value = true
    } catch (error) {
        console.error('Error previewing rates:', error)
    } finally {
        previewing.value = false
    }
}

const updateDigitRates = async () => {
    const hasValues = Object.values(digitPercentages.value).some(v => v && v !== 0)
    if (!hasValues) {
        alert('Please enter at least one percentage value')
        return
    }

    if (!confirm('Are you sure you want to update rates based on digit ranges?')) {
        return
    }

    updatingRates.value = true
    try {
        await axios.post('/services/update-digit-rates', {
            percentages: digitPercentages.value,
            operation: digitOperation.value,
        })
        fetchServices()
    } catch (error) {
        console.error('Error updating digit rates:', error)
    } finally {
        updatingRates.value = false
    }
}

const confirmDigitRates = async () => {
    updatingRates.value = true
    try {
        await axios.post('/services/update-digit-rates', {
            percentages: digitPercentages.value,
            operation: digitOperation.value,
        })
        previewDialog.value = false
        fetchServices()
    } catch (error) {
        console.error('Error updating digit rates:', error)
    } finally {
        updatingRates.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchServices()
})
</script>

<style scoped>
.bulk-rate-card .bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Services table styles */
.services-table {
    font-size: 0.85rem;
}

.services-table :deep(th) {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
    padding: 8px 6px !important;
}

.services-table :deep(td) {
    padding: 6px 6px !important;
    vertical-align: middle !important;
}

/* Name column - truncate with ellipsis */
.service-name {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 500;
    font-size: 0.8rem;
}

/* Category chip */
.category-chip {
    max-width: 150px;
    font-size: 0.7rem !important;
}

/* Show edit button on hover */
.services-table :deep(tr:hover) .edit-btn {
    opacity: 1 !important;
}

/* Compact row height */
.services-table :deep(.v-data-table__tr) {
    height: 40px !important;
}
</style>
