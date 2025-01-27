<template>
    <div class="container">
        <div v-if="alertBox">
            <div class="alert alert-success">
                Permissions updated successfully!
            </div>
        </div>
        <form @submit.prevent="updateRolePermissions">
            <div>
                <label class="col-form-label mt-2" for="user">Select Role:</label>
                <select class="form-select" style="width:20rem" v-model="rolePermission.role" required>
                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
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
            roles: [],
            permissions: [],
            rolePermission: {
                role: null,
                permissions: [],
            },
        };
    },
    methods: {
        async fetchPermission() {
            try {
                const url = '/permission/role';
                const response = await axios.get(url);
                if (response.data) {
                    this.permissions = response.data.permissions;
                    this.roles = response.data.roles;
                }
            } catch (error) {
                console.error('Error fetching permissions and roles:', error);
            }
        },
        async fetchRolePermissions(roleId) {
            try {
                const url = `/permission/role/${roleId}`;
                const response = await axios.get(url);
                this.rolePermission.permissions = response.data.permissions;
            } catch (error) {
                console.error('Error fetching role permissions:', error);
            }
        },
        async updateRolePermissions() {
            try {
                const url = '/permission/update/role';
                const payload = this.rolePermission;
                await axios.post(url, payload);
                this.alertBox = true;
                setTimeout(() => (this.alertBox = false), 3000);
            } catch (error) {
                console.error('Error updating role permissions:', error);
            }
        },
    },
    watch: {
        'rolePermission.role'(newRoleId) {
            if (newRoleId) {
                this.fetchRolePermissions(newRoleId);
            }
        },
    },
    mounted() {
        this.fetchPermission();
    },
};
</script>
