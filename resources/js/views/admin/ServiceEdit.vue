<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center">
                <v-btn icon variant="text" to="/admin/services" class="mr-2">
                    <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
                <v-icon class="mr-2">mdi-cog</v-icon>
                Edit Service
            </v-card-title>
        </v-card>

        <v-row v-if="loading">
            <v-col cols="12" class="text-center py-10">
                <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                <div class="mt-4 text-grey">Loading service...</div>
            </v-col>
        </v-row>

        <v-row v-else-if="service">
            <v-col cols="12" md="8">
                <v-card>
                    <v-card-text class="pa-6">
                        <!-- Service Info Header -->
                        <v-alert type="info" variant="tonal" class="mb-6">
                            <div class="d-flex flex-wrap align-center gap-4">
                                <div>
                                    <span class="text-caption">Service ID:</span>
                                    <strong class="ml-1">{{ service.service_id }}</strong>
                                </div>
                                <div>
                                    <span class="text-caption">Category:</span>
                                    <strong class="ml-1">{{ locale === 'ar' ? service.category_ar : service.category_en }}</strong>
                                </div>
                                <div>
                                    <span class="text-caption">API Rate:</span>
                                    <strong class="ml-1 text-primary">${{ formatNumber(service.original_rate || service.rate, 4) }}/1k</strong>
                                </div>
                            </div>
                        </v-alert>

                        <!-- Name Fields -->
                        <div class="text-subtitle-2 font-weight-bold mb-3">Service Names</div>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.name_en"
                                    label="Name (English)"
                                    variant="outlined"
                                    :rules="[v => !!v || 'English name is required']"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.name_ar"
                                    label="Name (Arabic)"
                                    variant="outlined"
                                    dir="rtl"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <!-- Description Fields -->
                        <div class="text-subtitle-2 font-weight-bold mb-3 mt-4">Descriptions</div>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    v-model="form.description_en"
                                    label="Description (English)"
                                    variant="outlined"
                                    rows="3"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    v-model="form.description_ar"
                                    label="Description (Arabic)"
                                    variant="outlined"
                                    rows="3"
                                    dir="rtl"
                                ></v-textarea>
                            </v-col>
                        </v-row>

                        <v-divider class="my-6"></v-divider>

                        <!-- Pricing Section -->
                        <div class="text-subtitle-2 font-weight-bold mb-3">Pricing</div>
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    v-model.number="form.rate"
                                    label="Your Rate (per 1k)"
                                    type="number"
                                    variant="outlined"
                                    prefix="$"
                                    step="0.0001"
                                    min="0"
                                    :hint="`Original: $${formatNumber(service.original_rate || service.rate, 4)}`"
                                    persistent-hint
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    :model-value="profitMargin"
                                    label="Profit Margin"
                                    variant="outlined"
                                    suffix="%"
                                    readonly
                                    :class="profitMarginClass"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    :model-value="profitPerK"
                                    label="Profit per 1k"
                                    variant="outlined"
                                    prefix="$"
                                    readonly
                                    :class="profitMarginClass"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <!-- Quick Margin Buttons -->
                        <div class="d-flex flex-wrap gap-2 mt-2 mb-4">
                            <v-chip
                                v-for="margin in [10, 20, 30, 50, 100]"
                                :key="margin"
                                variant="outlined"
                                color="primary"
                                @click="applyMargin(margin)"
                            >
                                +{{ margin }}%
                            </v-chip>
                        </div>

                        <v-divider class="my-6"></v-divider>

                        <!-- Quantity Limits -->
                        <div class="text-subtitle-2 font-weight-bold mb-3">Quantity Limits</div>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model.number="form.min"
                                    label="Minimum Quantity"
                                    type="number"
                                    variant="outlined"
                                    min="1"
                                    :hint="`Original: ${service.original_min || service.min}`"
                                    persistent-hint
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model.number="form.max"
                                    label="Maximum Quantity"
                                    type="number"
                                    variant="outlined"
                                    min="1"
                                    :hint="`Original: ${service.original_max || service.max}`"
                                    persistent-hint
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <v-divider class="my-6"></v-divider>

                        <!-- Service Options -->
                        <div class="text-subtitle-2 font-weight-bold mb-3">Options</div>
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-switch
                                    v-model="form.is_active"
                                    label="Active"
                                    color="success"
                                    hide-details
                                ></v-switch>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-switch
                                    v-model="form.refill"
                                    label="Refill Available"
                                    color="info"
                                    hide-details
                                ></v-switch>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-switch
                                    v-model="form.dripfeed"
                                    label="Drip-feed Available"
                                    color="warning"
                                    hide-details
                                ></v-switch>
                            </v-col>
                        </v-row>

                        <v-divider class="my-6"></v-divider>

                        <!-- Category Override -->
                        <div class="text-subtitle-2 font-weight-bold mb-3">Category Override</div>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.category_en"
                                    label="Category (English)"
                                    variant="outlined"
                                    :placeholder="service.category_en"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.category_ar"
                                    label="Category (Arabic)"
                                    variant="outlined"
                                    dir="rtl"
                                    :placeholder="service.category_ar"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <!-- Platform Selection -->
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="form.platform"
                                    :items="platforms"
                                    label="Platform"
                                    variant="outlined"
                                    clearable
                                >
                                    <template v-slot:selection="{ item }">
                                        <v-icon class="mr-2" :color="getPlatformColor(item.value)">
                                            {{ getPlatformIcon(item.value) }}
                                        </v-icon>
                                        {{ item.title }}
                                    </template>
                                    <template v-slot:item="{ item, props }">
                                        <v-list-item v-bind="props">
                                            <template v-slot:prepend>
                                                <v-icon :color="getPlatformColor(item.value)">
                                                    {{ getPlatformIcon(item.value) }}
                                                </v-icon>
                                            </template>
                                        </v-list-item>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model.number="form.sort_order"
                                    label="Sort Order"
                                    type="number"
                                    variant="outlined"
                                    min="0"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <v-divider class="my-6"></v-divider>

                        <!-- Actions -->
                        <div class="d-flex gap-3">
                            <v-btn
                                color="primary"
                                size="large"
                                @click="saveService"
                                :loading="saving"
                            >
                                <v-icon start>mdi-content-save</v-icon>
                                Save Changes
                            </v-btn>
                            <v-btn
                                color="grey"
                                size="large"
                                variant="outlined"
                                @click="resetForm"
                            >
                                <v-icon start>mdi-refresh</v-icon>
                                Reset
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                size="large"
                                variant="outlined"
                                @click="deleteService"
                            >
                                <v-icon start>mdi-delete</v-icon>
                                Delete
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="4">
                <!-- Service Preview -->
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2" size="small">mdi-eye</v-icon>
                        Preview
                    </v-card-title>
                    <v-card-text>
                        <v-card variant="outlined" class="pa-3">
                            <div class="d-flex align-center mb-2">
                                <v-icon
                                    v-if="form.platform"
                                    :color="getPlatformColor(form.platform)"
                                    class="mr-2"
                                >
                                    {{ getPlatformIcon(form.platform) }}
                                </v-icon>
                                <span class="font-weight-bold">
                                    {{ locale === 'ar' ? form.name_ar : form.name_en }}
                                </span>
                            </div>
                            <div class="text-caption text-grey mb-2">
                                {{ locale === 'ar' ? form.category_ar : form.category_en }}
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <v-chip size="small" color="primary" variant="tonal">
                                    ${{ formatNumber(form.rate, 4) }}/1k
                                </v-chip>
                                <v-chip size="small" variant="tonal">
                                    {{ form.min }} - {{ form.max }}
                                </v-chip>
                                <v-chip v-if="form.refill" size="small" color="success" variant="tonal">
                                    Refill
                                </v-chip>
                            </div>
                            <div class="text-caption text-grey">
                                {{ locale === 'ar' ? form.description_ar : form.description_en }}
                            </div>
                        </v-card>
                    </v-card-text>
                </v-card>

                <!-- Service Statistics -->
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2" size="small">mdi-chart-bar</v-icon>
                        Statistics
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <v-list-item-title>Total Orders</v-list-item-title>
                                <template v-slot:append>
                                    <strong>{{ service.orders_count || 0 }}</strong>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-title>Total Revenue</v-list-item-title>
                                <template v-slot:append>
                                    <strong class="text-success">${{ formatNumber(service.total_revenue || 0) }}</strong>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-title>Total Profit</v-list-item-title>
                                <template v-slot:append>
                                    <strong class="text-primary">${{ formatNumber(service.total_profit || 0) }}</strong>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-title>Avg. Order Size</v-list-item-title>
                                <template v-slot:append>
                                    <strong>{{ formatNumber(service.avg_quantity || 0, 0) }}</strong>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>

                <!-- Quick Actions -->
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2" size="small">mdi-lightning-bolt</v-icon>
                        Quick Actions
                    </v-card-title>
                    <v-card-text class="d-flex flex-column gap-2">
                        <v-btn
                            variant="outlined"
                            color="info"
                            block
                            @click="syncFromApi"
                            :loading="syncing"
                        >
                            <v-icon start>mdi-sync</v-icon>
                            Sync from API
                        </v-btn>
                        <v-btn
                            variant="outlined"
                            color="warning"
                            block
                            @click="duplicateService"
                        >
                            <v-icon start>mdi-content-copy</v-icon>
                            Duplicate Service
                        </v-btn>
                        <v-btn
                            variant="outlined"
                            :color="form.is_active ? 'error' : 'success'"
                            block
                            @click="toggleActive"
                        >
                            <v-icon start>{{ form.is_active ? 'mdi-eye-off' : 'mdi-eye' }}</v-icon>
                            {{ form.is_active ? 'Deactivate' : 'Activate' }}
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Not Found -->
        <v-card v-else>
            <v-card-text class="text-center py-10">
                <v-icon size="64" color="grey">mdi-alert-circle-outline</v-icon>
                <div class="text-h6 mt-4">Service not found</div>
                <v-btn color="primary" to="/admin/services" class="mt-4">
                    Back to Services
                </v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '../../stores/app'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const appStore = useAppStore()
const showSnackbar = inject('showSnackbar')

const locale = computed(() => appStore.locale)

const service = ref(null)
const loading = ref(true)
const saving = ref(false)
const syncing = ref(false)

const form = ref({
    name_en: '',
    name_ar: '',
    description_en: '',
    description_ar: '',
    rate: 0,
    min: 100,
    max: 10000,
    is_active: true,
    refill: false,
    dripfeed: false,
    category_en: '',
    category_ar: '',
    platform: null,
    sort_order: 0,
})

const platforms = [
    'Instagram',
    'TikTok',
    'YouTube',
    'Facebook',
    'Twitter',
    'Telegram',
    'Spotify',
    'Threads',
    'LinkedIn',
    'Snapchat',
    'Discord',
    'SoundCloud',
    'Twitch',
    'Other',
]

// Computed
const profitMargin = computed(() => {
    const originalRate = service.value?.original_rate || service.value?.rate || 0
    if (originalRate <= 0) return '0.00'
    const margin = ((form.value.rate - originalRate) / originalRate) * 100
    return margin.toFixed(2)
})

const profitPerK = computed(() => {
    const originalRate = service.value?.original_rate || service.value?.rate || 0
    const profit = form.value.rate - originalRate
    return profit.toFixed(4)
})

const profitMarginClass = computed(() => {
    const margin = parseFloat(profitMargin.value)
    if (margin > 0) return 'text-success'
    if (margin < 0) return 'text-error'
    return ''
})

// Methods
const formatNumber = (num, decimals = 2) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(num || 0)
}

const getPlatformIcon = (platform) => {
    const icons = {
        'Instagram': 'mdi-instagram',
        'TikTok': 'mdi-music-note',
        'YouTube': 'mdi-youtube',
        'Facebook': 'mdi-facebook',
        'Twitter': 'mdi-twitter',
        'Telegram': 'mdi-send',
        'Spotify': 'mdi-spotify',
        'Threads': 'mdi-at',
        'LinkedIn': 'mdi-linkedin',
        'Snapchat': 'mdi-snapchat',
        'Discord': 'mdi-discord',
        'SoundCloud': 'mdi-soundcloud',
        'Twitch': 'mdi-twitch',
    }
    return icons[platform] || 'mdi-web'
}

const getPlatformColor = (platform) => {
    const colors = {
        'Instagram': '#E4405F',
        'TikTok': '#000000',
        'YouTube': '#FF0000',
        'Facebook': '#1877F2',
        'Twitter': '#1DA1F2',
        'Telegram': '#0088CC',
        'Spotify': '#1DB954',
        'Threads': '#000000',
        'LinkedIn': '#0A66C2',
        'Snapchat': '#FFFC00',
        'Discord': '#5865F2',
        'SoundCloud': '#FF5500',
        'Twitch': '#9146FF',
    }
    return colors[platform] || 'grey'
}

const fetchService = async () => {
    loading.value = true
    try {
        const response = await axios.get(`/api/services/${route.params.id}`)
        service.value = response.data.service
        populateForm()
    } catch (error) {
        console.error('Error fetching service:', error)
        showSnackbar?.('Failed to load service', 'error')
    } finally {
        loading.value = false
    }
}

const populateForm = () => {
    if (!service.value) return
    form.value = {
        name_en: service.value.name_en || '',
        name_ar: service.value.name_ar || '',
        description_en: service.value.description_en || '',
        description_ar: service.value.description_ar || '',
        rate: service.value.rate || 0,
        min: service.value.min || 100,
        max: service.value.max || 10000,
        is_active: service.value.is_active ?? true,
        refill: service.value.refill ?? false,
        dripfeed: service.value.dripfeed ?? false,
        category_en: service.value.category_en || '',
        category_ar: service.value.category_ar || '',
        platform: service.value.platform || null,
        sort_order: service.value.sort_order || 0,
    }
}

const resetForm = () => {
    populateForm()
    showSnackbar?.('Form reset to original values', 'info')
}

const applyMargin = (percentage) => {
    const originalRate = service.value?.original_rate || service.value?.rate || 0
    form.value.rate = parseFloat((originalRate * (1 + percentage / 100)).toFixed(4))
}

const saveService = async () => {
    saving.value = true
    try {
        await axios.put(`/api/services/${route.params.id}`, form.value)
        showSnackbar?.('Service updated successfully', 'success')
        fetchService() // Refresh data
    } catch (error) {
        console.error('Error saving service:', error)
        showSnackbar?.(error.response?.data?.message || 'Failed to save service', 'error')
    } finally {
        saving.value = false
    }
}

const deleteService = async () => {
    if (!confirm('Are you sure you want to delete this service? This cannot be undone.')) return

    try {
        await axios.delete(`/api/services/${route.params.id}`)
        showSnackbar?.('Service deleted successfully', 'success')
        router.push('/admin/services')
    } catch (error) {
        console.error('Error deleting service:', error)
        showSnackbar?.('Failed to delete service', 'error')
    }
}

const syncFromApi = async () => {
    syncing.value = true
    try {
        await axios.post(`/api/services/${route.params.id}/sync`)
        showSnackbar?.('Service synced from API', 'success')
        fetchService()
    } catch (error) {
        console.error('Error syncing service:', error)
        showSnackbar?.('Failed to sync service', 'error')
    } finally {
        syncing.value = false
    }
}

const duplicateService = async () => {
    try {
        const response = await axios.post(`/api/services/${route.params.id}/duplicate`)
        showSnackbar?.('Service duplicated successfully', 'success')
        if (response.data.service?.id) {
            router.push(`/admin/services/${response.data.service.id}/edit`)
        }
    } catch (error) {
        console.error('Error duplicating service:', error)
        showSnackbar?.('Failed to duplicate service', 'error')
    }
}

const toggleActive = () => {
    form.value.is_active = !form.value.is_active
}

onMounted(() => {
    fetchService()
})
</script>
