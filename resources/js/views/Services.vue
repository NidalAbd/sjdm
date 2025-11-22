<template>
    <v-container class="py-12">
        <!-- Header -->
        <div class="text-center mb-8">
            <v-chip color="primary" variant="tonal" class="mb-4">All Services</v-chip>
            <h1 class="text-h3 font-weight-bold mb-4">Our SMM Services</h1>
            <p class="text-h6 text-medium-emphasis">Choose from {{ store.services.length }}+ services</p>
        </div>

        <!-- Filters -->
        <v-card class="mb-8 pa-4">
            <v-row align="center">
                <v-col cols="12" md="4">
                    <v-text-field
                        v-model="search"
                        label="Search services..."
                        prepend-inner-icon="mdi-magnify"
                        clearable
                        hide-details
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <v-select
                        v-model="selectedCategory"
                        label="Category"
                        :items="categories"
                        prepend-inner-icon="mdi-filter"
                        clearable
                        hide-details
                    ></v-select>
                </v-col>
                <v-col cols="12" md="4">
                    <v-select
                        v-model="sortBy"
                        label="Sort by"
                        :items="sortOptions"
                        prepend-inner-icon="mdi-sort"
                        hide-details
                    ></v-select>
                </v-col>
            </v-row>
        </v-card>

        <!-- Services by Category -->
        <div v-if="!store.loading">
            <div v-for="(services, category) in filteredGroupedServices" :key="category" class="mb-8">
                <div class="d-flex align-center mb-4">
                    <v-icon color="primary" class="mr-2">mdi-folder</v-icon>
                    <h2 class="text-h5 font-weight-bold">{{ category }}</h2>
                    <v-chip size="small" class="ml-2">{{ services.length }}</v-chip>
                </div>

                <v-row>
                    <v-col v-for="service in services" :key="service.service_id" cols="12" sm="6" lg="4">
                        <v-card class="service-card h-100" hover :to="`/service/${service.service_id}`">
                            <v-card-text class="pa-5">
                                <div class="d-flex justify-space-between align-start mb-3">
                                    <v-chip size="x-small" color="secondary" variant="tonal">
                                        ID: {{ service.service_id }}
                                    </v-chip>
                                    <v-chip size="small" color="success" variant="flat">
                                        ${{ service.rate }}/1K
                                    </v-chip>
                                </div>

                                <h3 class="text-subtitle-1 font-weight-bold mb-3 service-name">
                                    {{ store.locale === 'ar' ? service.name_ar : service.name_en }}
                                </h3>

                                <v-divider class="mb-3"></v-divider>

                                <div class="d-flex justify-space-between text-caption">
                                    <div>
                                        <span class="text-medium-emphasis">Min:</span>
                                        <span class="font-weight-medium ml-1">{{ formatNumber(service.min) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-medium-emphasis">Max:</span>
                                        <span class="font-weight-medium ml-1">{{ formatNumber(service.max) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex ga-2 mt-3">
                                    <v-chip v-if="service.refill" size="x-small" color="info" variant="tonal">
                                        <v-icon start size="small">mdi-refresh</v-icon>
                                        Refill
                                    </v-chip>
                                    <v-chip v-if="service.cancel" size="x-small" color="warning" variant="tonal">
                                        <v-icon start size="small">mdi-cancel</v-icon>
                                        Cancel
                                    </v-chip>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </div>

            <v-alert v-if="Object.keys(filteredGroupedServices).length === 0" type="info" variant="tonal">
                No services found matching your criteria.
            </v-alert>
        </div>

        <!-- Loading -->
        <v-row v-else>
            <v-col v-for="n in 9" :key="n" cols="12" sm="6" lg="4">
                <v-skeleton-loader type="card" height="180"></v-skeleton-loader>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAppStore } from '../stores/app'
import { useRoute } from 'vue-router'

const store = useAppStore()
const route = useRoute()

const search = ref('')
const selectedCategory = ref(null)
const sortBy = ref('name')

const sortOptions = [
    { title: 'Name (A-Z)', value: 'name' },
    { title: 'Price (Low to High)', value: 'price_asc' },
    { title: 'Price (High to Low)', value: 'price_desc' },
    { title: 'Min Order', value: 'min' },
]

const categories = computed(() => {
    const cats = [...new Set(store.services.map(s =>
        store.locale === 'ar' ? s.category_ar : s.category_en
    ))]
    return cats.filter(c => c)
})

const filteredServices = computed(() => {
    let services = [...store.services]

    // Search filter
    if (search.value) {
        const searchLower = search.value.toLowerCase()
        services = services.filter(s =>
            s.name_en?.toLowerCase().includes(searchLower) ||
            s.name_ar?.toLowerCase().includes(searchLower) ||
            s.service_id.toString().includes(searchLower)
        )
    }

    // Category filter
    if (selectedCategory.value) {
        services = services.filter(s =>
            (store.locale === 'ar' ? s.category_ar : s.category_en) === selectedCategory.value
        )
    }

    // Sort
    switch (sortBy.value) {
        case 'price_asc':
            services.sort((a, b) => parseFloat(a.rate) - parseFloat(b.rate))
            break
        case 'price_desc':
            services.sort((a, b) => parseFloat(b.rate) - parseFloat(a.rate))
            break
        case 'min':
            services.sort((a, b) => parseInt(a.min) - parseInt(b.min))
            break
        default:
            services.sort((a, b) => (a.name_en || '').localeCompare(b.name_en || ''))
    }

    return services
})

const filteredGroupedServices = computed(() => {
    const grouped = {}
    filteredServices.value.forEach(service => {
        const category = store.locale === 'ar' ? service.category_ar : service.category_en
        if (!grouped[category]) {
            grouped[category] = []
        }
        grouped[category].push(service)
    })
    return grouped
})

const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(0) + 'K'
    return num?.toString() || '0'
}

onMounted(() => {
    // Check for platform filter from URL
    if (route.query.platform) {
        // Could filter by platform if needed
    }
})
</script>

<style scoped>
.service-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(99, 102, 241, 0.1);
}

.service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
    border-color: rgba(99, 102, 241, 0.3);
}

.service-name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 48px;
}
</style>
