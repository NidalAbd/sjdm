<template>
    <div class="page-header mb-6">
        <div class="d-flex align-center justify-space-between flex-wrap ga-3">
            <div class="d-flex align-center ga-3">
                <div class="page-header-icon">
                    <v-icon :color="iconColor || 'primary'" size="22">{{ icon }}</v-icon>
                </div>
                <div>
                    <h1 class="page-header-title">{{ title }}</h1>
                    <p v-if="subtitle" class="page-header-subtitle">{{ subtitle }}</p>
                </div>
            </div>
            <div class="d-flex align-center ga-2">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    icon: { type: String, default: 'mdi-view-dashboard' },
    iconColor: { type: String, default: null },
})
</script>

<style scoped>
.page-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--v-theme-primary), 0.1);
    flex-shrink: 0;
}
.page-header-title {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    line-height: 1.3;
}
.page-header-subtitle {
    font-size: 0.8rem;
    opacity: 0.5;
    margin-top: 2px;
    margin-bottom: 0;
}
</style>
