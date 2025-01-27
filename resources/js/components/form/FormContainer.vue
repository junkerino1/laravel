<template>

    <TabView>
        <TabPanel header="Role">
            <RolePermission></RolePermission>
        </TabPanel>

        <TabPanel header="User">
            <UserPermission></UserPermission>
        </TabPanel>

        <TabPanel header="Create">
            <CreatePermission></CreatePermission>
        </TabPanel>

    </TabView>

</template>

<script>
import axios from "axios";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import RolePermission from "./RolePermission.vue";
import UserPermission from "./UserPermission.vue";
import CreatePermission from "./CreatePermission.vue";

export default {
    components: {
        TabView,
        TabPanel,
        RolePermission,
        UserPermission,
        CreatePermission,
    },
    data() {
        return {

        }
    },
    methods: {

    },
    created() {

    },
    mounted() {

    }
};
</script>

<style>
.p-tabview-nav-link {
    text-decoration: none !important;
}
</style>
