<template>
  <div>
    <!-- Hero Section -->
    <section class="hero" :style="{ background: store.gradientStyle }">
      <v-container>
        <v-row align="center">
          <v-col cols="12" lg="8">
            <span class="hero-badge">
              <v-icon size="16">mdi-storefront</v-icon>
              {{ $t('servicesPage.allSmmServices') }}
            </span>
            <h1 class="hero-title">{{ $t('servicesPage.allSmmServices') }}</h1>
            <p class="hero-desc">{{ $t('servicesPage.discoverServices') }}</p>
            <div v-if="hasFilters" class="hero-actions">
              <v-chip color="white" variant="flat" class="px-4 py-2">
                <v-icon start>mdi-filter</v-icon>
                {{ pagination.total }} {{ $t('servicesPage.servicesFound') }}
              </v-chip>
            </div>
          </v-col>
          <v-col cols="12" lg="4" class="text-center">
            <div class="card" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.25); text-align: center; padding: 2rem;">
              <div style="font-size: 2.5rem; font-weight: 800; color: #fff;">{{ pagination.total || 0 }}</div>
              <div style="color: rgba(255,255,255,0.85); font-size: 0.875rem;">{{ $t('servicesPage.totalServices') }}</div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <v-container class="py-8">
      <!-- Filters Section -->
      <div class="card filter-bar">
        <v-row>
          <!-- Search -->
          <v-col cols="12" md="6" lg="4">
            <v-text-field
              v-model="filters.search"
              :label="$t('servicesPage.searchPlaceholder')"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="comfortable"
              clearable
              hide-details
              @update:model-value="debouncedSearch"
            />
          </v-col>

          <!-- Platform Filter -->
          <v-col cols="12" md="6" lg="4">
            <v-select
              v-model="filters.platform"
              :items="platformOptions"
              item-title="title"
              item-value="value"
              :label="$t('servicesPage.platform')"
              prepend-inner-icon="mdi-apps"
              variant="outlined"
              density="comfortable"
              hide-details
              clearable
              :loading="platformsLoading"
              :disabled="platformOptions.length === 0"
              @update:model-value="onPlatformChange"
            >
              <template v-slot:no-data>
                <v-list-item>
                  <v-list-item-title>{{ $t('servicesPage.noPlatforms') }}</v-list-item-title>
                </v-list-item>
              </template>
            </v-select>
          </v-col>

          <!-- Category Filter -->
          <v-col cols="12" md="6" lg="4">
            <v-select
              v-model="filters.category"
              :items="categoryOptions"
              item-title="title"
              item-value="value"
              :label="$t('servicesPage.category')"
              prepend-inner-icon="mdi-tag"
              variant="outlined"
              density="comfortable"
              hide-details
              clearable
              :loading="categoriesLoading"
              @update:model-value="fetchServices"
            >
              <template v-slot:no-data>
                <v-list-item>
                  <v-list-item-title>{{ $t('servicesPage.noCategories') }}</v-list-item-title>
                </v-list-item>
              </template>
            </v-select>
          </v-col>

          <!-- Sort By -->
          <v-col cols="12" md="6" lg="4">
            <v-select
              v-model="filters.sortBy"
              :items="sortOptions"
              :label="$t('servicesPage.sortBy')"
              prepend-inner-icon="mdi-sort"
              variant="outlined"
              density="comfortable"
              hide-details
              @update:model-value="fetchServices"
            />
          </v-col>

          <!-- Sort Order -->
          <v-col cols="12" md="6" lg="4">
            <v-select
              v-model="filters.sortOrder"
              :items="sortOrderOptions"
              :label="$t('servicesPage.order')"
              prepend-inner-icon="mdi-swap-vertical"
              variant="outlined"
              density="comfortable"
              hide-details
              @update:model-value="fetchServices"
            />
          </v-col>

          <!-- Per Page -->
          <v-col cols="12" md="6" lg="4">
            <v-select
              v-model="filters.perPage"
              :items="perPageOptions"
              :label="$t('servicesPage.itemsPerPage')"
              prepend-inner-icon="mdi-view-list"
              variant="outlined"
              density="comfortable"
              hide-details
              @update:model-value="fetchServices"
            />
          </v-col>
        </v-row>

        <div class="d-flex flex-wrap gap-3 mt-4">
          <v-btn color="primary" size="default" @click="fetchServices" :loading="loading">
            <v-icon start>mdi-filter</v-icon>
            {{ $t('servicesPage.applyFilters') }}
          </v-btn>
          <v-btn variant="outlined" size="default" @click="clearFilters">
            <v-icon start>mdi-close</v-icon>
            {{ $t('servicesPage.clearFilters') }}
          </v-btn>
        </div>
      </div>

      <!-- Featured Services -->
      <div v-if="featured.length > 0 && !hasFilters" class="mb-8">
        <div class="section-header">
          <h2 class="heading-lg">
            <v-icon color="amber" class="mr-2">mdi-star</v-icon>
            {{ $t('servicesPage.featuredServices') }}
          </h2>
        </div>
        <div class="services-grid">
          <router-link
            v-for="service in featured"
            :key="service.service_id"
            :to="`/service/${service.service_id}`"
            class="svc"
            :style="{ background: store.gradientStyle, color: '#fff' }"
          >
            <div class="svc-top">
              <span class="svc-id">#{{ service.service_id }}</span>
              <span class="svc-price">${{ formatPrice(service.rate) }}<small>/1K</small></span>
            </div>
            <div class="svc-name" style="color: #fff;">{{ service.name || service.name_en }}</div>
            <div class="svc-bottom">
              <span class="svc-range">{{ formatNumber(service.min) }} - {{ formatNumber(service.max) }}</span>
            </div>
            <div style="text-align: center; margin-top: 0.5rem;">
              <v-btn color="white" variant="flat" size="small">
                {{ $t('servicesPage.viewDetails') }}
              </v-btn>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Services Section Header -->
      <div class="d-flex flex-wrap justify-space-between align-center mb-6">
        <div>
          <h2 class="heading-lg">
            <template v-if="filters.search">{{ $t('servicesPage.searchResults') }}</template>
            <template v-else-if="filters.platform">{{ filters.platform }} {{ $t('publicNav.services') }}</template>
            <template v-else-if="filters.category">{{ filters.category }}</template>
            <template v-else>{{ $t('servicesPage.allServices') }}</template>
          </h2>
          <p class="text-muted">
            {{ pagination.total }} {{ $t('servicesPage.servicesAvailable') }}
          </p>
        </div>
        <v-btn-toggle v-model="viewMode" mandatory variant="outlined" divided>
          <v-btn value="grid" icon="mdi-view-grid" />
          <v-btn value="list" icon="mdi-view-list" />
        </v-btn-toggle>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <v-skeleton-loader type="card" class="mb-4" />
        <v-skeleton-loader type="card" class="mb-4" />
        <p class="text-muted mt-4">{{ $t('servicesPage.loadingServices') }}</p>
      </div>

      <!-- Grid View -->
      <div v-else-if="viewMode === 'grid' && services.length > 0" class="services-grid">
        <router-link
          v-for="service in services"
          :key="service.service_id"
          :to="`/service/${service.service_id}`"
          class="svc"
        >
          <div class="svc-top">
            <span class="svc-id">#{{ service.service_id }}</span>
            <span class="svc-price">${{ formatPrice(service.rate) }}<small>/1K</small></span>
          </div>
          <div class="svc-name">{{ service.name || service.name_en }}</div>
          <div class="svc-cat">
            <v-icon size="12">mdi-tag</v-icon>
            {{ service.category || service.category_en }}
          </div>
          <div class="svc-bottom">
            <span class="svc-range">{{ formatNumber(service.min) }} - {{ formatNumber(service.max) }}</span>
            <div class="svc-badges">
              <span v-if="service.refill" class="svc-badge svc-badge-ok">{{ $t('services.refill') }}</span>
              <span v-if="service.cancel" class="svc-badge svc-badge-warn">{{ $t('common.cancel') }}</span>
            </div>
          </div>
        </router-link>
      </div>

      <!-- List View -->
      <div v-else-if="viewMode === 'list' && services.length > 0" class="card" style="overflow-x: auto;">
        <v-table hover>
          <thead>
            <tr>
              <th>{{ $t('servicesPage.service') }}</th>
              <th>{{ $t('servicesPage.category') }}</th>
              <th>{{ $t('servicesPage.price') }}</th>
              <th>{{ $t('servicesPage.minMax') }}</th>
              <th>{{ $t('servicesPage.features') }}</th>
              <th>{{ $t('common.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in services" :key="service.service_id" style="cursor: pointer" @click="goToService(service.service_id)">
              <td>
                <div class="font-weight-bold">{{ service.name || service.name_en }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">#{{ service.service_id }}</div>
              </td>
              <td>
                <v-chip size="small" variant="tonal">
                  {{ service.category || service.category_en }}
                </v-chip>
              </td>
              <td>
                <span class="font-weight-bold" style="color: var(--color-success);">${{ formatPrice(service.rate) }}</span>
                <span class="text-muted" style="font-size: 0.75rem;">/ 1K</span>
              </td>
              <td style="font-size: 0.75rem;">
                {{ formatNumber(service.min) }} - {{ formatNumber(service.max) }}
              </td>
              <td>
                <v-chip v-if="service.refill" color="success" size="x-small" class="mr-1">{{ $t('services.refill') }}</v-chip>
                <v-chip v-if="service.cancel" color="warning" size="x-small">{{ $t('common.cancel') }}</v-chip>
              </td>
              <td>
                <v-btn color="primary" size="small" variant="flat">{{ $t('servicesPage.order') }}</v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </div>

      <!-- No Results -->
      <div v-else-if="!loading && services.length === 0" class="card text-center" style="padding: 4rem 2rem;">
        <v-icon size="80" color="grey" class="mb-4">mdi-magnify</v-icon>
        <h3 class="heading-md mb-2">{{ $t('servicesPage.noServices') }}</h3>
        <p class="text-muted mb-6">
          {{ hasFilters ? $t('servicesPage.tryDifferent') : $t('servicesPage.noServicesAvailable') }}
        </p>
        <v-btn v-if="hasFilters" color="primary" @click="clearFilters">
          {{ $t('servicesPage.viewAllServices') }}
        </v-btn>
      </div>

      <!-- Pagination -->
      <div v-if="services.length > 0 && pagination.last_page > 1" class="d-flex justify-center mt-8">
        <v-pagination
          v-model="currentPage"
          :length="pagination.last_page"
          :total-visible="7"
          rounded="circle"
          @update:model-value="changePage"
        />
      </div>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAppStore } from '../stores/app'
import { useI18n } from 'vue-i18n'
import { useSeo, seoConfigs, seoConfigsAr } from '../composables/useSeo'

const { t, locale: i18nLocale } = useI18n()
const router = useRouter()
const route = useRoute()
const store = useAppStore()

// SEO Configuration
const seoConfig = computed(() => {
    const config = i18nLocale.value === 'ar' ? { ...seoConfigs.services, ...seoConfigsAr.services } : seoConfigs.services
    return config
})
useSeo(seoConfig.value)

const services = ref([])
const featured = ref([])
const platforms = ref([])
const categories = ref([])
const loading = ref(false)
const platformsLoading = ref(false)
const categoriesLoading = ref(false)
const viewMode = ref('grid')
const currentPage = ref(1)

const pagination = ref({
  total: 0,
  per_page: 50,
  current_page: 1,
  last_page: 1,
})

const filters = ref({
  search: '',
  platform: null,
  category: null,
  sortBy: 'service_id',
  sortOrder: 'asc',
  perPage: 50,
})

const locale = computed(() => store.locale)

const hasFilters = computed(() => {
  return filters.value.search || filters.value.platform || filters.value.category
})

const platformOptions = computed(() => {
  if (!platforms.value || !Array.isArray(platforms.value) || platforms.value.length === 0) return []
  return platforms.value.map(p => ({
    title: `${p.name} (${p.count})`,
    value: p.key,
  }))
})

const categoryOptions = computed(() => {
  if (!categories.value || !Array.isArray(categories.value) || categories.value.length === 0) return []
  return categories.value
    .filter(c => c && c.name)
    .map(c => ({
      title: `${c.name} (${c.count})`,
      value: c.name,
    }))
})

const sortOptions = computed(() => [
  { title: t('servicesPage.serviceId'), value: 'service_id' },
  { title: t('servicesPage.price'), value: 'rate' },
  { title: t('servicesPage.minOrder'), value: 'min' },
  { title: t('servicesPage.maxOrder'), value: 'max' },
])

const sortOrderOptions = computed(() => [
  { title: t('servicesPage.ascending'), value: 'asc' },
  { title: t('servicesPage.descending'), value: 'desc' },
])

const perPageOptions = [
  { title: '25', value: 25 },
  { title: '50', value: 50 },
  { title: '100', value: 100 },
]

const formatPrice = (r) => {
  const n = Number(r)
  if (n < 0.001) return n.toFixed(7)
  if (n < 0.01) return n.toFixed(5)
  if (n < 1) return n.toFixed(4)
  return n.toFixed(2)
}

let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    if (filters.value.search.length >= 3 || filters.value.search.length === 0) {
      fetchServices()
    }
  }, 500)
}

const fetchServices = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      lang: locale.value,
      page: currentPage.value,
      per_page: filters.value.perPage,
      sort_by: filters.value.sortBy,
      sort_order: filters.value.sortOrder,
    })

    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.platform) params.append('platform', filters.value.platform)
    if (filters.value.category) params.append('category', filters.value.category)

    const response = await fetch(`/api/services?${params}`)
    if (!response.ok) {
      console.error('Services API error:', response.status)
      services.value = []
      return
    }
    const data = await response.json()

    services.value = Array.isArray(data.services) ? data.services : []
    pagination.value = data.pagination || {
      total: 0,
      per_page: 50,
      current_page: 1,
      last_page: 1,
    }
  } catch (error) {
    console.error('Error fetching services:', error)
    services.value = []
  } finally {
    loading.value = false
  }
}

const fetchPlatforms = async () => {
  platformsLoading.value = true
  try {
    const response = await fetch(`/api/platforms?lang=${locale.value}`)
    if (!response.ok) {
      console.error('Platforms API error:', response.status)
      platforms.value = []
      return
    }
    const data = await response.json()
    const platformsData = data.platforms
    platforms.value = Array.isArray(platformsData) ? platformsData : []
    console.log('Platforms loaded:', platforms.value.length, 'items')
  } catch (error) {
    console.error('Error fetching platforms:', error)
    platforms.value = []
  } finally {
    platformsLoading.value = false
  }
}

const fetchCategories = async (platform = null) => {
  categoriesLoading.value = true
  try {
    let url = `/api/categories?lang=${locale.value}`
    if (platform) {
      url += `&platform=${platform}`
    }
    const response = await fetch(url)
    if (!response.ok) {
      console.error('Categories API error:', response.status)
      categories.value = []
      return
    }
    const data = await response.json()
    const categoriesData = data.categories
    categories.value = Array.isArray(categoriesData) ? categoriesData : []
    console.log('Categories loaded:', categories.value.length, 'items')
  } catch (error) {
    console.error('Error fetching categories:', error)
    categories.value = []
  } finally {
    categoriesLoading.value = false
  }
}

const fetchFeatured = async () => {
  try {
    const response = await fetch(`/api/featured?lang=${locale.value}`)
    const data = await response.json()
    featured.value = data.featured || []
  } catch (error) {
    console.error('Error fetching featured:', error)
  }
}

const onPlatformChange = (platform) => {
  filters.value.category = null
  router.replace({
    query: platform ? { platform } : {}
  })
  fetchCategories(platform)
  fetchServices()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    platform: null,
    category: null,
    sortBy: 'service_id',
    sortOrder: 'asc',
    perPage: 50,
  }
  currentPage.value = 1
  router.replace({ query: {} })
  fetchCategories()
  fetchServices()
}

const changePage = (page) => {
  currentPage.value = page
  fetchServices()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const goToService = (id) => {
  router.push(`/service/${id}`)
}

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num)
}

// Restore view preference and apply URL query parameters
onMounted(async () => {
  const savedView = localStorage.getItem('servicesView')
  if (savedView) viewMode.value = savedView

  await fetchPlatforms()

  const platformFromUrl = route.query.platform
  if (platformFromUrl) {
    filters.value.platform = platformFromUrl
    await fetchCategories(platformFromUrl)
  } else {
    await fetchCategories()
  }

  fetchServices()
  fetchFeatured()
})

watch(viewMode, (newVal) => {
  localStorage.setItem('servicesView', newVal)
})

watch(() => route.query.platform, (newPlatform) => {
  if (newPlatform !== filters.value.platform) {
    filters.value.platform = newPlatform || null
    filters.value.category = null
    fetchCategories(newPlatform || null)
    fetchServices()
  }
})

watch(locale, () => {
  fetchServices()
  fetchPlatforms()
  fetchCategories(filters.value.platform)
  fetchFeatured()
})
</script>
