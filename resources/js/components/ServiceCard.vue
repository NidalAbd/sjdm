<template>
    <router-link :to="`/service/${service.service_id}`" class="service-card-link">
        <div class="svc-card">
            <div class="svc-header">
                <span class="svc-id">#{{ service.service_id }}</span>
                <span class="svc-price">${{ formatPrice(service.rate) }}<small>/1K</small></span>
            </div>
            <h3 class="svc-name">{{ name }}</h3>
            <div class="svc-category">
                <v-icon size="12" class="mr-1">mdi-tag</v-icon>
                {{ category }}
            </div>
            <div class="svc-footer">
                <span class="svc-range">{{ formatNum(service.min) }} - {{ formatNum(service.max) }}</span>
                <div class="svc-badges">
                    <span v-if="service.refill" class="svc-badge svc-badge-success">Refill</span>
                    <span v-if="service.cancel" class="svc-badge svc-badge-warning">Cancel</span>
                </div>
            </div>
        </div>
    </router-link>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    service: { type: Object, required: true },
})

const name = computed(() => props.service.name || props.service.name_en || '')
const category = computed(() => props.service.category || props.service.category_en || '')
const formatNum = (n) => new Intl.NumberFormat().format(n)
const formatPrice = (rate) => {
    const n = Number(rate)
    if (n < 0.001) return n.toFixed(7)
    if (n < 0.01) return n.toFixed(5)
    if (n < 1) return n.toFixed(4)
    return n.toFixed(2)
}
</script>

<style scoped>
.service-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
    height: 100%;
}
.svc-card {
    height: 100%;
    padding: 18px;
    border-radius: 14px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    transition: border-color 0.15s ease, transform 0.15s ease;
    display: flex;
    flex-direction: column;
}
.svc-card:hover {
    border-color: rgb(var(--v-theme-primary));
    transform: translateY(-2px);
}
.svc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.svc-id {
    font-size: 0.7rem;
    font-weight: 600;
    opacity: 0.4;
}
.svc-price {
    font-size: 0.85rem;
    font-weight: 700;
    color: rgb(var(--v-theme-success));
}
.svc-price small {
    font-weight: 500;
    opacity: 0.6;
    font-size: 0.7rem;
}
.svc-name {
    font-size: 0.88rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
}
.svc-category {
    font-size: 0.72rem;
    color: rgb(var(--v-theme-primary));
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}
.svc-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}
.svc-range {
    font-size: 0.7rem;
    opacity: 0.4;
    font-weight: 500;
}
.svc-badges {
    display: flex;
    gap: 4px;
}
.svc-badge {
    font-size: 0.6rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.svc-badge-success {
    background: rgba(var(--v-theme-success), 0.1);
    color: rgb(var(--v-theme-success));
}
.svc-badge-warning {
    background: rgba(var(--v-theme-warning), 0.1);
    color: rgb(var(--v-theme-warning));
}
</style>
