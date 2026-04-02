<template>
    <div>
        <v-container class="py-6">
            <div v-if="loading" style="text-align:center;padding:80px 0;">
                <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
            </div>

            <div v-else-if="!service" class="card" style="text-align:center;padding:60px 24px;">
                <v-icon size="48" style="opacity:0.2;">mdi-alert-circle</v-icon>
                <h2 class="heading-md" style="margin-top:12px;">Service not found</h2>
                <v-btn color="primary" to="/all-services" class="mt-4">{{ $t('servicesPage.allServices') }}</v-btn>
            </div>

            <v-row v-else>
                <v-col cols="12" md="8">
                    <div style="display:flex;align-items:center;gap:6px;font-size:0.78rem;opacity:0.5;margin-bottom:16px;">
                        <router-link to="/" style="color:inherit;text-decoration:none;">{{ $t('publicNav.home') }}</router-link>
                        <v-icon size="12">mdi-chevron-right</v-icon>
                        <router-link to="/all-services" style="color:inherit;text-decoration:none;">{{ $t('publicNav.services') }}</router-link>
                        <v-icon size="12">mdi-chevron-right</v-icon>
                        <span>#{{ service.service_id }}</span>
                    </div>

                    <div class="card" style="margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <span style="font-size:0.72rem;font-weight:600;padding:3px 10px;border-radius:6px;background:rgba(var(--v-theme-primary),0.1);color:rgb(var(--v-theme-primary));">#{{ service.service_id }}</span>
                            <span style="font-size:0.72rem;font-weight:600;padding:3px 10px;border-radius:6px;background:rgba(var(--v-theme-on-surface),0.06);">{{ service.type || 'Default' }}</span>
                        </div>

                        <h1 class="heading-lg" style="margin-bottom:8px;">{{ serviceName }}</h1>
                        <p style="font-size:0.82rem;color:rgb(var(--v-theme-primary));margin-bottom:20px;">
                            <v-icon size="14">mdi-tag</v-icon> {{ serviceCategory }}
                        </p>

                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;">
                            <div class="card" style="text-align:center;padding:14px 8px;">
                                <div class="label">{{ $t('servicesPage.price') }}</div>
                                <div style="font-size:1.1rem;font-weight:800;color:rgb(var(--v-theme-success));">${{ formatPrice(service.rate) }}</div>
                            </div>
                            <div class="card" style="text-align:center;padding:14px 8px;">
                                <div class="label">{{ $t('servicesPage.min') }}</div>
                                <div style="font-size:1.1rem;font-weight:800;">{{ fmtNum(service.min) }}</div>
                            </div>
                            <div class="card" style="text-align:center;padding:14px 8px;">
                                <div class="label">{{ $t('servicesPage.max') }}</div>
                                <div style="font-size:1.1rem;font-weight:800;">{{ fmtNum(service.max) }}</div>
                            </div>
                            <div class="card" style="text-align:center;padding:14px 8px;">
                                <div class="label">/1K</div>
                                <div style="font-size:1.1rem;font-weight:800;">${{ formatPrice(service.rate) }}</div>
                            </div>
                        </div>

                        <div class="features-row">
                            <span v-if="service.refill" class="feature-chip"><v-icon size="12">mdi-refresh</v-icon> {{ $t('services.refill') }}</span>
                            <span v-if="service.cancel" class="feature-chip" style="background:rgba(var(--v-theme-warning),0.08);color:rgb(var(--v-theme-warning));"><v-icon size="12">mdi-close-circle</v-icon> {{ $t('common.cancel') }}</span>
                            <span class="feature-chip"><v-icon size="12">mdi-lightning-bolt</v-icon> {{ $t('home.instantDelivery') }}</span>
                            <span class="feature-chip"><v-icon size="12">mdi-shield-check</v-icon> {{ $t('home.securePayment') }}</span>
                        </div>
                    </div>

                    <div v-if="relatedServices.length">
                        <h3 class="heading-md" style="margin-bottom:14px;">{{ $t('servicesPage.featuredServices') }}</h3>
                        <div class="services-grid">
                            <router-link v-for="rs in relatedServices" :key="rs.service_id" :to="`/service/${rs.service_id}`" class="svc">
                                <div class="svc-top">
                                    <span class="svc-id">#{{ rs.service_id }}</span>
                                    <span class="svc-price">${{ formatPrice(rs.rate) }}<small>/1K</small></span>
                                </div>
                                <div class="svc-name">{{ rs.name || rs.name_en }}</div>
                                <div class="svc-cat"><v-icon size="12">mdi-tag</v-icon> {{ rs.category || rs.category_en }}</div>
                                <div class="svc-bottom">
                                    <span class="svc-range">{{ fmtNum(rs.min) }} - {{ fmtNum(rs.max) }}</span>
                                </div>
                            </router-link>
                        </div>
                    </div>
                </v-col>

                <v-col cols="12" md="4">
                    <div class="card" style="position:sticky;top:72px;">
                        <h3 class="heading-md" style="margin-bottom:16px;">
                            <v-icon size="18" color="primary" class="mr-1">mdi-cart</v-icon>
                            {{ $t('home.orderNow') }}
                        </h3>
                        <div style="margin-bottom:12px;">
                            <div class="label" style="margin-bottom:6px;">{{ $t('orders.link') }}</div>
                            <v-text-field v-model="orderForm.link" placeholder="https://..." prepend-inner-icon="mdi-link"></v-text-field>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div class="label" style="margin-bottom:6px;">{{ $t('orders.quantity') }}</div>
                            <v-text-field v-model.number="orderForm.quantity" type="number" :min="service.min" :max="service.max" prepend-inner-icon="mdi-numeric"></v-text-field>
                            <div style="font-size:0.7rem;opacity:0.4;margin-top:2px;">Min: {{ fmtNum(service.min) }} — Max: {{ fmtNum(service.max) }}</div>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px;border-radius:10px;background:rgba(var(--v-theme-success),0.06);margin-bottom:16px;">
                            <span style="font-size:0.82rem;font-weight:600;">{{ $t('common.total') }}</span>
                            <span style="font-size:1.3rem;font-weight:800;color:rgb(var(--v-theme-success));">${{ calculatedPrice }}</span>
                        </div>
                        <v-btn color="primary" size="large" block href="/login" prepend-icon="mdi-login">
                            {{ $t('publicNav.signIn') }} & {{ $t('home.orderNow') }}
                        </v-btn>
                        <p style="text-align:center;margin-top:10px;font-size:0.75rem;opacity:0.4;">
                            {{ $t('home.noAccount') }} <a href="/register" style="color:rgb(var(--v-theme-primary));text-decoration:none;">{{ $t('publicNav.signUp') }}</a>
                        </p>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '../stores/app'
import axios from 'axios'

const route = useRoute()
const store = useAppStore()

const service = ref(null)
const relatedServices = ref([])
const loading = ref(true)
const orderForm = ref({ link: '', quantity: 0 })

const serviceName = computed(() => service.value?.name || service.value?.name_en || '')
const serviceCategory = computed(() => service.value?.category || service.value?.category_en || '')
const calculatedPrice = computed(() => {
    if (!service.value || !orderForm.value.quantity) return '0.00'
    return formatPrice((orderForm.value.quantity / 1000) * Number(service.value.rate))
})

const fmtNum = (n) => new Intl.NumberFormat().format(n)
const formatPrice = (r) => { const n = Number(r); if (n < 0.001) return n.toFixed(7); if (n < 0.01) return n.toFixed(5); if (n < 1) return n.toFixed(4); return n.toFixed(2) }

const fetchService = async (id) => {
    loading.value = true
    try {
        const res = await axios.get(`/api/services/${id}`, { params: { lang: store.locale } })
        service.value = res.data.service
        relatedServices.value = res.data.related_services || []
        if (service.value) orderForm.value.quantity = service.value.min
    } catch (e) { service.value = null }
    finally { loading.value = false }
}

watch(() => route.params.id, (id) => { if (id) fetchService(id) })
onMounted(() => { fetchService(route.params.id) })
</script>
