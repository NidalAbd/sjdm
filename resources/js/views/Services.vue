<template>
    <div>
        <!-- Header -->
        <section class="section" style="padding-bottom:0;">
            <v-container>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
                    <div>
                        <h1 class="heading-lg">{{ $t('servicesPage.allSmmServices') }}</h1>
                        <p class="text-muted" style="margin-top:4px;">{{ pagination.total || 0 }} {{ $t('servicesPage.servicesFound') }}</p>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <v-btn :variant="viewMode === 'grid' ? 'flat' : 'outlined'" color="primary" icon size="small" @click="viewMode = 'grid'"><v-icon size="18">mdi-view-grid</v-icon></v-btn>
                        <v-btn :variant="viewMode === 'list' ? 'flat' : 'outlined'" color="primary" icon size="small" @click="viewMode = 'list'"><v-icon size="18">mdi-view-list</v-icon></v-btn>
                    </div>
                </div>
            </v-container>
        </section>

        <v-container class="py-6">
            <!-- Filters -->
            <div class="card" style="padding:16px;margin-bottom:24px;">
                <v-row dense>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="filters.search" :label="$t('servicesPage.searchPlaceholder')" prepend-inner-icon="mdi-magnify" clearable @keyup.enter="fetchServices" @click:clear="filters.search = ''; fetchServices()"></v-text-field>
                    </v-col>
                    <v-col cols="6" md="2">
                        <v-select v-model="filters.platform" :items="platformOptions" :label="$t('servicesPage.platform')" prepend-inner-icon="mdi-apps" clearable @update:model-value="onPlatformChange"></v-select>
                    </v-col>
                    <v-col cols="6" md="2">
                        <v-select v-model="filters.category" :items="categoryOptions" :label="$t('servicesPage.category')" prepend-inner-icon="mdi-tag" clearable @update:model-value="fetchServices"></v-select>
                    </v-col>
                    <v-col cols="6" md="2">
                        <v-select v-model="filters.sortBy" :items="sortOptions" :label="$t('servicesPage.sortBy')" @update:model-value="fetchServices"></v-select>
                    </v-col>
                    <v-col cols="6" md="2">
                        <v-select v-model="filters.perPage" :items="perPageOptions" :label="$t('servicesPage.itemsPerPage')" @update:model-value="fetchServices"></v-select>
                    </v-col>
                </v-row>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="services-grid">
                <v-skeleton-loader v-for="n in 6" :key="n" type="card" height="180"></v-skeleton-loader>
            </div>

            <!-- Grid View -->
            <div v-else-if="services.length && viewMode === 'grid'" class="services-grid">
                <router-link v-for="svc in services" :key="svc.service_id" :to="`/service/${svc.service_id}`" class="svc">
                    <div class="svc-top">
                        <span class="svc-id">#{{ svc.service_id }}</span>
                        <span class="svc-price">${{ formatPrice(svc.rate) }}<small>/1K</small></span>
                    </div>
                    <div class="svc-name">{{ svc.name || svc.name_en }}</div>
                    <div class="svc-cat"><v-icon size="12">mdi-tag</v-icon> {{ svc.category || svc.category_en }}</div>
                    <div class="svc-bottom">
                        <span class="svc-range">{{ fmtNum(svc.min) }} - {{ fmtNum(svc.max) }}</span>
                        <div class="svc-badges">
                            <span v-if="svc.refill" class="svc-badge svc-badge-ok">Refill</span>
                            <span v-if="svc.cancel" class="svc-badge svc-badge-warn">Cancel</span>
                        </div>
                    </div>
                </router-link>
            </div>

            <!-- List View -->
            <div v-else-if="services.length && viewMode === 'list'">
                <div class="card" style="padding:0;overflow:hidden;">
                    <v-table hover>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ $t('servicesPage.service') }}</th>
                                <th>{{ $t('servicesPage.category') }}</th>
                                <th style="text-align:right;">{{ $t('servicesPage.price') }}</th>
                                <th style="text-align:right;">Min</th>
                                <th style="text-align:right;">Max</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="svc in services" :key="svc.service_id" style="cursor:pointer;" @click="$router.push(`/service/${svc.service_id}`)">
                                <td style="opacity:0.4;">#{{ svc.service_id }}</td>
                                <td style="font-weight:600;max-width:300px;">
                                    <div class="line-clamp-2">{{ svc.name || svc.name_en }}</div>
                                </td>
                                <td style="font-size:0.82rem;color:rgb(var(--v-theme-primary));">{{ svc.category || svc.category_en }}</td>
                                <td style="text-align:right;font-weight:700;color:rgb(var(--v-theme-success));">${{ formatPrice(svc.rate) }}</td>
                                <td style="text-align:right;opacity:0.5;">{{ fmtNum(svc.min) }}</td>
                                <td style="text-align:right;opacity:0.5;">{{ fmtNum(svc.max) }}</td>
                            </tr>
                        </tbody>
                    </v-table>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="card" style="text-align:center;padding:60px 24px;">
                <v-icon size="48" style="opacity:0.2;">mdi-magnify</v-icon>
                <h3 class="heading-md" style="margin-top:12px;">{{ $t('servicesPage.noServices') }}</h3>
                <p class="text-muted">{{ $t('servicesPage.tryDifferent') }}</p>
                <v-btn color="primary" variant="outlined" @click="clearFilters" class="mt-4">{{ $t('servicesPage.clearFilters') }}</v-btn>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" style="display:flex;justify-content:center;margin-top:32px;">
                <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="5" rounded @update:model-value="changePage"></v-pagination>
            </div>
        </v-container>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '../stores/app'
import axios from 'axios'

const { t, locale } = useI18n()
const store = useAppStore()
const route = useRoute()
const router = useRouter()

const services = ref([])
const loading = ref(false)
const viewMode = ref(localStorage.getItem('svc-view') || 'grid')
const currentPage = ref(1)
const pagination = ref({ total: 0, last_page: 1 })
const platformOptions = ref([])
const categoryOptions = ref([])

const filters = ref({
    search: route.query.search || '',
    platform: route.query.platform || null,
    category: route.query.category || null,
    sortBy: route.query.sort_by || 'service_id',
    sortOrder: route.query.sort_order || 'asc',
    perPage: Number(route.query.per_page) || 50,
})

const sortOptions = computed(() => [
    { title: t('servicesPage.serviceId'), value: 'service_id' },
    { title: t('servicesPage.price'), value: 'rate' },
    { title: t('servicesPage.minOrder'), value: 'min' },
    { title: t('servicesPage.maxOrder'), value: 'max' },
])

const perPageOptions = [
    { title: '25', value: 25 },
    { title: '50', value: 50 },
    { title: '100', value: 100 },
]

watch(viewMode, (v) => localStorage.setItem('svc-view', v))

const fmtNum = (n) => new Intl.NumberFormat().format(n)
const formatPrice = (r) => { const n = Number(r); if (n < 0.001) return n.toFixed(7); if (n < 0.01) return n.toFixed(5); if (n < 1) return n.toFixed(4); return n.toFixed(2) }

const fetchServices = async () => {
    loading.value = true
    try {
        const params = {
            page: currentPage.value,
            per_page: filters.value.perPage,
            sort_by: filters.value.sortBy,
            sort_order: filters.value.sortOrder,
            lang: store.locale,
        }
        if (filters.value.search) params.search = filters.value.search
        if (filters.value.platform) params.platform = filters.value.platform
        if (filters.value.category) params.category = filters.value.category

        const res = await axios.get('/api/services', { params })
        services.value = res.data.services || []
        pagination.value = res.data.pagination || { total: 0, last_page: 1 }
    } catch (e) { console.error(e) }
    finally { loading.value = false }
}

const fetchPlatforms = async () => {
    try {
        const res = await axios.get('/api/platforms', { params: { lang: store.locale } })
        platformOptions.value = (res.data.platforms || []).map(p => ({ title: p.name, value: p.key }))
    } catch (e) {}
}

const fetchCategories = async () => {
    try {
        const params = { lang: store.locale }
        if (filters.value.platform) params.platform = filters.value.platform
        const res = await axios.get('/api/categories', { params })
        categoryOptions.value = (res.data.categories || []).map(c => ({ title: c.name, value: c.name }))
    } catch (e) {}
}

const onPlatformChange = () => { fetchCategories(); fetchServices() }
const changePage = (p) => { currentPage.value = p; fetchServices(); window.scrollTo(0, 0) }
const clearFilters = () => { filters.value = { search: '', platform: null, category: null, sortBy: 'service_id', sortOrder: 'asc', perPage: 50 }; currentPage.value = 1; fetchServices() }

onMounted(() => {
    if (route.query.platform) filters.value.platform = route.query.platform
    if (route.query.page) currentPage.value = Number(route.query.page)
    fetchServices()
    fetchPlatforms()
    fetchCategories()
})
</script>
