<template>
    <div class="stats-row mb-5">
        <div class="stats-grid">
            <div v-for="(stat, i) in stats" :key="i" class="stat-item" @click="stat.to && $router.push(stat.to)">
                <div class="stat-icon" :style="{ background: `rgba(var(--v-theme-${stat.color || 'primary'}), 0.1)` }">
                    <v-icon :color="stat.color || 'primary'" size="20">{{ stat.icon }}</v-icon>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ stat.value }}</div>
                    <div class="stat-label">{{ stat.label }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    stats: {
        type: Array,
        required: true,
        // Each stat: { value, label, icon, color, to? }
    }
})
</script>

<style scoped>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}
.stat-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 14px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    transition: border-color 0.15s ease;
    cursor: default;
}
.stat-item[onClick] { cursor: pointer; }
.stat-item:hover {
    border-color: rgba(var(--v-theme-primary), 0.3);
}
.stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-number {
    font-size: 1.3rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
}
.stat-label {
    font-size: 0.72rem;
    opacity: 0.45;
    margin-top: 1px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 500;
}
@media (max-width: 600px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .stat-item { padding: 12px 14px; }
    .stat-number { font-size: 1.1rem; }
}
</style>
