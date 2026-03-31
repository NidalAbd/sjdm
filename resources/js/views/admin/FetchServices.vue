<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center flex-wrap ga-2">
                <v-icon class="mr-2">mdi-sync</v-icon>
                <span>Fetch Services ({{ language === 'ar' ? 'Arabic' : 'English' }})</span>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    @click="fetchServices"
                    :loading="loading"
                    prepend-icon="mdi-cloud-download"
                    size="large"
                >
                    Fetch from API
                </v-btn>
            </v-card-title>
        </v-card>

        <!-- Info Card -->
        <v-card class="mb-4">
            <v-card-text>
                <v-alert type="info" variant="tonal" class="mb-4">
                    <div class="font-weight-bold">About Fetch Services</div>
                    <div class="mt-2">
                        This will fetch all available services from the external API and update your database.
                        <ul class="mt-2">
                            <li>New services will be created with a default 30% markup</li>
                            <li>Existing services will have their API rate, min, and max values updated</li>
                            <li>Language: <strong>{{ language === 'ar' ? 'Arabic' : 'English' }}</strong></li>
                        </ul>
                    </div>
                </v-alert>

                <v-row v-if="stats.total > 0">
                    <v-col cols="6" sm="3">
                        <v-card color="info" variant="flat">
                            <v-card-text class="text-white text-center">
                                <div class="text-h4 font-weight-bold">{{ stats.total }}</div>
                                <div class="text-caption">Total Services</div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="6" sm="3">
                        <v-card color="success" variant="flat">
                            <v-card-text class="text-white text-center">
                                <div class="text-h4 font-weight-bold">{{ stats.active }}</div>
                                <div class="text-caption">Active</div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="6" sm="3">
                        <v-card color="warning" variant="flat">
                            <v-card-text class="text-white text-center">
                                <div class="text-h4 font-weight-bold">{{ stats.inactive }}</div>
                                <div class="text-caption">Inactive</div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="6" sm="3">
                        <v-card color="purple" variant="flat">
                            <v-card-text class="text-white text-center">
                                <div class="text-h4 font-weight-bold">{{ stats.categories }}</div>
                                <div class="text-caption">Categories</div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Result Card -->
        <v-card v-if="result">
            <v-card-title class="d-flex align-center">
                <v-icon class="mr-2" :color="result.success ? 'success' : 'error'">
                    {{ result.success ? 'mdi-check-circle' : 'mdi-alert-circle' }}
                </v-icon>
                Fetch Result
            </v-card-title>
            <v-card-text>
                <v-alert :type="result.success ? 'success' : 'error'" variant="tonal">
                    <div class="font-weight-bold">{{ result.message }}</div>
                    <div v-if="result.success" class="mt-2">
                        <v-chip color="success" class="mr-2">
                            <v-icon start>mdi-plus</v-icon>
                            {{ result.created }} Created
                        </v-chip>
                        <v-chip color="info">
                            <v-icon start>mdi-refresh</v-icon>
                            {{ result.updated }} Updated
                        </v-chip>
                    </div>
                </v-alert>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" :to="'/admin/services'">
                    <v-icon start>mdi-arrow-right</v-icon>
                    View All Services
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const showSnackbar = inject('showSnackbar')

// Get language from route meta
const language = computed(() => route.meta.language || 'en')

// Data
const loading = ref(false)
const result = ref(null)
const stats = ref({
    total: 0,
    active: 0,
    inactive: 0,
    categories: 0,
})

// Methods
const fetchStats = async () => {
    try {
        const response = await axios.get('/api/admin/services', { params: { per_page: 1 } })
        if (response.data.stats) {
            stats.value = response.data.stats
        }
    } catch (error) {
        console.error('Error fetching stats:', error)
    }
}

const fetchServices = async () => {
    if (!confirm(`Are you sure you want to fetch services in ${language.value === 'ar' ? 'Arabic' : 'English'}? This will update your database.`)) {
        return
    }

    loading.value = true
    result.value = null

    try {
        const response = await axios.post('/api/admin/services/fetch', {
            language: language.value
        })

        result.value = {
            success: true,
            message: response.data.message,
            created: response.data.created || 0,
            updated: response.data.updated || 0,
        }

        showSnackbar?.('Services fetched successfully!', 'success')

        // Refresh stats
        await fetchStats()
    } catch (error) {
        console.error('Error fetching services:', error)
        result.value = {
            success: false,
            message: error.response?.data?.message || 'Failed to fetch services from API',
        }
        showSnackbar?.('Failed to fetch services', 'error')
    } finally {
        loading.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchStats()
})
</script>
