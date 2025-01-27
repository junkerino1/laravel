<template>
    <DataTable :value="products" sortMode="multiple" scrollable scrollHeight="400px" tableStyle="min-width: 47rem">
        <Column field="product_name" header="Name" sortable
            style="width: 15%; text-align: center; vertical-align: middle;"></Column>
        <Column field="description" header="Description" sortable
            style="width: 20%; text-align: center; vertical-align: middle;"></Column>
        <Column field="price" header="Price (RM)" sortable
            style="width: 15%; text-align: center; vertical-align: middle;"></Column>
        <Column field="stock_quantity" header="Quantity" sortable
            style="width: 15%; text-align: center; vertical-align: middle;"></Column>
        <Column header="Action" style="width:30%;">
            <template v-slot:body="slotProps">
                <div class="action">
                    <button class="btn btn-success m-2" @click="editProduct(slotProps.data)">Edit</button>
                    <button class="btn btn-danger m-2" @click="deleteProduct(slotProps.data)">Delete</button>
                </div>
            </template>
        </Column>
    </DataTable>
    <div>
        <button class="btn btn-danger m-2" @click="hideList()">Close</button>
    </div>

    <div v-if="edit" class="popup">
        <div class="popup-inner">
            <EditProduct :product="product" @close="hideEdit()"></EditProduct>
        </div>
    </div>

</template>

<script>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import EditProduct from './form/EditProduct.vue';

export default {
    props: {
        category: {
            type: Array,
            required: true
        }
    },
    components: {
        DataTable,
        Column,
        EditProduct,
    },
    data() {
        return {
            products: [],
            edit: false,
        }
    },
    methods: {
        async getProduct() {
            {
                try {
                    console.log(this.category.id);
                    const response = await axios.get(`/product/${this.category.id}`);
                    this.products = response.data;
                } catch (error) {
                    console.error("Error fetching products:", error);
                }
            }
        },
        editProduct(product) {
            this.edit = true;
            this.product = product;
            console.log('console:', this.product);
        },
        hideList() {
            this.$emit("cancel");
        },
        hideEdit() {
            this.edit = false;
        },
    },

    created() {
        this.getProduct();
    },
};
</script>

<style>
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
    width: 80%;
    max-width: 700px;
}
</style>
