<template>
    <div class="container">
        <form @submit.prevent="updateProduct">
            <div class="container">
                <div class="row">
                    <label class="col-form-label mt-2" for="product_name">Name:</label>
                    <input
                        type="text"
                        id="product_name"
                        v-model="updatedProduct.product_name"
                        class="form-control"
                        required
                    />
                </div>
                <div class="row">
                    <label class="col-form-label mt-2" for="description">Description:</label>
                    <textarea
                        id="description"
                        v-model="updatedProduct.description"
                        rows="5"
                        class="form-control"
                    ></textarea>
                </div>
                <div class="row">
                    <label class="col-form-label mt-2" for="price">Price:</label>
                    <input
                        type="number"
                        id="price"
                        v-model="updatedProduct.price"
                        step="any"
                        class="form-control"
                        required
                    />
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col align-self-end">
                    <div class="mt-5">
                        <button type="submit" class="btn btn-success col">Save</button>
                        <button type="button" class="btn btn-danger col" @click="closeForm">Cancel</button>
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
        product: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            updatedProduct: {
                product_name: '',
                description: '',
                price: 0,
            },
        };
    },
    watch: {
        product: {
            immediate: true,
            handler(newProduct) {
                this.updatedProduct = {
                    product_name: newProduct.product_name,
                    description: newProduct.description,
                    price: newProduct.price,
                };
            },
        },
    },
    methods: {
        async updateProduct() {
            try {
                console.log('Data:', this.updatedProduct);
                const response = await axios.post(`/product/edit/${this.product.id}`, this.updatedProduct);
                console.log('response: ', response);
                if (response.status === 200) {
                    console.log('Updated successfully');
                    this.$emit('close');
                    this.$emit('successful');
                    this.$emit('refresh');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update the product.');
            }
        },
        closeForm() {
            this.$emit('close');
        },
    },
};
</script>
