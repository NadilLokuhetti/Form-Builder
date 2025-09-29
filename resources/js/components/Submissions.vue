<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">
                <i class="fas fa-list me-2"></i>
                Form Submissions: {{ formTitle }}
            </h2>
            <div class="d-flex gap-2">
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search submissions..." v-model="submissionSearch">
                </div>
                <button class="btn btn-secondary" @click="$router.push('/forms')">
                    <i class="fas fa-arrow-left me-1"></i> Back to Forms
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-database me-2"></i>
                            {{ filteredSubmissions.length }} submission(s)
                        </h5>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-primary" @click="refreshSubmissions" :disabled="loading">
                            <i class="fas fa-sync-alt" :class="{'fa-spin': loading}"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading submissions...</p>
                </div>
                
                <div v-else-if="submissions.length === 0" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <h5>No Submissions Yet</h5>
                    <p>This form hasn't received any submissions yet.</p>
                    <router-link :to="`/forms/${id}/preview`" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Preview Form
                    </router-link>
                </div>
                
                <div v-else-if="filteredSubmissions.length === 0" class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>No Matching Submissions</h5>
                    <p>No submissions found matching your search criteria.</p>
                    <button class="btn btn-outline-secondary" @click="submissionSearch = ''">
                        Clear Search
                    </button>
                </div>
                
                <div v-else>
                    <div v-for="submission in filteredSubmissions" :key="submission.id" 
                         class="submission-card card mb-3 hover-shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    Submission #{{ submission.id }}
                                </h5>
                                <small class="text-muted">
                                    {{ formatDate(submission.created_at) }}
                                </small>
                            </div>
                            
                            <div class="row g-3">
                                <div v-for="(value, key, index) in submission.data" :key="key" 
                                     class="col-md-6" v-if="index < 4">
                                    <strong class="text-primary">{{ key }}:</strong>
                                    <span class="ms-2">
                                        {{ Array.isArray(value) ? value.join(', ') : value || 'N/A' }}
                                    </span>
                                </div>
                                
                                <div v-if="Object.keys(submission.data).length > 4" class="col-12">
                                    <small class="text-muted">
                                        +{{ Object.keys(submission.data).length - 4 }} more fields
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-globe me-1"></i>
                                        IP: {{ submission.ip_address }}
                                    </small>
                                    <button class="btn btn-sm btn-info" @click="viewSubmissionDetails(submission)">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        View Details
                                    </button>
                                </div>
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
    name: 'Submissions',
    props: ['id'],
    data() {
        return {
            submissions: [],
            formTitle: '',
            submissionSearch: '',
            loading: true
        }
    },
    computed: {
        filteredSubmissions() {
            if (!this.submissionSearch.trim()) {
                return this.submissions;
            }
            
            const searchTerm = this.submissionSearch.toLowerCase();
            return this.submissions.filter(submission => {
                return Object.values(submission.data).some(value => 
                    String(value).toLowerCase().includes(searchTerm)
                ) || submission.id.toString().includes(searchTerm);
            });
        }
    },
    mounted() {
        this.fetchFormTitle();
        this.fetchSubmissions();
    },
    methods: {
        fetchSubmissions() {
            this.loading = true;
            axios.get(`/api/forms/${this.id}/submissions`)
                .then(response => {
                    this.submissions = response.data;
                })
                .catch(error => {
                    console.error('Error fetching submissions:', error);
                    alert('Error loading submissions');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        fetchFormTitle() {
            axios.get(`/api/forms/${this.id}`)
                .then(response => {
                    this.formTitle = response.data.title;
                document.title = `Submissions - ${this.formTitle} | Form Builder`;
                })
                .catch(error => {
                    console.error('Error fetching form title:', error);
                });
        },
        viewSubmissionDetails(submission) {
            this.$router.push(`/submissions/${submission.id}`);
        },
        refreshSubmissions() {
            this.fetchSubmissions();
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        }
    }
}
</script>

<style scoped>
.hover-shadow {
    transition: box-shadow 0.2s ease-in-out;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>