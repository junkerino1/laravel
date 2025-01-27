<template>
    <div class="container">
        <div v-if="alertBox">
            <div class="alert alert-success">
                Permissions updated successfully!
            </div>
        </div>
        <form @submit.prevent="updateUserPermissions">
            <div>
                <label class="col-form-label mt-2" for="user">Select User:</label>
                <select class="form-select" style="width:20rem" v-model="rolePermission.user" required>
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.username }}</option>
                </select>
            </div>

            <div>
                <label class="col-form-label">Select Permissions:</label>
                <div v-for="permission in permissions" :key="permission.permission" class="form-switch">
                    <label>
                        <input class="form-check-input" type="checkbox" v-model="rolePermission.permissions"
                            :value="permission.permission">
                            <span class="mx-3 my-5">{{ permission.permission }}</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="btn btn-success mt-3">Update Permissions</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            alertBox: false,
            users: [],
            permissions: [],
            rolePermission: {
                user: null,
                permissions: [],
            },
        };
    },
    methods: {
        async fetchPermission() {
            try {
                const url = '/permission/user';
                const response = await axios.get(url);
                this.permissions = response.data.permissions;
                this.users = response.data.users;
            } catch (error) {
                console.error('Error fetching permissions and users:', error);
            }
        },
        async fetchUserPermissions(userId) {
            try {
                const url = `/permission/user/${userId}`;
                const response = await axios.get(url);
                this.rolePermission.permissions = response.data.permissions;
            } catch (error) {
                console.error('Error fetching user permissions:', error);
            }
        },
        async updateUserPermissions() {
            try {
                const url = '/permission/update/user';
                const payload = this.rolePermission;
                console.log(payload);
                await axios.post(url, payload);
                this.alertBox = true;
                setTimeout(() => (this.alertBox = false), 3000);
            } catch (error) {
                console.error('Error updating user permissions:', error);
            }
        },
    },
    watch: {
        'rolePermission.user'(newUserId) {
            if (newUserId) {
                this.fetchUserPermissions(newUserId);
            }
        },
    },
    mounted() {
        this.fetchPermission();
    },
};
</script>
