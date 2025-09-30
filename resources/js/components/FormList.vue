<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Forms Management</h2>
            <router-link to="/forms/create" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Form
            </router-link>
        </div>

        <div v-if="loading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div v-else>
            <div v-if="forms.length === 0" class="text-center py-5 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>No Forms Created Yet</h5>
                <p>Get started by creating your first form!</p>
                <router-link to="/forms/create" class="btn btn-primary">
                    Create Your First Form
                </router-link>
            </div>

            <div v-else class="row">
                <div class="col-md-6 col-lg-4 mb-4" v-for="form in forms" :key="form.id">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ form.title }}</h5>
                            <p class="card-text text-muted">
                                <small>{{ form.fields_count || 0 }} field(s)</small>
                            </p>
                            <p class="card-text">
                                <span class="badge" :class="form.submissions_count > 0 ? 'bg-success' : 'bg-secondary'">
                                    {{ form.submissions_count || 0 }} submissions
                                </span>
                            </p>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-sm btn-outline-info" @click="previewForm(form)" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <router-link :to="`/forms/${form.id}/edit`" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </router-link>
                                <router-link :to="`/forms/${form.id}/submissions`" class="btn btn-sm btn-outline-warning" title="Submissions">
                                    <i class="fas fa-list"></i>
                                </router-link>
                                <button class="btn btn-sm btn-outline-danger" @click="deleteForm(form)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
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
            forms: [],
            loading: true
        }
    },
    mounted() {
        this.fetchForms();
    },
    methods: {
    fetchForms() {
        this.loading = true;
        // Remove /api prefix since baseURL already has it
        axios.get('/forms')  // Changed from '/api/forms'
            .then(response => {
                this.forms = response.data;
            })
            .catch(error => {
                console.error('Error fetching forms:', error);
                alert('Error loading forms');
            })
            .finally(() => {
                this.loading = false;
            });
    },
    deleteForm(form) {
        if (confirm(`Are you sure you want to delete "${form.title}"? This action cannot be undone.`)) {
            axios.delete(`/forms/${form.id}`)  // Changed from '/api/forms/${form.id}'
                .then(() => {
                    this.fetchForms();
                })
                .catch(error => {
                    console.error('Error deleting form:', error);
                    alert('Error deleting form');
                });
        }
    }
}
}
</script>