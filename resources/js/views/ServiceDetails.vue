<template>
  <div>
    <!-- Breadcrumbs -->
    <v-container class="py-4">
      <v-breadcrumbs :items="breadcrumbs" class="px-0">
        <template v-slot:divider>
          <v-icon>mdi-chevron-right</v-icon>
        </template>
      </v-breadcrumbs>
    </v-container>

    <v-container class="pb-12">
      <v-row v-if="service && !loading">
        <v-col cols="12" lg="8">
          <!-- Service Info Card -->
          <v-card variant="outlined" class="mb-6">
            <v-card-text class="pa-8">
              <div class="d-flex flex-wrap align-center gap-2 mb-4">
                <v-chip color="primary" variant="flat">
                  #{{ service.service_id }}
                </v-chip>
                <v-chip variant="tonal">
                  {{ service.category || service.category_en }}
                </v-chip>
                <v-chip variant="tonal">
                  {{ service.type || 'Default' }}
                </v-chip>
              </div>

              <h1 class="text-h4 font-weight-bold mb-4">
                {{ service.name || service.name_en }}
              </h1>

              <v-divider class="my-6" />

              <!-- Stats Grid -->
              <v-row>
                <v-col cols="6" sm="3">
                  <v-sheet rounded class="pa-4 text-center" color="surface-variant">
                    <div class="text-caption text-medium-emphasis mb-1">{{ $t('servicesPage.price') }}</div>
                    <div class="text-h5 font-weight-bold text-success">${{ Number(service.rate).toFixed(4) }}</div>
                  </v-sheet>
                </v-col>
                <v-col cols="6" sm="3">
                  <v-sheet rounded class="pa-4 text-center" color="surface-variant">
                    <div class="text-caption text-medium-emphasis mb-1">{{ $t('servicesPage.min') }}</div>
                    <div class="text-h5 font-weight-bold text-primary">{{ formatNumber(service.min) }}</div>
                  </v-sheet>
                </v-col>
                <v-col cols="6" sm="3">
                  <v-sheet rounded class="pa-4 text-center" color="surface-variant">
                    <div class="text-caption text-medium-emphasis mb-1">{{ $t('servicesPage.max') }}</div>
                    <div class="text-h5 font-weight-bold text-primary">{{ formatNumber(service.max) }}</div>
                  </v-sheet>
                </v-col>
                <v-col cols="6" sm="3">
                  <v-sheet rounded class="pa-4 text-center" color="surface-variant">
                    <div class="text-caption text-medium-emphasis mb-1">{{ $t('servicesPage.type') || 'Type' }}</div>
                    <div class="text-h6 font-weight-bold text-capitalize">{{ service.type || 'Default' }}</div>
                  </v-sheet>
                </v-col>
              </v-row>

              <v-divider class="my-6" />

              <!-- Features -->
              <h3 class="text-h6 font-weight-bold mb-4">{{ $t('servicesPage.features') || 'Features' }}</h3>
              <div class="d-flex flex-wrap gap-3">
                <v-chip v-if="service.refill" color="success" variant="flat" size="large">
                  <v-icon start>mdi-refresh</v-icon>
                  {{ $t('services.refillAvailable') || 'Refill Available' }}
                </v-chip>
                <v-chip v-if="service.cancel" color="warning" variant="flat" size="large">
                  <v-icon start>mdi-close-circle</v-icon>
                  {{ $t('services.cancelAvailable') || 'Cancellation Available' }}
                </v-chip>
                <v-chip color="info" variant="flat" size="large">
                  <v-icon start>mdi-lightning-bolt</v-icon>
                  {{ $t('services.instantStart') || 'Instant Start' }}
                </v-chip>
                <v-chip color="purple" variant="flat" size="large">
                  <v-icon start>mdi-shield-check</v-icon>
                  {{ $t('services.safe') || '100% Safe' }}
                </v-chip>
              </div>
            </v-card-text>
          </v-card>

          <!-- Description Card -->
          <v-card variant="outlined" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold pa-6 pb-2">
              <v-icon start color="primary">mdi-information</v-icon>
              {{ $t('servicesPage.serviceDescription') || 'Service Description' }}
            </v-card-title>
            <v-card-text class="pa-6 pt-0">
              <p class="text-body-2 mb-4">
                {{ locale === 'ar'
                  ? 'هذه الخدمة توفر تفاعلاً عالي الجودة لحسابك على وسائل التواصل الاجتماعي. تبدأ الطلبات عادةً في غضون 0-15 دقيقة وتكتمل بناءً على الكمية المطلوبة.'
                  : 'This service provides high-quality engagement for your social media account. Orders typically start within 0-15 minutes and complete based on the quantity ordered.'
                }}
              </p>
              <v-list density="compact" class="bg-transparent">
                <v-list-item v-for="(item, i) in descriptionPoints" :key="i" class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="success" size="small">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title>{{ item }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Related Services -->
          <div v-if="relatedServices.length > 0">
            <h2 class="text-h5 font-weight-bold mb-4">
              <v-icon start color="primary">mdi-link-variant</v-icon>
              {{ $t('servicesPage.relatedServices') || 'Related Services' }}
            </h2>
            <v-row>
              <v-col v-for="related in relatedServices" :key="related.service_id" cols="12" sm="6">
                <v-card variant="outlined" class="h-100 hover-lift" @click="goToService(related.service_id)" style="cursor: pointer">
                  <v-card-text class="pa-4">
                    <div class="d-flex justify-space-between align-start mb-2">
                      <v-chip size="x-small" color="primary" variant="flat">
                        #{{ related.service_id }}
                      </v-chip>
                      <span class="text-success font-weight-bold">${{ Number(related.rate).toFixed(4) }}/1K</span>
                    </div>
                    <h4 class="text-subtitle-2 font-weight-bold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden">
                      {{ related.name || related.name_en }}
                    </h4>
                    <div class="d-flex gap-2">
                      <v-chip v-if="related.refill" size="x-small" color="success" variant="tonal">
                        {{ $t('services.refill') }}
                      </v-chip>
                      <v-chip v-if="related.cancel" size="x-small" color="warning" variant="tonal">
                        {{ $t('common.cancel') }}
                      </v-chip>
                    </div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </v-col>

        <v-col cols="12" lg="4">
          <!-- Order Card -->
          <v-card variant="outlined" style="position: sticky; top: 80px">
            <div :style="{ background: store.gradientStyle }" class="pa-4 d-flex align-center">
              <v-icon start color="white">mdi-cart</v-icon>
              <span class="text-h6 font-weight-bold text-white">{{ $t('servicesPage.placeOrder') || 'Place Order' }}</span>
            </div>
            <v-card-text class="pa-6">
              <v-alert type="info" variant="tonal" class="mb-6" density="compact">
                <template v-slot:prepend>
                  <v-icon>mdi-login</v-icon>
                </template>
                {{ $t('servicesPage.loginToOrder') || 'Login to place orders' }}
              </v-alert>

              <v-text-field
                v-model="orderForm.link"
                :label="$t('servicesPage.link') || 'Link'"
                :placeholder="$t('servicesPage.linkPlaceholder') || 'Enter your profile/post link'"
                prepend-inner-icon="mdi-link"
                variant="outlined"
                density="comfortable"
                class="mb-4"
              />

              <v-text-field
                v-model="orderForm.quantity"
                :label="$t('servicesPage.quantity') || 'Quantity'"
                type="number"
                :min="service.min"
                :max="service.max"
                prepend-inner-icon="mdi-numeric"
                variant="outlined"
                density="comfortable"
                :hint="`${$t('servicesPage.min') || 'Min'}: ${formatNumber(service.min)} - ${$t('servicesPage.max') || 'Max'}: ${formatNumber(service.max)}`"
                persistent-hint
                class="mb-4"
              />

              <v-divider class="my-4" />

              <div class="d-flex justify-space-between mb-2">
                <span class="text-medium-emphasis">{{ $t('servicesPage.pricePer1000') || 'Price per 1000:' }}</span>
                <span class="font-weight-bold">${{ Number(service.rate).toFixed(4) }}</span>
              </div>
              <div class="d-flex justify-space-between mb-4">
                <span class="text-medium-emphasis">{{ $t('servicesPage.total') || 'Total:' }}</span>
                <span class="text-h5 font-weight-bold text-primary">${{ calculatedPrice }}</span>
              </div>

              <v-btn color="primary" size="large" block href="/login" class="mb-4">
                <v-icon start>mdi-login</v-icon>
                {{ $t('servicesPage.loginToOrderBtn') || 'Login to Order' }}
              </v-btn>

              <p class="text-caption text-medium-emphasis text-center">
                {{ $t('servicesPage.noAccount') || "Don't have an account?" }}
                <a href="/register" class="text-primary font-weight-bold">{{ $t('servicesPage.signUp') || 'Sign up' }}</a>
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Loading -->
      <v-row v-else-if="loading">
        <v-col cols="12" lg="8">
          <v-skeleton-loader type="card" height="400" />
        </v-col>
        <v-col cols="12" lg="4">
          <v-skeleton-loader type="card" height="400" />
        </v-col>
      </v-row>

      <!-- Not Found -->
      <v-card v-else class="text-center py-16" variant="outlined">
        <v-icon size="80" color="error-lighten-2" class="mb-4">mdi-alert-circle</v-icon>
        <h2 class="text-h5 text-medium-emphasis mb-4">{{ $t('servicesPage.serviceNotFound') || 'Service not found' }}</h2>
        <v-btn color="primary" to="/all-services">
          <v-icon start>mdi-arrow-left</v-icon>
          {{ $t('servicesPage.browseAll') || 'Browse all services' }}
        </v-btn>
      </v-card>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { useHead } from '@vueuse/head'

const route = useRoute()
const router = useRouter()
const store = useAppStore()

// heroBg computed
const heroBg = computed(() => {
    const c = store.currentThemeColor
    const p = store.isDark ? c.primaryDark : c.primary
    const r = parseInt(p.slice(1,3),16), g = parseInt(p.slice(3,5),16), b = parseInt(p.slice(5,7),16)
    return `linear-gradient(135deg, rgba(${r},${g},${b}, 0.06) 0%, transparent 60%)`
})

const service = ref(null)
const relatedServices = ref([])
const loading = ref(true)

const BASE_URL = 'https://smmjd.com'

const orderForm = ref({
  link: '',
  quantity: 100
})

const locale = computed(() => store.locale)

const breadcrumbs = computed(() => [
  { title: locale.value === 'ar' ? 'الرئيسية' : 'Home', to: '/' },
  { title: locale.value === 'ar' ? 'الخدمات' : 'Services', to: '/all-services' },
  { title: service.value ? (service.value.category || service.value.category_en) : '...', disabled: true },
  { title: service.value ? `#${service.value.service_id}` : '...', disabled: true },
])

const descriptionPoints = computed(() => {
  return locale.value === 'ar'
    ? [
        'تفاعل حقيقي ونشط',
        'طرق توصيل آمنة',
        'لا يلزم كلمة مرور',
        'دعم العملاء على مدار الساعة',
        'ضمان استرداد الأموال',
        'التسليم السريع'
      ]
    : [
        'Real and active engagement',
        'Safe delivery methods',
        'No password required',
        '24/7 customer support',
        'Money-back guarantee',
        'Fast delivery'
      ]
})

const calculatedPrice = computed(() => {
  if (!service.value || !orderForm.value.quantity) return '0.00'
  const price = (parseFloat(service.value.rate) * orderForm.value.quantity) / 1000
  return price.toFixed(4)
})

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num)
}

const fetchService = async () => {
  loading.value = true
  try {
    const id = route.params.id
    const response = await fetch(`/api/services/${id}?lang=${locale.value}`)
    const data = await response.json()

    if (data.error) {
      service.value = null
    } else {
      service.value = data.service
      relatedServices.value = data.related_services || []
      orderForm.value.quantity = service.value?.min || 100
    }
  } catch (error) {
    console.error('Error fetching service:', error)
    service.value = null
  } finally {
    loading.value = false
  }
}

const goToService = (id) => {
  router.push(`/service/${id}`)
}

// SEO: Product structured data for Google Shopping
const productStructuredData = computed(() => {
  if (!service.value) return null

  const serviceName = service.value.name || service.value.name_en
  const categoryName = service.value.category || service.value.category_en

  return {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: serviceName,
    description: locale.value === 'ar'
      ? `خدمة ${serviceName} - تفاعل عالي الجودة لحسابك على وسائل التواصل الاجتماعي`
      : `${serviceName} - High-quality engagement for your social media account`,
    sku: `SMM-${service.value.service_id}`,
    mpn: `SMM-${service.value.service_id}`,
    brand: {
      '@type': 'Brand',
      name: 'SMM Panel'
    },
    category: categoryName,
    offers: {
      '@type': 'Offer',
      url: `${BASE_URL}/service/${service.value.service_id}`,
      priceCurrency: 'USD',
      price: Number(service.value.rate).toFixed(4),
      priceValidUntil: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      availability: 'https://schema.org/InStock',
      itemCondition: 'https://schema.org/NewCondition',
      seller: {
        '@type': 'Organization',
        name: 'SMM Panel'
      },
      hasMerchantReturnPolicy: {
        '@type': 'MerchantReturnPolicy',
        returnPolicyCategory: 'https://schema.org/MerchantReturnFiniteReturnWindow',
        merchantReturnDays: 30,
        returnMethod: 'https://schema.org/ReturnByMail',
        returnFees: 'https://schema.org/FreeReturn'
      },
      shippingDetails: {
        '@type': 'OfferShippingDetails',
        shippingRate: {
          '@type': 'MonetaryAmount',
          value: 0,
          currency: 'USD'
        },
        deliveryTime: {
          '@type': 'ShippingDeliveryTime',
          handlingTime: {
            '@type': 'QuantitativeValue',
            minValue: 0,
            maxValue: 15,
            unitCode: 'MIN'
          },
          transitTime: {
            '@type': 'QuantitativeValue',
            minValue: 0,
            maxValue: 24,
            unitCode: 'HUR'
          }
        }
      }
    },
    aggregateRating: {
      '@type': 'AggregateRating',
      ratingValue: '4.8',
      reviewCount: '150',
      bestRating: '5',
      worstRating: '1'
    },
    review: [
      {
        '@type': 'Review',
        reviewRating: {
          '@type': 'Rating',
          ratingValue: '5',
          bestRating: '5'
        },
        author: {
          '@type': 'Person',
          name: 'Verified Buyer'
        },
        reviewBody: locale.value === 'ar'
          ? 'خدمة ممتازة! توصيل سريع وجودة عالية.'
          : 'Excellent service! Fast delivery and high quality.'
      }
    ]
  }
})

// SEO: BreadcrumbList structured data
const breadcrumbStructuredData = computed(() => {
  if (!service.value) return null

  const categoryName = service.value.category || service.value.category_en
  const serviceName = service.value.name || service.value.name_en

  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      {
        '@type': 'ListItem',
        position: 1,
        name: locale.value === 'ar' ? 'الرئيسية' : 'Home',
        item: BASE_URL
      },
      {
        '@type': 'ListItem',
        position: 2,
        name: locale.value === 'ar' ? 'الخدمات' : 'Services',
        item: `${BASE_URL}/all-services`
      },
      {
        '@type': 'ListItem',
        position: 3,
        name: categoryName,
        item: `${BASE_URL}/all-services?category=${encodeURIComponent(categoryName)}`
      },
      {
        '@type': 'ListItem',
        position: 4,
        name: serviceName
      }
    ]
  }
})

// Dynamic SEO head configuration
const seoHead = computed(() => {
  if (!service.value) {
    return {
      title: 'Service Details | SMM Panel',
      meta: [{ name: 'robots', content: 'noindex' }]
    }
  }

  const serviceName = service.value.name || service.value.name_en
  const categoryName = service.value.category || service.value.category_en
  const title = `${serviceName} | SMM Panel`
  const description = locale.value === 'ar'
    ? `اشترِ ${serviceName} بأرخص الأسعار. السعر: $${Number(service.value.rate).toFixed(4)}/1000. توصيل فوري، دعم على مدار الساعة.`
    : `Buy ${serviceName} at the cheapest prices. Price: $${Number(service.value.rate).toFixed(4)}/1000. Instant delivery, 24/7 support.`

  const structuredDataArray = []
  if (productStructuredData.value) {
    structuredDataArray.push({
      type: 'application/ld+json',
      children: JSON.stringify(productStructuredData.value)
    })
  }
  if (breadcrumbStructuredData.value) {
    structuredDataArray.push({
      type: 'application/ld+json',
      children: JSON.stringify(breadcrumbStructuredData.value)
    })
  }

  return {
    title,
    meta: [
      { name: 'description', content: description },
      { name: 'keywords', content: `${serviceName}, ${categoryName}, SMM panel, buy followers, buy likes, social media marketing` },
      { name: 'robots', content: 'index, follow' },
      // Open Graph
      { property: 'og:title', content: title },
      { property: 'og:description', content: description },
      { property: 'og:type', content: 'product' },
      { property: 'og:url', content: `${BASE_URL}/service/${service.value.service_id}` },
      { property: 'og:image', content: `${BASE_URL}/images/logo.png` },
      { property: 'og:locale', content: locale.value === 'ar' ? 'ar_SA' : 'en_US' },
      // Product specific OG tags
      { property: 'product:price:amount', content: Number(service.value.rate).toFixed(4) },
      { property: 'product:price:currency', content: 'USD' },
      { property: 'product:availability', content: 'in stock' },
      // Twitter Card
      { name: 'twitter:card', content: 'summary_large_image' },
      { name: 'twitter:title', content: title },
      { name: 'twitter:description', content: description }
    ],
    link: [
      { rel: 'canonical', href: `${BASE_URL}/service/${service.value.service_id}` }
    ],
    script: structuredDataArray
  }
})

useHead(seoHead)

onMounted(fetchService)
watch(() => route.params.id, fetchService)
watch(locale, fetchService)
</script>
