<template>
    <section class="hero page-hero">
        <v-container>
            <div class="hero-badge"><v-icon size="14">{{ icon }}</v-icon> {{ badge }}</div>
            <h1 class="heading-xl hero-title">{{ title }}</h1>
            <p v-if="subtitle" class="hero-desc" style="margin-left:auto;margin-right:auto;">{{ subtitle }}</p>
        </v-container>
    </section>
</template>

<script setup>
defineProps({
    icon: { type: String, required: true },
    badge: { type: String, required: true },
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
})
</script>
