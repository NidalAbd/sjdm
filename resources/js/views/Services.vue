<template>
  <div class="services-page">
    <!-- Hero Section -->
    <v-container fluid class="hero-section pa-0">
      <div class="hero-gradient">
        <v-container class="py-16">
          <v-row align="center">
            <v-col cols="12" lg="8">
              <h1 class="text-h3 text-md-h2 font-weight-bold text-white mb-4">
                {{ locale === 'ar' ? 'جميع خدمات SMM' : 'All SMM Services' }}
              </h1>
              <p class="text-h6 text-white-darken-1 mb-6">
                {{ locale === 'ar' ? 'اكتشف مجموعتنا الكاملة من خدمات التسويق عبر وسائل التواصل الاجتماعي' : 'Discover our complete range of social media marketing services' }}
              </p>
              <v-chip v-if="hasFilters" color="white" variant="flat" class="px-4 py-2">
                <v-icon start>mdi-filter</v-icon>
                {{ pagination.total }} {{ locale === 'ar' ? 'خدمة' : 'services found' }}
              </v-chip>
            </v-col>
            <v-col cols="12" lg="4" class="text-center">
              <v-card class="hero-stat-card pa-6" color="rgba(255,255,255,0.15)" variant="flat">
                <div class="text-h2 font-weight-bold text-white">{{ pagination.total || 0 }}</div>
                <div class="text-subtitle-1 text-white-darken-1">{{ locale === 'ar' ? 'إجمالي الخدمات' : 'Total Services' }}</div>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </div>
    </v-container>

    <v-container class="py-8">
      <!-- Filters Section -->
      <v-card class="mb-8" elevation="2">
        <v-card-text class="pa-6">
          <v-row>
            <!-- Search -->
            <v-col cols="12" md="6" lg="4">
              <v-text-field
                v-model="filters.search"
                :label="locale === 'ar' ? 'البحث في الخدمات' : 'Search Services'"
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
                :label="locale === 'ar' ? 'المنصة' : 'Platform'"
                prepend-inner-icon="mdi-apps"
                variant="outlined"
                density="comfortable"
                hide-details
                clearable
                :loading="platformsLoading"
                :disabled="platformOptions.length === 0"
                @update:model-value="fetchServices"
              >
                <template v-slot:no-data>
                  <v-list-item>
                    <v-list-item-title>{{ locale === 'ar' ? 'لا توجد منصات' : 'No platforms available' }}</v-list-item-title>
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
                :label="locale === 'ar' ? 'الفئة' : 'Category'"
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
                    <v-list-item-title>{{ locale === 'ar' ? 'لا توجد فئات' : 'No categories available' }}</v-list-item-title>
                  </v-list-item>
                </template>
              </v-select>
            </v-col>

            <!-- Sort By -->
            <v-col cols="12" md="6" lg="4">
              <v-select
                v-model="filters.sortBy"
                :items="sortOptions"
                :label="locale === 'ar' ? 'ترتيب حسب' : 'Sort By'"
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
                :label="locale === 'ar' ? 'الترتيب' : 'Order'"
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
                :label="locale === 'ar' ? 'عدد العناصر' : 'Items per page'"
                prepend-inner-icon="mdi-view-list"
                variant="outlined"
                density="comfortable"
                hide-details
                @update:model-value="fetchServices"
              />
            </v-col>
          </v-row>

          <v-row class="mt-4">
            <v-col cols="12">
              <div class="d-flex flex-wrap gap-3">
                <v-btn color="primary" size="large" @click="fetchServices" :loading="loading">
                  <v-icon start>mdi-filter</v-icon>
                  {{ locale === 'ar' ? 'تطبيق الفلاتر' : 'Apply Filters' }}
                </v-btn>
                <v-btn variant="outlined" size="large" @click="clearFilters">
                  <v-icon start>mdi-close</v-icon>
                  {{ locale === 'ar' ? 'مسح الفلاتر' : 'Clear Filters' }}
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Featured Services -->
      <div v-if="featured.length > 0 && !hasFilters" class="mb-8">
        <div class="d-flex align-center mb-4">
          <v-icon color="amber" class="mr-2">mdi-star</v-icon>
          <h2 class="text-h5 font-weight-bold">{{ locale === 'ar' ? 'الخدمات المميزة' : 'Featured Services' }}</h2>
        </div>
        <v-row>
          <v-col v-for="service in featured" :key="service.service_id" cols="12" md="6" lg="4">
            <v-card class="featured-card h-100" @click="goToService(service.service_id)">
              <div class="featured-badge">
                <v-icon size="20">mdi-star</v-icon>
              </div>
              <v-card-text class="text-center text-white">
                <h3 class="text-subtitle-1 font-weight-bold mb-2">
                  {{ locale === 'ar' ? service.name_ar : service.name_en }}
                </h3>
                <div class="text-h4 font-weight-bold mb-2">${{ Number(service.rate).toFixed(2) }}</div>
                <div class="text-body-2 opacity-80">{{ formatNumber(service.min) }} - {{ formatNumber(service.max) }}</div>
                <v-btn color="white" variant="flat" class="mt-4" size="small">
                  {{ locale === 'ar' ? 'عرض التفاصيل' : 'View Details' }}
                </v-btn>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </div>

      <!-- Services Section Header -->
      <div class="d-flex flex-wrap justify-space-between align-center mb-6">
        <div>
          <h2 class="text-h5 font-weight-bold">
            <template v-if="filters.search">{{ locale === 'ar' ? 'نتائج البحث' : 'Search Results' }}</template>
            <template v-else-if="filters.platform">{{ filters.platform }} {{ locale === 'ar' ? 'خدمات' : 'Services' }}</template>
            <template v-else-if="filters.category">{{ filters.category }}</template>
            <template v-else>{{ locale === 'ar' ? 'جميع الخدمات' : 'All Services' }}</template>
          </h2>
          <p class="text-body-2 text-medium-emphasis">
            {{ pagination.total }} {{ locale === 'ar' ? 'خدمة متاحة' : 'services available' }}
          </p>
        </div>
        <v-btn-toggle v-model="viewMode" mandatory variant="outlined" divided>
          <v-btn value="grid" icon="mdi-view-grid" />
          <v-btn value="list" icon="mdi-view-list" />
        </v-btn-toggle>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <v-progress-circular indeterminate color="primary" size="64" />
        <p class="mt-4 text-medium-emphasis">{{ locale === 'ar' ? 'جاري التحميل...' : 'Loading services...' }}</p>
      </div>

      <!-- Grid View -->
      <v-row v-else-if="viewMode === 'grid' && services.length > 0">
        <v-col v-for="service in services" :key="service.service_id" cols="12" md="6" lg="4">
          <v-card class="service-card h-100" @click="goToService(service.service_id)">
            <!-- Header -->
            <div class="service-card-header d-flex justify-space-between align-center pa-4 pb-0">
              <v-chip color="primary" size="small" variant="flat">
                #{{ service.service_id }}
              </v-chip>
              <v-chip size="small" variant="tonal">
                {{ service.type || 'Default' }}
              </v-chip>
            </div>

            <!-- Body -->
            <v-card-text class="pt-4">
              <h3 class="text-subtitle-1 font-weight-bold mb-2 service-title">
                {{ locale === 'ar' ? service.name_ar : service.name_en }}
              </h3>
              <div class="text-body-2 text-primary mb-4">
                <v-icon size="16" class="mr-1">mdi-tag</v-icon>
                {{ locale === 'ar' ? service.category_ar : service.category_en }}
              </div>

              <!-- Stats -->
              <v-row dense class="mb-3">
                <v-col cols="6">
                  <v-card variant="tonal" class="pa-3 text-center">
                    <div class="text-caption text-medium-emphasis">{{ locale === 'ar' ? 'الحد الأدنى' : 'Min' }}</div>
                    <div class="text-body-2 font-weight-bold">{{ formatNumber(service.min) }}</div>
                  </v-card>
                </v-col>
                <v-col cols="6">
                  <v-card variant="tonal" class="pa-3 text-center">
                    <div class="text-caption text-medium-emphasis">{{ locale === 'ar' ? 'الحد الأقصى' : 'Max' }}</div>
                    <div class="text-body-2 font-weight-bold">{{ formatNumber(service.max) }}</div>
                  </v-card>
                </v-col>
              </v-row>

              <!-- Features -->
              <div class="d-flex gap-2 flex-wrap">
                <v-chip v-if="service.refill" color="success" size="x-small" variant="flat">
                  <v-icon start size="12">mdi-refresh</v-icon>
                  {{ locale === 'ar' ? 'إعادة تعبئة' : 'Refill' }}
                </v-chip>
                <v-chip v-if="service.cancel" color="warning" size="x-small" variant="flat">
                  <v-icon start size="12">mdi-close</v-icon>
                  {{ locale === 'ar' ? 'إلغاء' : 'Cancel' }}
                </v-chip>
              </div>
            </v-card-text>

            <!-- Footer -->
            <v-divider />
            <v-card-actions class="pa-4">
              <div>
                <span class="text-h6 font-weight-bold text-success">${{ Number(service.rate).toFixed(4) }}</span>
                <span class="text-caption text-medium-emphasis ml-1">/ 1K</span>
              </div>
              <v-spacer />
              <v-btn color="primary" variant="flat" size="small">
                {{ locale === 'ar' ? 'اطلب الآن' : 'Order Now' }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <!-- List View -->
      <v-card v-else-if="viewMode === 'list' && services.length > 0">
        <v-table hover>
          <thead>
            <tr>
              <th>{{ locale === 'ar' ? 'الخدمة' : 'Service' }}</th>
              <th>{{ locale === 'ar' ? 'الفئة' : 'Category' }}</th>
              <th>{{ locale === 'ar' ? 'السعر' : 'Price' }}</th>
              <th>{{ locale === 'ar' ? 'الحدود' : 'Min/Max' }}</th>
              <th>{{ locale === 'ar' ? 'الميزات' : 'Features' }}</th>
              <th>{{ locale === 'ar' ? 'إجراء' : 'Action' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in services" :key="service.service_id" class="service-row" @click="goToService(service.service_id)">
              <td>
                <div class="font-weight-bold">{{ locale === 'ar' ? service.name_ar : service.name_en }}</div>
                <div class="text-caption text-medium-emphasis">#{{ service.service_id }}</div>
              </td>
              <td>
                <v-chip size="small" variant="tonal">
                  {{ locale === 'ar' ? service.category_ar : service.category_en }}
                </v-chip>
              </td>
              <td>
                <span class="font-weight-bold text-success">${{ Number(service.rate).toFixed(4) }}</span>
                <span class="text-caption text-medium-emphasis">/ 1K</span>
              </td>
              <td class="text-caption">
                {{ formatNumber(service.min) }} - {{ formatNumber(service.max) }}
              </td>
              <td>
                <v-chip v-if="service.refill" color="success" size="x-small" class="mr-1">{{ locale === 'ar' ? 'إعادة تعبئة' : 'Refill' }}</v-chip>
                <v-chip v-if="service.cancel" color="warning" size="x-small">{{ locale === 'ar' ? 'إلغاء' : 'Cancel' }}</v-chip>
              </td>
              <td>
                <v-btn color="primary" size="small" variant="flat">{{ locale === 'ar' ? 'اطلب' : 'Order' }}</v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>

      <!-- No Results -->
      <v-card v-else-if="!loading && services.length === 0" class="text-center py-16" variant="flat">
        <v-icon size="80" color="grey" class="mb-4">mdi-magnify</v-icon>
        <h3 class="text-h5 text-medium-emphasis mb-2">{{ locale === 'ar' ? 'لم يتم العثور على خدمات' : 'No services found' }}</h3>
        <p class="text-body-2 text-medium-emphasis mb-6">
          {{ hasFilters ? (locale === 'ar' ? 'جرب تعديل الفلاتر أو مصطلحات البحث' : 'Try adjusting your filters or search terms') : (locale === 'ar' ? 'لا توجد خدمات متاحة حاليًا' : 'No services are currently available') }}
        </p>
        <v-btn v-if="hasFilters" color="primary" @click="clearFilters">
          {{ locale === 'ar' ? 'عرض جميع الخدمات' : 'View All Services' }}
        </v-btn>
      </v-card>

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
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'

const router = useRouter()
const appStore = useAppStore()

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

const locale = computed(() => appStore.locale)

const hasFilters = computed(() => {
  return filters.value.search || filters.value.platform || filters.value.category
})

const platformOptions = computed(() => {
  if (!platforms.value || platforms.value.length === 0) return []
  return platforms.value.map(p => ({
    title: `${p.name} (${p.count})`,
    value: p.key,
  }))
})

const categoryOptions = computed(() => {
  if (!categories.value || categories.value.length === 0) return []
  return categories.value
    .filter(c => c && c.name)
    .map(c => ({
      title: `${c.name} (${c.count})`,
      value: c.name,
    }))
})

const sortOptions = computed(() => [
  { title: locale.value === 'ar' ? 'رقم الخدمة' : 'Service ID', value: 'service_id' },
  { title: locale.value === 'ar' ? 'السعر' : 'Price', value: 'rate' },
  { title: locale.value === 'ar' ? 'الحد الأدنى' : 'Min Order', value: 'min' },
  { title: locale.value === 'ar' ? 'الحد الأقصى' : 'Max Order', value: 'max' },
])

const sortOrderOptions = computed(() => [
  { title: locale.value === 'ar' ? 'تصاعدي' : 'Ascending', value: 'asc' },
  { title: locale.value === 'ar' ? 'تنازلي' : 'Descending', value: 'desc' },
])

const perPageOptions = [
  { title: '25', value: 25 },
  { title: '50', value: 50 },
  { title: '100', value: 100 },
]

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
    const data = await response.json()

    services.value = data.services || []
    pagination.value = data.pagination || pagination.value
  } catch (error) {
    console.error('Error fetching services:', error)
  } finally {
    loading.value = false
  }
}

const fetchPlatforms = async () => {
  platformsLoading.value = true
  try {
    const response = await fetch(`/api/platforms?lang=${locale.value}`)
    const data = await response.json()
    platforms.value = data.platforms || []
    console.log('Platforms loaded:', platforms.value)
  } catch (error) {
    console.error('Error fetching platforms:', error)
    platforms.value = []
  } finally {
    platformsLoading.value = false
  }
}

const fetchCategories = async () => {
  categoriesLoading.value = true
  try {
    const response = await fetch(`/api/categories?lang=${locale.value}`)
    const data = await response.json()
    categories.value = data.categories || []
    console.log('Categories loaded:', categories.value)
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

// Restore view preference
onMounted(() => {
  const savedView = localStorage.getItem('servicesView')
  if (savedView) viewMode.value = savedView

  fetchServices()
  fetchPlatforms()
  fetchCategories()
  fetchFeatured()
})

watch(viewMode, (newVal) => {
  localStorage.setItem('servicesView', newVal)
})

watch(locale, () => {
  fetchServices()
  fetchPlatforms()
  fetchCategories()
  fetchFeatured()
})
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-gradient {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95));
}

.hero-stat-card {
  backdrop-filter: blur(10px);
  border-radius: 16px !important;
}

.service-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 16px !important;
  overflow: hidden;
}

.service-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 35px rgba(var(--v-theme-primary), 0.25) !important;
}

.service-title {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}

.featured-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 16px !important;
  position: relative;
  overflow: hidden;
}

.featured-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4) !important;
}

.featured-badge {
  position: absolute;
  top: -10px;
  right: -10px;
  width: 50px;
  height: 50px;
  background: #ffc107;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #000;
}

.service-row {
  cursor: pointer;
  transition: background 0.2s ease;
}

.service-row:hover {
  background: rgba(var(--v-theme-primary), 0.05);
}
</style>
