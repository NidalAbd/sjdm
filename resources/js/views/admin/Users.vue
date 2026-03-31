<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center flex-wrap ga-2">
                <v-icon class="mr-2">mdi-account-multiple</v-icon>
                <span>Manage Users</span>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="openCreateDialog" prepend-icon="mdi-plus">
                    Add User
                </v-btn>
            </v-card-title>
        </v-card>

        <!-- Stats Cards -->
        <v-row class="mb-4">
            <v-col cols="6" sm="3">
                <v-card color="info" variant="flat">
                    <v-card-text class="text-white text-center">
                        <div class="text-h4 font-weight-bold">{{ stats.totalUsers }}</div>
                        <div class="text-caption">Total Users</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6" sm="3">
                <v-card color="success" variant="flat">
                    <v-card-text class="text-white text-center">
                        <div class="text-h4 font-weight-bold">{{ stats.verifiedUsers }}</div>
                        <div class="text-caption">Verified</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6" sm="3">
                <v-card color="warning" variant="flat">
                    <v-card-text class="text-white text-center">
                        <div class="text-h4 font-weight-bold">{{ stats.unverifiedUsers }}</div>
                        <div class="text-caption">Unverified</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6" sm="3">
                <v-card color="purple" variant="flat">
                    <v-card-text class="text-white text-center">
                        <div class="text-h4 font-weight-bold">${{ formatNumber(stats.totalBalance) }}</div>
                        <div class="text-caption">Total Balance</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="3">
                        <v-text-field
                            v-model="filters.search"
                            label="Search users"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                            hide-details
                            @keyup.enter="fetchUsers"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.role"
                            :items="roleOptions"
                            label="Role"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchUsers"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-select
                            v-model="filters.status"
                            :items="statusOptions"
                            label="Status"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                            @update:model-value="fetchUsers"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                        <v-btn color="primary" block @click="fetchUsers" prepend-icon="mdi-magnify">
                            Search
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Users Table -->
        <v-card>
            <v-data-table-server
                v-model:items-per-page="itemsPerPage"
                v-model:page="page"
                :headers="headers"
                :items="users"
                :items-length="totalUsers"
                :loading="loading"
                class="elevation-0"
                @update:options="onTableUpdate"
            >
                <!-- ID Column -->
                <template v-slot:item.id="{ item }">
                    <span class="font-weight-bold">#{{ item.id }}</span>
                </template>

                <!-- User Column -->
                <template v-slot:item.user="{ item }">
                    <div class="d-flex align-center py-2">
                        <v-avatar size="40" class="mr-3" :color="item.avatar ? undefined : 'primary'">
                            <v-img v-if="item.avatar" :src="item.avatar"></v-img>
                            <span v-else class="text-white">{{ item.name?.charAt(0) || 'U' }}</span>
                        </v-avatar>
                        <div>
                            <div class="font-weight-medium">{{ item.name }}</div>
                            <div class="text-caption text-grey">{{ item.email }}</div>
                        </div>
                    </div>
                </template>

                <!-- Balance Column -->
                <template v-slot:item.balance="{ item }">
                    <span class="font-weight-bold text-success">${{ formatNumber(item.balance) }}</span>
                </template>

                <!-- Role Column -->
                <template v-slot:item.roles="{ item }">
                    <v-chip
                        v-for="role in item.roles"
                        :key="role.id || role.name || role"
                        :color="(role.name || role) === 'admin' ? 'error' : 'info'"
                        size="small"
                        class="mr-1"
                    >
                        {{ role.name || role }}
                    </v-chip>
                </template>

                <!-- Verified Column -->
                <template v-slot:item.email_verified_at="{ item }">
                    <v-icon :color="item.email_verified_at ? 'success' : 'error'">
                        {{ item.email_verified_at ? 'mdi-check-circle' : 'mdi-close-circle' }}
                    </v-icon>
                </template>

                <!-- Status Column -->
                <template v-slot:item.status="{ item }">
                    <v-chip
                        :color="item.status === 'banned' ? 'error' : 'success'"
                        size="small"
                        variant="flat"
                    >
                        {{ item.status === 'banned' ? 'Banned' : 'Active' }}
                    </v-chip>
                </template>

                <!-- Date Column -->
                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex ga-1">
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="info"
                            @click="viewUser(item)"
                        >
                            <v-icon>mdi-eye</v-icon>
                            <v-tooltip activator="parent" location="top">View</v-tooltip>
                        </v-btn>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="warning"
                            @click="editUser(item)"
                        >
                            <v-icon>mdi-pencil</v-icon>
                            <v-tooltip activator="parent" location="top">Edit</v-tooltip>
                        </v-btn>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="success"
                            @click="openBalanceDialog(item)"
                        >
                            <v-icon>mdi-cash-plus</v-icon>
                            <v-tooltip activator="parent" location="top">Add Balance</v-tooltip>
                        </v-btn>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            :color="item.status === 'banned' ? 'success' : 'error'"
                            @click="toggleBan(item)"
                        >
                            <v-icon>{{ item.status === 'banned' ? 'mdi-account-check' : 'mdi-account-cancel' }}</v-icon>
                            <v-tooltip activator="parent" location="top">
                                {{ item.status === 'banned' ? 'Unban' : 'Ban' }}
                            </v-tooltip>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- User Details Dialog -->
        <v-dialog v-model="viewDialog" max-width="700">
            <v-card v-if="selectedUser">
                <v-card-title class="bg-primary text-white">
                    <v-icon class="mr-2">mdi-account</v-icon>
                    User Details - #{{ selectedUser.id }}
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-row>
                        <v-col cols="12" class="text-center mb-4">
                            <v-avatar size="80" :color="selectedUser.avatar ? undefined : 'primary'">
                                <v-img v-if="selectedUser.avatar" :src="selectedUser.avatar"></v-img>
                                <span v-else class="text-h4 text-white">{{ selectedUser.name?.charAt(0) }}</span>
                            </v-avatar>
                            <div class="text-h5 mt-2">{{ selectedUser.name }}</div>
                            <div class="text-grey">{{ selectedUser.email }}</div>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <div class="text-h5 font-weight-bold text-success">
                                    ${{ formatNumber(selectedUser.balance) }}
                                </div>
                                <div class="text-caption text-grey">Balance</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <div class="text-h5 font-weight-bold">{{ selectedUser.orders_count || 0 }}</div>
                                <div class="text-caption text-grey">Orders</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6" md="4">
                            <v-card variant="outlined" class="pa-3 text-center">
                                <div class="text-h5 font-weight-bold">{{ selectedUser.referrals_count || 0 }}</div>
                                <div class="text-caption text-grey">Referrals</div>
                            </v-card>
                        </v-col>
                        <v-col cols="6" md="6">
                            <div class="text-caption text-grey">Roles</div>
                            <v-chip
                                v-for="role in selectedUser.roles"
                                :key="role.id || role.name || role"
                                :color="(role.name || role) === 'admin' ? 'error' : 'info'"
                                size="small"
                                class="mr-1 mt-1"
                            >
                                {{ role.name || role }}
                            </v-chip>
                        </v-col>
                        <v-col cols="6" md="6">
                            <div class="text-caption text-grey">Status</div>
                            <v-chip :color="selectedUser.status === 'banned' ? 'error' : 'success'" class="mt-1">
                                {{ selectedUser.status === 'banned' ? 'Banned' : 'Active' }}
                            </v-chip>
                            <v-chip
                                :color="selectedUser.email_verified_at ? 'success' : 'warning'"
                                class="mt-1 ml-1"
                            >
                                {{ selectedUser.email_verified_at ? 'Verified' : 'Unverified' }}
                            </v-chip>
                        </v-col>
                        <v-col cols="6">
                            <div class="text-caption text-grey">Registered</div>
                            <div>{{ formatDateTime(selectedUser.created_at) }}</div>
                        </v-col>
                        <v-col cols="6" v-if="selectedUser.referrer">
                            <div class="text-caption text-grey">Referred By</div>
                            <div>{{ selectedUser.referrer.name }}</div>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="viewDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Edit User Dialog -->
        <v-dialog v-model="editDialog" max-width="600">
            <v-card v-if="editForm">
                <v-card-title class="bg-warning text-white">
                    <v-icon class="mr-2">mdi-pencil</v-icon>
                    Edit User
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-text-field
                        v-model="editForm.name"
                        label="Name"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-text-field
                        v-model="editForm.email"
                        label="Email"
                        type="email"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-text-field
                        v-model="editForm.password"
                        label="New Password (leave blank to keep current)"
                        type="password"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-select
                        v-model="editForm.roles"
                        :items="availableRoles"
                        label="Roles"
                        variant="outlined"
                        multiple
                        chips
                    ></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="editDialog = false">Cancel</v-btn>
                    <v-btn color="warning" @click="updateUser" :loading="saving">
                        Save Changes
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Add Balance Dialog -->
        <v-dialog v-model="balanceDialog" max-width="500">
            <v-card v-if="selectedUser">
                <v-card-title class="bg-success text-white">
                    <v-icon class="mr-2">mdi-cash-plus</v-icon>
                    Add Balance
                </v-card-title>
                <v-card-text class="pa-4">
                    <div class="mb-4">
                        <div class="text-caption text-grey">User</div>
                        <div class="font-weight-bold">{{ selectedUser.name }}</div>
                        <div class="text-grey">{{ selectedUser.email }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-caption text-grey">Current Balance</div>
                        <div class="text-h5 font-weight-bold text-success">${{ formatNumber(selectedUser.balance) }}</div>
                    </div>
                    <v-text-field
                        v-model="balanceAmount"
                        label="Amount to Add"
                        type="number"
                        step="0.01"
                        min="0"
                        prefix="$"
                        variant="outlined"
                    ></v-text-field>
                    <v-text-field
                        v-model="balanceNote"
                        label="Note (optional)"
                        variant="outlined"
                        class="mt-4"
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="balanceDialog = false">Cancel</v-btn>
                    <v-btn color="success" @click="addBalance" :loading="saving">
                        Add Balance
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Create User Dialog -->
        <v-dialog v-model="createDialog" max-width="600">
            <v-card>
                <v-card-title class="bg-primary text-white">
                    <v-icon class="mr-2">mdi-account-plus</v-icon>
                    Create User
                </v-card-title>
                <v-card-text class="pa-4">
                    <v-text-field
                        v-model="createForm.name"
                        label="Name"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-text-field
                        v-model="createForm.email"
                        label="Email"
                        type="email"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-text-field
                        v-model="createForm.password"
                        label="Password"
                        type="password"
                        variant="outlined"
                        class="mb-4"
                    ></v-text-field>
                    <v-select
                        v-model="createForm.roles"
                        :items="availableRoles"
                        label="Roles"
                        variant="outlined"
                        multiple
                        chips
                    ></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="createDialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="createUser" :loading="saving">
                        Create User
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

// Data
const users = ref([])
const totalUsers = ref(0)
const loading = ref(false)
const saving = ref(false)
const page = ref(1)
const itemsPerPage = ref(15)

const stats = ref({
    totalUsers: 0,
    verifiedUsers: 0,
    unverifiedUsers: 0,
    totalBalance: 0,
})

// Filters
const filters = ref({
    search: '',
    role: null,
    status: null,
})

const roleOptions = [
    { title: 'All', value: null },
    { title: 'Admin', value: 'admin' },
    { title: 'User', value: 'user' },
]

const statusOptions = [
    { title: 'All', value: null },
    { title: 'Active', value: 'active' },
    { title: 'Banned', value: 'banned' },
    { title: 'Verified', value: 'verified' },
    { title: 'Unverified', value: 'unverified' },
]

const availableRoles = ['admin', 'user']

// Dialogs
const viewDialog = ref(false)
const editDialog = ref(false)
const balanceDialog = ref(false)
const createDialog = ref(false)
const selectedUser = ref(null)

const editForm = ref(null)
const createForm = ref({
    name: '',
    email: '',
    password: '',
    roles: ['user'],
})

const balanceAmount = ref(0)
const balanceNote = ref('')

// Table headers
const headers = [
    { title: '#', key: 'id', width: '80px' },
    { title: 'User', key: 'user', sortable: false },
    { title: 'Balance', key: 'balance', align: 'end' },
    { title: 'Role', key: 'roles', sortable: false },
    { title: 'Verified', key: 'email_verified_at', align: 'center' },
    { title: 'Status', key: 'status' },
    { title: 'Registered', key: 'created_at' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

// Methods
const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (date) => {
    return new Date(date).toLocaleString()
}

const fetchUsers = async () => {
    loading.value = true
    try {
        const params = {
            page: page.value,
            per_page: itemsPerPage.value,
            search: filters.value.search,
            role: filters.value.role,
            status: filters.value.status,
        }

        const response = await axios.get('/api/admin/users', { params })
        users.value = response.data.users || []
        totalUsers.value = response.data.pagination?.total || 0

        if (response.data.stats) {
            stats.value = {
                totalUsers: response.data.stats.total || 0,
                verifiedUsers: response.data.stats.verified || 0,
                unverifiedUsers: response.data.stats.unverified || 0,
                totalBalance: response.data.stats.total_balance || 0,
            }
        }
    } catch (error) {
        console.error('Error fetching users:', error)
        showSnackbar?.('Failed to load users', 'error')
    } finally {
        loading.value = false
    }
}

const onTableUpdate = ({ page: newPage, itemsPerPage: newItemsPerPage }) => {
    page.value = newPage
    itemsPerPage.value = newItemsPerPage
    fetchUsers()
}

const viewUser = (user) => {
    selectedUser.value = user
    viewDialog.value = true
}

const editUser = (user) => {
    selectedUser.value = user
    // Extract role names from role objects
    const roleNames = (user.roles || []).map(r => r.name || r)
    editForm.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: '',
        roles: roleNames,
    }
    editDialog.value = true
}

const updateUser = async () => {
    if (!editForm.value) return

    saving.value = true
    try {
        const data = {
            name: editForm.value.name,
            email: editForm.value.email,
            roles: editForm.value.roles,
        }
        if (editForm.value.password) {
            data.password = editForm.value.password
        }

        await axios.put(`/api/admin/users/${editForm.value.id}`, data)
        showSnackbar?.('User updated successfully', 'success')
        editDialog.value = false
        fetchUsers()
    } catch (error) {
        console.error('Error updating user:', error)
        showSnackbar?.('Failed to update user', 'error')
    } finally {
        saving.value = false
    }
}

const openBalanceDialog = (user) => {
    selectedUser.value = user
    balanceAmount.value = 0
    balanceNote.value = ''
    balanceDialog.value = true
}

const addBalance = async () => {
    if (!selectedUser.value || balanceAmount.value <= 0) return

    saving.value = true
    try {
        await axios.post(`/api/admin/users/${selectedUser.value.id}/add-balance`, {
            amount: balanceAmount.value,
            description: balanceNote.value,
        })
        showSnackbar?.('Balance added successfully', 'success')
        balanceDialog.value = false
        fetchUsers()
    } catch (error) {
        console.error('Error adding balance:', error)
        showSnackbar?.('Failed to add balance', 'error')
    } finally {
        saving.value = false
    }
}

const toggleBan = async (user) => {
    const action = user.status === 'banned' ? 'unban' : 'ban'
    if (!confirm(`Are you sure you want to ${action} this user?`)) return

    try {
        await axios.post(`/api/admin/users/${user.id}/toggle-ban`)
        showSnackbar?.(`User ${action}ned successfully`, 'success')
        fetchUsers()
    } catch (error) {
        console.error(`Error ${action}ning user:`, error)
        showSnackbar?.(`Failed to ${action} user`, 'error')
    }
}

const openCreateDialog = () => {
    createForm.value = {
        name: '',
        email: '',
        password: '',
        roles: ['user'],
    }
    createDialog.value = true
}

const createUser = async () => {
    if (!createForm.value.name || !createForm.value.email || !createForm.value.password) {
        showSnackbar?.('Please fill in all required fields', 'error')
        return
    }

    saving.value = true
    try {
        await axios.post('/api/admin/users', createForm.value)
        showSnackbar?.('User created successfully', 'success')
        createDialog.value = false
        fetchUsers()
    } catch (error) {
        console.error('Error creating user:', error)
        showSnackbar?.('Failed to create user', 'error')
    } finally {
        saving.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchUsers()
})
</script>
