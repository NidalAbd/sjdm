<template>
    <div class="cta-card" :style="{ background: store.gradientStyle }">
        <h2>{{ title }}</h2>
        <p v-if="description">{{ description }}</p>
        <div class="d-flex justify-center ga-3 flex-wrap">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { useAppStore } from '../../stores/app'

defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
})

const store = useAppStore()
</script>
