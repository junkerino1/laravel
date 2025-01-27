<template>
    <div class="container">
        <DataTable :value="categories" scrollHeight="550px">
            <Column field="category_name" header="Category" sortable style="width: 30%;"></Column>
            <Column field="category_count" header="Count" sortable style="width: 20%;"></Column>
            <Column header="Actions" style="width: 50%;">
                <template v-slot:body="slotProps">
                    <button v-if="$can('view product')" class="btn btn-primary m-2" @click="showList(slotProps.data)">
                        View
                    </button>
                    <button v-if="$can('view product')" class="btn btn-success m-2"
                        @click="showCreate(slotProps.data)" @refresh="getData">
                        Create
                    </button>
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Popup for ProductsList -->
    <div v-if="listPopup" class="popup">
        <div class="popup-inner">
            <ProductsList :category="category" @cancel="hideList" @refresh="getData" />
        </div>
    </div>

    <!-- Popup for CreateProductModel -->
    <div v-if="createPopup" class="popup">
        <div class="popup-inner">
            <CreateProductModel :category="category" @cancel="hideCreate" @refresh="getData" />
        </div>
    </div>
</template>

<script>
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ProductsList from "./ProductsList.vue";
import CreateProductModel from "./CreateProductModel.vue";
import { useAbility } from '@casl/vue';
import axios from 'axios';

export default {
    components: {
        DataTable,
        Column,
        ProductsList,
        CreateProductModel,
    },
    data() {
        return {
            listPopup: false,
            createPopup: false,
            categories: [],
            category: null,
            componentKey: 0,
        };
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await axios.get("/category");
                this.categories = response.data.categories;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        showList(category) {
            this.category = category;
            this.listPopup = true;
        },
        hideList() {
            this.listPopup = false;
        },
        showCreate(category) {
            this.category = category;
            this.createPopup = true;
        },
        hideCreate() {
            this.createPopup = false;
        },
    },
    created() {
        this.fetchCategories();
    },
    mounted() {
        document.title = "Category List";
    },
};
</script>

<style scoped>
.container {
    margin-top: 20px;
}

.popup {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.popup .popup-inner {
    background: rgb(230, 230, 230);
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 900px;
}

button {
    text-align: center;
}

.p-column-title {
    font-weight: bold;
}

.p-header {
    text-align: center;
    vertical-align: middle;
}
</style>
