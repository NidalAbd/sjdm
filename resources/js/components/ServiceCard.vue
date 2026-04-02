<template>
    <router-link :to="`/service/${service.service_id}`" class="svc">
        <div class="svc-top">
            <span class="svc-id">#{{ service.service_id }}</span>
            <span class="svc-price">${{ formatPrice(service.rate) }}<small>/1K</small></span>
        </div>
        <div class="svc-name">{{ service.name || service.name_en || '' }}</div>
        <div class="svc-cat"><v-icon size="12">mdi-tag</v-icon>{{ service.category || service.category_en || '' }}</div>
        <div class="svc-bottom">
            <span class="svc-range">{{ fmtNum(service.min) }} - {{ fmtNum(service.max) }}</span>
            <div class="svc-badges">
                <span v-if="service.refill" class="svc-badge svc-badge-ok">Refill</span>
                <span v-if="service.cancel" class="svc-badge svc-badge-warn">Cancel</span>
            </div>
        </div>
    </router-link>
</template>

<script setup>
defineProps({ service: { type: Object, required: true } })
const fmtNum = (n) => new Intl.NumberFormat().format(n)
const formatPrice = (r) => { const n = Number(r); if (n < 0.001) return n.toFixed(7); if (n < 0.01) return n.toFixed(5); if (n < 1) return n.toFixed(4); return n.toFixed(2) }
</script>
