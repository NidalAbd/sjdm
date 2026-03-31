<template>
    <div>
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-cog</v-icon>
                Profile Settings
            </v-card-title>
        </v-card>

        <v-row>
            <!-- Profile Information -->
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title class="d-flex align-center">
                        <v-icon class="mr-2" size="small">mdi-account</v-icon>
                        Profile Information
                    </v-card-title>
                    <v-card-text>
                        <div class="text-center mb-6">
                            <v-avatar size="100" :color="authStore.userAvatar ? undefined : 'primary'">
                                <v-img v-if="authStore.userAvatar" :src="authStore.userAvatar"></v-img>
                                <span v-else class="text-h3 text-white">{{ authStore.userName?.charAt(0) }}</span>
                            </v-avatar>
                            <v-btn
                                class="mt-3"
                                variant="outlined"
                                size="small"
                                @click="$refs.avatarInput.click()"
                            >
                                Change Avatar
                            </v-btn>
                            <input
                                ref="avatarInput"
                                type="file"
                                accept="image/*"
                                hidden
                                @change="uploadAvatar"
                            />
                        </div>

                        <v-text-field
                            v-model="profileForm.name"
                            label="Name"
                            variant="outlined"
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-model="profileForm.email"
                            label="Email"
                            type="email"
                            variant="outlined"
                            class="mb-4"
                            disabled
                        ></v-text-field>

                        <v-btn
                            color="primary"
                            @click="updateProfile"
                            :loading="savingProfile"
                            block
                        >
                            Save Changes
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Change Password -->
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title class="d-flex align-center">
                        <v-icon class="mr-2" size="small">mdi-lock</v-icon>
                        Change Password
                    </v-card-title>
                    <v-card-text>
                        <v-text-field
                            v-model="passwordForm.current_password"
                            label="Current Password"
                            type="password"
                            variant="outlined"
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-model="passwordForm.password"
                            label="New Password"
                            type="password"
                            variant="outlined"
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-model="passwordForm.password_confirmation"
                            label="Confirm New Password"
                            type="password"
                            variant="outlined"
                            class="mb-4"
                        ></v-text-field>

                        <v-btn
                            color="warning"
                            @click="updatePassword"
                            :loading="savingPassword"
                            block
                        >
                            Update Password
                        </v-btn>
                    </v-card-text>
                </v-card>

                <!-- Account Info -->
                <v-card class="mt-4">
                    <v-card-title class="d-flex align-center">
                        <v-icon class="mr-2" size="small">mdi-information</v-icon>
                        Account Information
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-wallet</v-icon>
                                </template>
                                <v-list-item-title>Balance</v-list-item-title>
                                <template v-slot:append>
                                    <span class="font-weight-bold text-success">
                                        ${{ authStore.userBalance?.toFixed(2) || '0.00' }}
                                    </span>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-shield-account</v-icon>
                                </template>
                                <v-list-item-title>Role</v-list-item-title>
                                <template v-slot:append>
                                    <v-chip
                                        :color="authStore.isAdmin ? 'error' : 'info'"
                                        size="small"
                                    >
                                        {{ authStore.isAdmin ? 'Admin' : 'User' }}
                                    </v-chip>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-email-check</v-icon>
                                </template>
                                <v-list-item-title>Email Verified</v-list-item-title>
                                <template v-slot:append>
                                    <v-icon :color="authStore.user?.email_verified_at ? 'success' : 'error'">
                                        {{ authStore.user?.email_verified_at ? 'mdi-check-circle' : 'mdi-close-circle' }}
                                    </v-icon>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-calendar</v-icon>
                                </template>
                                <v-list-item-title>Member Since</v-list-item-title>
                                <template v-slot:append>
                                    <span class="text-grey">
                                        {{ formatDate(authStore.user?.created_at) }}
                                    </span>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Referral Section -->
        <v-card class="mt-4">
            <v-card-title class="d-flex align-center">
                <v-icon class="mr-2" size="small">mdi-account-group</v-icon>
                Referral Program
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 text-grey mb-2">Your Referral Link</div>
                        <v-text-field
                            :model-value="referralLink"
                            variant="outlined"
                            readonly
                            density="compact"
                        >
                            <template v-slot:append-inner>
                                <v-btn
                                    icon
                                    size="small"
                                    variant="text"
                                    @click="copyReferralLink"
                                >
                                    <v-icon>mdi-content-copy</v-icon>
                                </v-btn>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 text-grey mb-2">Your Referral Code</div>
                        <v-text-field
                            :model-value="authStore.user?.referral_code || 'N/A'"
                            variant="outlined"
                            readonly
                            density="compact"
                        >
                            <template v-slot:append-inner>
                                <v-btn
                                    icon
                                    size="small"
                                    variant="text"
                                    @click="copyReferralCode"
                                >
                                    <v-icon>mdi-content-copy</v-icon>
                                </v-btn>
                            </template>
                        </v-text-field>
                    </v-col>
                </v-row>
                <v-alert type="info" variant="tonal" class="mt-4">
                    <div class="font-weight-bold">Earn rewards by referring friends!</div>
                    <div>Share your referral link and earn a bonus when your friends sign up and make their first purchase.</div>
                </v-alert>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const showSnackbar = inject('showSnackbar')

// Data
const savingProfile = ref(false)
const savingPassword = ref(false)

const profileForm = ref({
    name: '',
    email: '',
})

const passwordForm = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const referralLink = computed(() => {
    const code = authStore.user?.referral_code
    if (!code) return 'N/A'
    return `${window.location.origin}/register?ref=${code}`
})

// Methods
const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString()
}

const updateProfile = async () => {
    savingProfile.value = true
    try {
        const result = await authStore.updateProfile(profileForm.value)
        if (result.success) {
            showSnackbar?.(result.message, 'success')
        } else {
            showSnackbar?.(result.message, 'error')
        }
    } catch (error) {
        console.error('Error updating profile:', error)
        showSnackbar?.('Failed to update profile', 'error')
    } finally {
        savingProfile.value = false
    }
}

const updatePassword = async () => {
    if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
        showSnackbar?.('Passwords do not match', 'error')
        return
    }

    savingPassword.value = true
    try {
        const result = await authStore.updatePassword(passwordForm.value)
        if (result.success) {
            showSnackbar?.(result.message, 'success')
            passwordForm.value = {
                current_password: '',
                password: '',
                password_confirmation: '',
            }
        } else {
            showSnackbar?.(result.message, 'error')
        }
    } catch (error) {
        console.error('Error updating password:', error)
        showSnackbar?.('Failed to update password', 'error')
    } finally {
        savingPassword.value = false
    }
}

const uploadAvatar = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    const formData = new FormData()
    formData.append('avatar', file)

    try {
        const response = await axios.post('/api/profile/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        await authStore.fetchUser(true) // Force refresh
        showSnackbar?.('Avatar updated successfully', 'success')
    } catch (error) {
        console.error('Error uploading avatar:', error)
        showSnackbar?.('Failed to upload avatar', 'error')
    }
}

const copyReferralLink = () => {
    navigator.clipboard.writeText(referralLink.value)
    showSnackbar?.('Referral link copied!', 'success')
}

const copyReferralCode = () => {
    navigator.clipboard.writeText(authStore.user?.referral_code || '')
    showSnackbar?.('Referral code copied!', 'success')
}

// Lifecycle
onMounted(() => {
    if (authStore.user) {
        profileForm.value = {
            name: authStore.user.name,
            email: authStore.user.email,
        }
    }
})
</script>
