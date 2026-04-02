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
          <!-- Service Info -->
          <div class="card mb-6" style="padding: 2rem;">
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

            <h1 class="heading-xl mb-4">{{ service.name || service.name_en }}</h1>

            <hr style="border: none; border-top: 1px solid var(--v-border-color, rgba(0,0,0,0.12)); margin: 1.5rem 0;" />

            <!-- Stats Bar -->
            <div class="stats-bar">
              <div class="stats-bar-item">
                <span class="stats-bar-label">{{ $t('servicesPage.price') }}</span>
                <span class="stats-bar-value" style="color: var(--color-success);">${{ formatPrice(service.rate) }}</span>
              </div>
              <div class="stats-bar-item">
                <span class="stats-bar-label">{{ $t('servicesPage.min') }}</span>
                <span class="stats-bar-value">{{ formatNumber(service.min) }}</span>
              </div>
              <div class="stats-bar-item">
                <span class="stats-bar-label">{{ $t('servicesPage.max') }}</span>
                <span class="stats-bar-value">{{ formatNumber(service.max) }}</span>
              </div>
              <div class="stats-bar-item">
                <span class="stats-bar-label">{{ $t('servicesPage.type') || 'Type' }}</span>
                <span class="stats-bar-value" style="text-transform: capitalize;">{{ service.type || 'Default' }}</span>
              </div>
            </div>

            <hr style="border: none; border-top: 1px solid var(--v-border-color, rgba(0,0,0,0.12)); margin: 1.5rem 0;" />

            <!-- Features -->
            <h3 class="heading-md mb-4">{{ $t('servicesPage.features') || 'Features' }}</h3>
            <div class="d-flex flex-wrap gap-3">
              <span v-if="service.refill" class="feature-chip">
                <v-icon size="16">mdi-refresh</v-icon>
                {{ $t('services.refillAvailable') || 'Refill Available' }}
              </span>
              <span v-if="service.cancel" class="feature-chip">
                <v-icon size="16">mdi-close-circle</v-icon>
                {{ $t('services.cancelAvailable') || 'Cancellation Available' }}
              </span>
              <span class="feature-chip">
                <v-icon size="16">mdi-lightning-bolt</v-icon>
                {{ $t('services.instantStart') || 'Instant Start' }}
              </span>
              <span class="feature-chip">
                <v-icon size="16">mdi-shield-check</v-icon>
                {{ $t('services.safe') || '100% Safe' }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <div class="card mb-6" style="padding: 2rem;">
            <h3 class="heading-md mb-4">
              <v-icon start color="primary">mdi-information</v-icon>
              {{ $t('servicesPage.serviceDescription') || 'Service Description' }}
            </h3>
            <p class="text-muted mb-4">
              {{ locale === 'ar'
                ? 'هذه الخدمة توفر تفاعلاً عالي الجودة لحسابك على وسائل التواصل الاجتماعي. تبدأ الطلبات عادةً في غضون 0-15 دقيقة وتكتمل بناءً على الكمية المطلوبة.'
                : 'This service provides high-quality engagement for your social media account. Orders typically start within 0-15 minutes and complete based on the quantity ordered.'
              }}
            </p>
            <ul style="list-style: none; padding: 0; margin: 0;">
              <li v-for="(item, i) in descriptionPoints" :key="i" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0;">
                <v-icon color="success" size="small">mdi-check-circle</v-icon>
                <span>{{ item }}</span>
              </li>
            </ul>
          </div>

          <!-- Related Services -->
          <div v-if="relatedServices.length > 0">
            <h2 class="heading-lg mb-4">
              <v-icon start color="primary">mdi-link-variant</v-icon>
              {{ $t('servicesPage.relatedServices') || 'Related Services' }}
            </h2>
            <div class="services-grid" style="grid-template-columns: repeat(2, 1fr);">
              <router-link
                v-for="related in relatedServices"
                :key="related.service_id"
                :to="`/service/${related.service_id}`"
                class="svc"
              >
                <div class="svc-top">
                  <span class="svc-id">#{{ related.service_id }}</span>
                  <span class="svc-price">${{ formatPrice(related.rate) }}<small>/1K</small></span>
                </div>
                <div class="svc-name">{{ related.name || related.name_en }}</div>
                <div class="svc-bottom">
                  <div class="svc-badges">
                    <span v-if="related.refill" class="svc-badge svc-badge-ok">{{ $t('services.refill') }}</span>
                    <span v-if="related.cancel" class="svc-badge svc-badge-warn">{{ $t('common.cancel') }}</span>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
        </v-col>

        <v-col cols="12" lg="4">
          <!-- Order Card -->
          <div class="card" style="position: sticky; top: 80px; padding: 0; overflow: hidden;">
            <div :style="{ background: store.gradientStyle }" style="padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
              <v-icon color="white">mdi-cart</v-icon>
              <span class="heading-md" style="color: #fff;">{{ $t('servicesPage.placeOrder') || 'Place Order' }}</span>
            </div>
            <div style="padding: 1.5rem;">
              <div class="card-flat mb-4" style="padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <v-icon color="info">mdi-login</v-icon>
                <span class="text-muted">{{ $t('servicesPage.loginToOrder') || 'Login to place orders' }}</span>
              </div>

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

              <hr style="border: none; border-top: 1px solid var(--v-border-color, rgba(0,0,0,0.12)); margin: 1rem 0;" />

              <div class="d-flex justify-space-between mb-2">
                <span class="text-muted">{{ $t('servicesPage.pricePer1000') || 'Price per 1000:' }}</span>
                <span class="font-weight-bold">${{ formatPrice(service.rate) }}</span>
              </div>
              <div class="d-flex justify-space-between mb-4">
                <span class="text-muted">{{ $t('servicesPage.total') || 'Total:' }}</span>
                <span class="heading-lg text-gradient">${{ calculatedPrice }}</span>
              </div>

              <v-btn color="primary" size="large" block href="/login" class="mb-4">
                <v-icon start>mdi-login</v-icon>
                {{ $t('servicesPage.loginToOrderBtn') || 'Login to Order' }}
              </v-btn>

              <p class="text-muted" style="text-align: center; font-size: 0.75rem;">
                {{ $t('servicesPage.noAccount') || "Don't have an account?" }}
                <a href="/register" class="text-primary font-weight-bold">{{ $t('servicesPage.signUp') || 'Sign up' }}</a>
              </p>
            </div>
          </div>
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
      <div v-else class="card text-center" style="padding: 4rem 2rem;">
        <v-icon size="80" color="error" class="mb-4">mdi-alert-circle</v-icon>
        <h2 class="heading-lg mb-4">{{ $t('servicesPage.serviceNotFound') || 'Service not found' }}</h2>
        <v-btn color="primary" to="/all-services">
          <v-icon start>mdi-arrow-left</v-icon>
          {{ $t('servicesPage.browseAll') || 'Browse all services' }}
        </v-btn>
      </div>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '../stores/app'
import { useHead } from '@vueuse/head'

const route = useRoute()
const router = useRouter()
const store = useAppStore()

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

const formatPrice = (r) => {
  const n = Number(r)
  if (n < 0.001) return n.toFixed(7)
  if (n < 0.01) return n.toFixed(5)
  if (n < 1) return n.toFixed(4)
  return n.toFixed(2)
}

const calculatedPrice = computed(() => {
  if (!service.value || !orderForm.value.quantity) return '0.00'
  const price = (parseFloat(service.value.rate) * orderForm.value.quantity) / 1000
  return formatPrice(price)
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
      { property: 'og:title', content: title },
      { property: 'og:description', content: description },
      { property: 'og:type', content: 'product' },
      { property: 'og:url', content: `${BASE_URL}/service/${service.value.service_id}` },
      { property: 'og:image', content: `${BASE_URL}/images/logo.png` },
      { property: 'og:locale', content: locale.value === 'ar' ? 'ar_SA' : 'en_US' },
      { property: 'product:price:amount', content: Number(service.value.rate).toFixed(4) },
      { property: 'product:price:currency', content: 'USD' },
      { property: 'product:availability', content: 'in stock' },
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
