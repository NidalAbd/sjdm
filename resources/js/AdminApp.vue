<template>
    <v-app :theme="store.theme">
        <v-main>
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </v-main>
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top right">
            {{ snackbar.text }}
        </v-snackbar>
    </v-app>
</template>

<script setup>
import { ref, onMounted, provide } from 'vue'
import { useAppStore } from './stores/app'
import { useAuthStore } from './stores/auth'
import { updateThemeColors } from './plugins/vuetify'

const store = useAppStore()
const authStore = useAuthStore()
const snackbar = ref({ show: false, text: '', color: 'success' })

const showSnackbar = (text, color = 'success') => {
    snackbar.value = { show: true, text, color }
}
provide('showSnackbar', showSnackbar)

onMounted(async () => {
    await authStore.fetchUser()
    await store.fetchLanguages()
    updateThemeColors(store.themeColor)
})
</script>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.12s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
