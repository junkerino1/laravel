<template>
    <div v-if="$can('create product')">
        <form @submit.prevent="createProduct">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label class="col-form-label mt-2" for="product_name">Name</label>
                        <input type="text" id="product_name" v-model="product.product_name" class="form-control"
                            required />
                    </div>
                    <div class="col">
                        <label class="col-form-label mt-2" for="description">Description</label>
                        <textarea id="description" v-model="product.description" rows="5" class="form-control">
                    </textarea>
                    </div>
                    <div class="col">
                        <label class="col-form-label mt-2" for="category">Category</label>
                        <input type="text" id="category" class="form-control" :value="category.category_name"
                            readonly />
                    </div>
                    <div class="col">
                        <label class="col-form-label mt-2" for="price">Price</label>
                        <input type="number" id="price" v-model="product.price" step="any" class="form-control"
                            required />
                    </div>
                    <div class="col">
                        <label class="col-form-label mt-2" for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" v-model="product.stock_quantity" class="form-control"
                            required />
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col align-self-end">
                        <div class="mt-5">
                            <button type="submit" class="btn btn-success mr-2">Save</button>
                            <button type="button" class="btn btn-danger" @click="closeForm">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
import axios from 'axios';

export default {
    props: {
        category: {
            type: Object,
        }
    },
    data() {
        return {
            product: {
                product_name: '',
                description: '',
                price: null,
                stock_quantity: null,
                category_id: this.category.id,
            }
        };
    },
    methods: {
        async createProduct() {
            try {
                const url = 'product/create';
                console.log(this.product);
                const response = await axios.post(url, this.product);
                if (response.status === 200) {
                    alert('Product created successfully!');
                    this.$emit('cancel');
                    this.$emit('refresh');
                }
            } catch (error) {
                console.error('Error creating product:', error);
                alert('Failed to create product. Please try again.');
            }
        },
        closeForm() {
            this.$emit('cancel');
        }
    }
};
</script>
