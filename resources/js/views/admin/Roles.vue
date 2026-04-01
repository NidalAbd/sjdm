<template>
    <div>
        <PageHeader title="Roles" subtitle="Manage user roles" icon="mdi-shield-account">
            <template #actions>
                <v-btn color="primary" @click="openCreateDialog" prepend-icon="mdi-plus">
                    Add Role
                </v-btn>
            </template>
        </PageHeader>

        <v-card>
            <v-data-table
                :headers="headers"
                :items="roles"
                :loading="loading"
                class="elevation-0"
            >
                <template v-slot:item.permissions="{ item }">
                    <v-chip
                        v-for="(permission, index) in item.permissions?.slice(0, 3)"
                        :key="permission.id || index"
                        size="small"
                        class="ma-1"
                        variant="tonal"
                    >
                        {{ permission.name || permission }}
                    </v-chip>
                    <v-chip
                        v-if="item.permissions?.length > 3"
                        size="small"
                        class="ma-1"
                        color="grey"
                    >
                        +{{ item.permissions.length - 3 }} more
                    </v-chip>
                </template>

                <template v-slot:item.users_count="{ item }">
                    <v-chip size="small" variant="tonal">
                        {{ item.users_count || 0 }}
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="info"
                        @click="viewRole(item)"
                    >
                        <v-icon>mdi-eye</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="warning"
                        @click="editRole(item)"
                        :disabled="item.name === 'admin'"
                    >
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        size="small"
                        variant="text"
                        color="error"
                        @click="deleteRole(item)"
                        :disabled="item.name === 'admin' || item.name === 'user'"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>

        <!-- Create/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="600">
            <v-card>
                <v-card-title>
                    <v-icon class="mr-2">{{ editMode ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
                    {{ editMode ? 'Edit Role' : 'Create Role' }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-text-field
                        v-model="form.name"
                        label="Role Name"
                        variant="outlined"
                        class="mb-4"
                        :disabled="editMode && (form.name === 'admin' || form.name === 'user')"
                    ></v-text-field>

                    <div class="text-subtitle-2 mb-2">Permissions</div>
                    <v-chip-group
                        v-model="form.permissions"
                        multiple
                        column
                    >
                        <v-chip
                            v-for="permission in allPermissions"
                            :key="permission.id"
                            :value="permission.id"
                            filter
                            variant="outlined"
                        >
                            {{ permission.name }}
                        </v-chip>
                    </v-chip-group>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="dialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="saveRole" :loading="saving">
                        {{ editMode ? 'Update' : 'Create' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- View Dialog -->
        <v-dialog v-model="viewDialog" max-width="500">
            <v-card v-if="selectedRole">
                <v-card-title>
                    <v-icon class="mr-2">mdi-shield-account</v-icon>
                    {{ selectedRole.name }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <div class="text-subtitle-2 mb-2">Permissions ({{ selectedRole.permissions?.length || 0 }})</div>
                    <v-chip
                        v-for="(permission, index) in selectedRole.permissions"
                        :key="permission.id || index"
                        size="small"
                        class="ma-1"
                        variant="tonal"
                        color="primary"
                    >
                        {{ permission.name || permission }}
                    </v-chip>
                    <div v-if="!selectedRole.permissions?.length" class="text-grey">
                        No permissions assigned
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="viewDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import axios from 'axios'
import PageHeader from '../../components/PageHeader.vue'

const showSnackbar = inject('showSnackbar')

const roles = ref([])
const allPermissions = ref([])
const loading = ref(false)
const saving = ref(false)
const dialog = ref(false)
const viewDialog = ref(false)
const editMode = ref(false)
const selectedRole = ref(null)

const form = ref({
    id: null,
    name: '',
    permissions: [],
})

const headers = [
    { title: 'Name', key: 'name' },
    { title: 'Permissions', key: 'permissions', sortable: false },
    { title: 'Users', key: 'users_count', align: 'center' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

const fetchRoles = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/roles')
        roles.value = response.data.roles || []
        allPermissions.value = response.data.permissions || []
    } catch (error) {
        console.error('Error fetching roles:', error)
    } finally {
        loading.value = false
    }
}

const openCreateDialog = () => {
    editMode.value = false
    form.value = { id: null, name: '', permissions: [] }
    dialog.value = true
}

const editRole = (role) => {
    editMode.value = true
    form.value = {
        id: role.id,
        name: role.name,
        permissions: role.permissions?.map(p => p.id || p) || [],
    }
    dialog.value = true
}

const viewRole = (role) => {
    selectedRole.value = role
    viewDialog.value = true
}

const saveRole = async () => {
    saving.value = true
    try {
        if (editMode.value) {
            await axios.put(`/roles/${form.value.id}`, form.value)
            showSnackbar?.('Role updated successfully', 'success')
        } else {
            await axios.post('/roles', form.value)
            showSnackbar?.('Role created successfully', 'success')
        }
        dialog.value = false
        fetchRoles()
    } catch (error) {
        console.error('Error saving role:', error)
        showSnackbar?.('Failed to save role', 'error')
    } finally {
        saving.value = false
    }
}

const deleteRole = async (role) => {
    if (!confirm(`Are you sure you want to delete the role "${role.name}"?`)) return

    try {
        await axios.delete(`/roles/${role.id}`)
        showSnackbar?.('Role deleted successfully', 'success')
        fetchRoles()
    } catch (error) {
        console.error('Error deleting role:', error)
        showSnackbar?.('Failed to delete role', 'error')
    }
}

onMounted(() => {
    fetchRoles()
})
</script>
