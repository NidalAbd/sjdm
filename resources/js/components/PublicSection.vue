<template>
    <section class="public-section" :class="{ 'section-alt': alt }">
        <v-container>
            <div v-if="title" class="section-header" :class="{ 'text-center': center }">
                <h2 class="section-title">{{ title }}</h2>
                <p v-if="subtitle" class="section-subtitle">{{ subtitle }}</p>
            </div>
            <slot></slot>
        </v-container>
    </section>
</template>

<script setup>
defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    alt: { type: Boolean, default: false },
    center: { type: Boolean, default: true },
})
</script>

<style scoped>
.public-section {
    padding: 72px 0;
}
.section-alt {
    background: rgb(var(--v-theme-surface));
}
.section-header {
    margin-bottom: 40px;
}
.section-title {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.section-subtitle {
    font-size: 0.95rem;
    opacity: 0.5;
    margin-top: 8px;
    max-width: 500px;
}
.text-center .section-subtitle {
    margin-left: auto;
    margin-right: auto;
}
@media (max-width: 960px) {
    .public-section { padding: 48px 0; }
    .section-title { font-size: 1.4rem; }
    .section-header { margin-bottom: 28px; }
}
</style>
