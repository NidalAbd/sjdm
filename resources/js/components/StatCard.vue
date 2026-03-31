<template>
    <v-card
        :to="to"
        :color="color"
        variant="flat"
        class="stat-card h-100"
        hover
    >
        <v-card-text class="d-flex align-center justify-space-between">
            <div>
                <div class="text-h4 font-weight-bold text-white mb-1">
                    {{ value }}
                </div>
                <div class="text-body-2 text-white opacity-80">
                    {{ title }}
                </div>
            </div>
            <v-avatar :color="iconBgColor" size="56">
                <v-icon :color="iconColor" size="28">{{ icon }}</v-icon>
            </v-avatar>
        </v-card-text>
        <v-card-actions v-if="to" class="stat-card-footer px-4 py-2">
            <span class="text-caption text-white opacity-80">View Details</span>
            <v-spacer></v-spacer>
            <v-icon size="small" color="white" class="opacity-80">mdi-arrow-right</v-icon>
        </v-card-actions>
    </v-card>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    icon: {
        type: String,
        default: 'mdi-chart-box'
    },
    color: {
        type: String,
        default: 'primary'
    },
    to: {
        type: String,
        default: null
    }
})

const iconBgColor = computed(() => {
    return `rgba(255, 255, 255, 0.2)`
})

const iconColor = computed(() => {
    return 'white'
})
</script>

<style scoped>
.stat-card {
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.stat-card-footer {
    background: rgba(0, 0, 0, 0.1);
}
</style>
