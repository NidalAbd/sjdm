import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        permissions: [],
        roles: [],
        _isAdmin: false,
        loading: false,
        initialized: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        isAdmin: (state) => state.roles.includes('admin') || state._isAdmin === true,
        hasPermission: (state) => (permission) => state.permissions.includes(permission),
        hasRole: (state) => (role) => state.roles.includes(role),
        userName: (state) => state.user?.name || 'Guest',
        userEmail: (state) => state.user?.email || '',
        userBalance: (state) => state.user?.balance || 0,
        userAvatar: (state) => state.user?.avatar || null,
    },

    actions: {
        async fetchUser(force = false) {
            if (this.initialized && !force) return this.user

            this.loading = true
            try {
                const response = await axios.get('/api/user')
                this.user = response.data.user
                this.permissions = response.data.permissions || []
                this.roles = response.data.roles || []
                this._isAdmin = response.data.is_admin || false
                this.initialized = true
                console.log('Auth store loaded:', { roles: this.roles, isAdmin: this._isAdmin })
                return this.user
            } catch (error) {
                console.error('Error fetching user:', error)
                this.user = null
                this.permissions = []
                this.roles = []
                this._isAdmin = false
                return null
            } finally {
                this.loading = false
            }
        },

        async updateProfile(profileData) {
            try {
                const response = await axios.put('/api/user/profile', profileData)
                this.user = { ...this.user, ...response.data.user }
                return { success: true, message: 'Profile updated successfully' }
            } catch (error) {
                console.error('Error updating profile:', error)
                return { success: false, message: error.response?.data?.message || 'Failed to update profile' }
            }
        },

        async updatePassword(passwordData) {
            try {
                await axios.put('/api/user/password', passwordData)
                return { success: true, message: 'Password updated successfully' }
            } catch (error) {
                console.error('Error updating password:', error)
                return { success: false, message: error.response?.data?.message || 'Failed to update password' }
            }
        },

        async logout() {
            try {
                await axios.post('/logout')
                this.clearUser()
                return true
            } catch (error) {
                console.error('Error during logout:', error)
                return false
            }
        },

        clearUser() {
            this.user = null
            this.permissions = []
            this.roles = []
            this._isAdmin = false
            this.initialized = false
        },

        setUser(user) {
            this.user = user
            this.initialized = true
        },

        updateBalance(newBalance) {
            if (this.user) {
                this.user.balance = newBalance
            }
        },

        // Check if user can perform action
        can(permission) {
            if (this.isAdmin) return true
            return this.permissions.includes(permission)
        },

        // Check multiple permissions (any)
        canAny(permissions) {
            if (this.isAdmin) return true
            return permissions.some(p => this.permissions.includes(p))
        },

        // Check multiple permissions (all)
        canAll(permissions) {
            if (this.isAdmin) return true
            return permissions.every(p => this.permissions.includes(p))
        }
    }
})
