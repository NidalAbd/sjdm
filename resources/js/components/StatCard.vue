<template>
    <v-card :to="to" class="stat-card h-100 hover-lift" variant="outlined">
        <v-card-text class="pa-4">
            <div class="d-flex align-center justify-space-between mb-3">
                <v-avatar :color="color" variant="tonal" size="40" rounded="lg">
                    <v-icon size="20">{{ icon }}</v-icon>
                </v-avatar>
                <v-icon v-if="to" size="16" class="text-medium-emphasis">mdi-arrow-top-right</v-icon>
            </div>
            <div class="text-h5 font-weight-bold stat-value mb-1">{{ value }}</div>
            <div class="text-caption text-medium-emphasis">{{ title }}</div>
        </v-card-text>
    </v-card>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, default: 'mdi-chart-box' },
    color: { type: String, default: 'primary' },
    to: { type: String, default: null },
})
</script>
