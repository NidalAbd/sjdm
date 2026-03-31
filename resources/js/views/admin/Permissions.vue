<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center flex-wrap ga-2">
                <v-icon class="mr-2">mdi-lock</v-icon>
                <span>Manage Permissions</span>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="openCreateDialog" prepend-icon="mdi-plus">
                    Add Permission
                </v-btn>
            </v-card-title>
        </v-card>

        <v-card>
            <v-data-table
                :headers="headers"
                :items="permissions"
                :loading="loading"
                :search="search"
                class="elevation-0"
            >
                <template v-slot:top>
                    <v-text-field
                        v-model="search"
                        label="Search permissions"
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        class="ma-4"
                        style="max-width: 400px"
                        hide-details
                    ></v-text-field>
                </template>

                <template v-slot:item.guard_name="{ item }">
                    <v-chip size="small" variant="tonal" color="info">
                        {{ item.guard_name }}
                    </v-chip>
                </template>

                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <template v-slot:item.actions="{ item }">
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="warning"
                        @click="editPermission(item)"
                    >
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="error"
                        @click="deletePermission(item)"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>

        <!-- Create/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="500">
            <v-card>
                <v-card-title class="bg-primary text-white">
                    <v-icon class="mr-2">{{ editMode ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
                    {{ editMode ? 'Edit Permission' : 'Create Permission' }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-text-field
                        v-model="form.name"
                        label="Permission Name"
                        variant="outlined"
                        class="mb-4"
                        hint="Use snake_case, e.g., view_users, create_orders"
                        persistent-hint
                    ></v-text-field>

                    <v-select
                        v-model="form.guard_name"
                        :items="['web', 'api']"
                        label="Guard"
                        variant="outlined"
                    ></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="dialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="savePermission" :loading="saving">
                        {{ editMode ? 'Update' : 'Create' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import axios from 'axios'

const showSnackbar = inject('showSnackbar')

const permissions = ref([])
const loading = ref(false)
const saving = ref(false)
const dialog = ref(false)
const editMode = ref(false)
const search = ref('')

const form = ref({
    id: null,
    name: '',
    guard_name: 'web',
})

const headers = [
    { title: 'ID', key: 'id', width: '80px' },
    { title: 'Name', key: 'name' },
    { title: 'Guard', key: 'guard_name' },
    { title: 'Created', key: 'created_at' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const fetchPermissions = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/permissions')
        permissions.value = response.data.permissions || []
    } catch (error) {
        console.error('Error fetching permissions:', error)
    } finally {
        loading.value = false
    }
}

const openCreateDialog = () => {
    editMode.value = false
    form.value = { id: null, name: '', guard_name: 'web' }
    dialog.value = true
}

const editPermission = (permission) => {
    editMode.value = true
    form.value = { ...permission }
    dialog.value = true
}

const savePermission = async () => {
    saving.value = true
    try {
        if (editMode.value) {
            await axios.put(`/permissions/${form.value.id}`, form.value)
            showSnackbar?.('Permission updated successfully', 'success')
        } else {
            await axios.post('/permissions', form.value)
            showSnackbar?.('Permission created successfully', 'success')
        }
        dialog.value = false
        fetchPermissions()
    } catch (error) {
        console.error('Error saving permission:', error)
        showSnackbar?.('Failed to save permission', 'error')
    } finally {
        saving.value = false
    }
}

const deletePermission = async (permission) => {
    if (!confirm(`Are you sure you want to delete the permission "${permission.name}"?`)) return

    try {
        await axios.delete(`/permissions/${permission.id}`)
        showSnackbar?.('Permission deleted successfully', 'success')
        fetchPermissions()
    } catch (error) {
        console.error('Error deleting permission:', error)
        showSnackbar?.('Failed to delete permission', 'error')
    }
}

onMounted(() => {
    fetchPermissions()
})
</script>
