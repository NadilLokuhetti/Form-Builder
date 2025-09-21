<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Forms Management</h2>
            <router-link to="/forms/create" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Form
            </router-link>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4" v-for="form in forms" :key="form.id">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ form.title }}</h5>
                        <p class="card-text">{{ form.fields_count }} field(s)</p>
                        <p class="card-text">
                            <span class="badge bg-success">{{ form.submissions_count }} submissions</span>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button class="btn btn-sm btn-info" @click="previewForm(form)">
                            <i class="fas fa-eye me-1"></i> Preview
                        </button>
                        <router-link :to="`/forms/${form.id}/edit`" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </router-link>
                        <router-link :to="`/forms/${form.id}/submissions`" class="btn btn-sm btn-warning">
                            <i class="fas fa-list me-1"></i> Submissions
                        </router-link>
                        <button class="btn btn-sm btn-danger" @click="deleteForm(form)">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FormList',
    data() {
        return {
            forms: []
        }
    },
    mounted() {
        this.fetchForms();
    },
    methods: {
        fetchForms() {
            axios.get('/forms')
                .then(response => {
                    this.forms = response.data;
                })
                .catch(error => {
                    console.error('Error fetching forms:', error);
                });
        },
        previewForm(form) {
            this.$router.push(`/forms/${form.id}/preview`);
        },
        deleteForm(form) {
            if (confirm(`Are you sure you want to delete "${form.title}"?`)) {
                axios.delete(`/forms/${form.id}`)
                    .then(() => {
                        this.fetchForms();
                    })
                    .catch(error => {
                        console.error('Error deleting form:', error);
                    });
            }
        }
    }
}
</script>