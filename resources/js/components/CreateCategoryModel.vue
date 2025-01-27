<template>
    <form @submit.prevent="createCategory">
        <div class="container">
            <div class="row">
                <div class="col">
                    <label class="col-form-label mt-2" for="category_name">Category Name</label>
                    <input type="text" id="category_name" v-model="category.category_name" class="form-control"
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
</template>
<script>
import axios from 'axios';

export default {
    data() {
        return {
            category: {
                category_name: "",
            }
        };
    },
    methods: {
        async createCategory() {
            try {
                const url = '/category/create';
                console.log(this.category);
                const response = await axios.post(url, this.category);
                if (response.status === 200) {
                    alert('Category created successfully!');
                    this.$emit('cancel');
                    this.$emit('refresh');
                }
            } catch (error) {
                console.error('Error creating product:', error);
                alert('Failed to create category. Please try again.');
            }
        },
        closeForm() {
            this.$emit('cancel');
        }
    }
};
</script>
