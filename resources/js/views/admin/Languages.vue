<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <div>
                <h1 class="text-h5 font-weight-bold">Languages & Translations</h1>
                <p class="text-body-2 text-medium-emphasis">Manage languages and auto-translate UI strings</p>
            </div>
            <div class="d-flex ga-2">
                <v-btn color="primary" variant="tonal" @click="seedTranslations" :loading="seeding" prepend-icon="mdi-database-import">
                    Seed EN/AR
                </v-btn>
                <v-btn color="success" @click="showTranslateDialog = true" prepend-icon="mdi-translate">
                    Auto Translate
                </v-btn>
            </div>
        </div>

        <!-- Stats Cards -->
        <v-row class="mb-4">
            <v-col cols="12" sm="4">
                <v-card>
                    <v-card-text class="d-flex align-center">
                        <v-avatar color="primary" variant="tonal" size="48" class="mr-4">
                            <v-icon>mdi-translate</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-h5 font-weight-bold">{{ languages.length }}</div>
                            <div class="text-body-2 text-medium-emphasis">Total Languages</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4">
                <v-card>
                    <v-card-text class="d-flex align-center">
                        <v-avatar color="success" variant="tonal" size="48" class="mr-4">
                            <v-icon>mdi-check-circle</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-h5 font-weight-bold">{{ languages.filter(l => l.is_active).length }}</div>
                            <div class="text-body-2 text-medium-emphasis">Active Languages</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4">
                <v-card>
                    <v-card-text class="d-flex align-center">
                        <v-avatar color="info" variant="tonal" size="48" class="mr-4">
                            <v-icon>mdi-text</v-icon>
                        </v-avatar>
                        <div>
                            <div class="text-h5 font-weight-bold">{{ translationStats.total || 0 }}</div>
                            <div class="text-body-2 text-medium-emphasis">Total Translations</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Languages Table -->
        <v-card>
            <v-card-title class="d-flex align-center">
                <v-icon start>mdi-earth</v-icon>
                Languages
                <v-spacer></v-spacer>
                <v-btn icon variant="text" @click="fetchLanguages" :loading="loading">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-card-title>

            <v-data-table
                :headers="headers"
                :items="languages"
                :loading="loading"
                class="elevation-0"
                density="comfortable"
            >
                <template v-slot:item.native_name="{ item }">
                    <div class="d-flex align-center">
                        <span class="font-weight-medium">{{ item.native_name }}</span>
                        <v-chip v-if="item.is_default" color="primary" size="x-small" class="ml-2">Default</v-chip>
                        <v-chip v-if="item.direction === 'rtl'" color="warning" size="x-small" variant="tonal" class="ml-1">RTL</v-chip>
                    </div>
                </template>

                <template v-slot:item.is_active="{ item }">
                    <v-switch
                        :model-value="item.is_active"
                        color="success"
                        density="compact"
                        hide-details
                        @change="toggleLanguage(item)"
                    ></v-switch>
                </template>

                <template v-slot:item.translations_count="{ item }">
                    <v-chip :color="item.translations_count > 0 ? 'success' : 'error'" size="small" variant="tonal">
                        {{ item.translations_count || 0 }}
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <v-btn
                        icon
                        variant="text"
                        size="small"
                        color="primary"
                        @click="translateSingle(item)"
                        :loading="translatingLocale === item.code"
                        :disabled="item.code === 'en'"
                    >
                        <v-icon size="small">mdi-translate</v-icon>
                        <v-tooltip activator="parent" location="top">Auto-translate to {{ item.name }}</v-tooltip>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>

        <!-- Auto Translate Dialog -->
        <v-dialog v-model="showTranslateDialog" max-width="500">
            <v-card>
                <v-card-title>Auto Translate All Languages</v-card-title>
                <v-card-text>
                    <p class="mb-4">This will translate all UI strings from English to all active languages using Google Translate (free) or OpenAI (if configured in .env).</p>
                    <v-alert type="info" variant="tonal" class="mb-4">
                        <strong>How it works:</strong><br>
                        1. English base translations must be seeded first<br>
                        2. Each language gets translated from English<br>
                        3. Existing translations are kept (not overwritten)
                    </v-alert>
                    <v-select
                        v-model="selectedLocales"
                        :items="translatableLanguages"
                        item-title="name"
                        item-value="code"
                        label="Select languages to translate"
                        multiple
                        chips
                        closable-chips
                    ></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="showTranslateDialog = false">Cancel</v-btn>
                    <v-btn color="success" @click="translateAll" :loading="translatingAll" :disabled="selectedLocales.length === 0">
                        Translate {{ selectedLocales.length }} languages
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Snackbar -->
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="4000" location="top right">
            {{ snackbar.text }}
        </v-snackbar>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const loading = ref(false)
const seeding = ref(false)
const languages = ref([])
const translationStats = ref({})
const showTranslateDialog = ref(false)
const selectedLocales = ref([])
const translatingAll = ref(false)
const translatingLocale = ref(null)
const snackbar = ref({ show: false, text: '', color: 'success' })

const headers = [
    { title: 'Code', key: 'code', width: '80px' },
    { title: 'Language', key: 'native_name' },
    { title: 'English Name', key: 'name' },
    { title: 'Direction', key: 'direction', width: '100px' },
    { title: 'Translations', key: 'translations_count', width: '120px' },
    { title: 'Active', key: 'is_active', width: '100px' },
    { title: 'Actions', key: 'actions', width: '80px', sortable: false },
]

const translatableLanguages = computed(() => {
    return languages.value.filter(l => l.code !== 'en' && l.is_active)
})

const fetchLanguages = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/admin/languages')
        languages.value = response.data.languages || []
        translationStats.value = response.data.stats || {}
    } catch (error) {
        console.error('Error fetching languages:', error)
    } finally {
        loading.value = false
    }
}

const toggleLanguage = async (lang) => {
    try {
        await axios.patch(`/api/admin/languages/${lang.code}/toggle`)
        lang.is_active = !lang.is_active
        showSnack(`${lang.name} ${lang.is_active ? 'activated' : 'deactivated'}`, 'success')
    } catch (error) {
        showSnack('Failed to update language', 'error')
    }
}

const seedTranslations = async () => {
    seeding.value = true
    try {
        const response = await axios.post('/api/admin/languages/seed')
        showSnack(`Seeded ${response.data.en_count || 0} EN + ${response.data.ar_count || 0} AR translations`, 'success')
        fetchLanguages()
    } catch (error) {
        showSnack('Failed to seed translations', 'error')
    } finally {
        seeding.value = false
    }
}

const translateSingle = async (lang) => {
    translatingLocale.value = lang.code
    try {
        const response = await axios.post(`/api/admin/languages/${lang.code}/translate`)
        showSnack(`Translated ${response.data.completed || 0} strings to ${lang.name}`, 'success')
        fetchLanguages()
    } catch (error) {
        showSnack(`Failed to translate to ${lang.name}`, 'error')
    } finally {
        translatingLocale.value = null
    }
}

const translateAll = async () => {
    translatingAll.value = true
    let completed = 0
    for (const locale of selectedLocales.value) {
        try {
            await axios.post(`/api/admin/languages/${locale}/translate`)
            completed++
        } catch (error) {
            console.error(`Failed to translate ${locale}:`, error)
        }
    }
    showSnack(`Translated ${completed}/${selectedLocales.value.length} languages`, completed > 0 ? 'success' : 'error')
    translatingAll.value = false
    showTranslateDialog.value = false
    fetchLanguages()
}

const showSnack = (text, color) => {
    snackbar.value = { show: true, text, color }
}

onMounted(() => {
    fetchLanguages()
})
</script>
