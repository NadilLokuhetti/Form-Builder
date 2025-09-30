<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">
                <i class="fas fa-file-alt text-primary me-2"></i>
                Submission Details
            </h2>
            <button class="btn btn-secondary" @click="$router.go(-1)">
                <i class="fas fa-arrow-left me-1"></i> Back to Submissions
            </button>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i>
                            Form Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <div v-for="(value, key) in submission.data" :key="key" class="mb-4 pb-3 border-bottom">
                            <label class="form-label fw-bold text-primary mb-2">
                                {{ key }}
                            </label>
                            <div class="submission-value p-3 bg-light rounded">
                                <template v-if="Array.isArray(value)">
                                    <span v-if="value.length > 0" class="d-block">
                                        {{ value.join(', ') }}
                                    </span>
                                    <span v-else class="text-muted fst-italic">
                                        No selection
                                    </span>
                                </template>
                                <template v-else>
                                    <span v-if="value">
                                        {{ value }}
                                    </span>
                                    <span v-else class="text-muted fst-italic">
                                        Not provided
                                    </span>
                                </template>
                            </div>
                        </div>
                        
                        <div v-if="Object.keys(submission.data).length === 0" class="text-center py-5 text-muted">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p>No form data available for this submission.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Submission Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-info">
                                <i class="fas fa-hashtag me-1"></i>
                                Submission ID
                            </label>
                            <p class="mb-0">{{ submission.id }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-info">
                                <i class="fas fa-calendar me-1"></i>
                                Submitted Date
                            </label>
                            <p class="mb-0">{{ formatDate(submission.created_at) }}</p>
                            <small class="text-muted">{{ formatRelativeTime(submission.created_at) }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-info">
                                <i class="fas fa-globe me-1"></i>
                                IP Address
                            </label>
                            <p class="mb-0 font-monospace">{{ submission.ip_address }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-info">
                                <i class="fas fa-database me-1"></i>
                                Data Fields
                            </label>
                            <p class="mb-0">{{ Object.keys(submission.data).length }} field(s)</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-primary w-100 mb-2" @click="printSubmission">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-outline-success w-100 mb-2" @click="exportSubmission">
                            <i class="fas fa-download me-1"></i> Export as JSON
                        </button>
                        <button class="btn btn-outline-danger w-100" @click="deleteSubmission" :disabled="deleting">
                            <i class="fas fa-trash me-1" :class="{'fa-spin': deleting}"></i>
                            {{ deleting ? 'Deleting...' : 'Delete Submission' }}
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
    name: 'SubmissionDetails',
    props: ['id'],
    data() {
        return {
            submission: {
                id: null,
                data: {},
                ip_address: '',
                created_at: '',
                form_id: null
            },
            deleting: false,
            loading: true
        }
    },
    mounted() {
        this.fetchSubmission();
    },
    methods: {
        fetchSubmission() {
            this.loading = true;
            axios.get(`/submissions/${this.id}`)
                .then(response => {
                    this.submission = response.data;
                    document.title = `Submission #${this.submission.id} | Form Builder`;
                })
                .catch(error => {
                    console.error('Error fetching submission:', error);
                    alert('Error loading submission details');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        },
        formatRelativeTime(dateString) {
            const now = new Date();
            const time = new Date(dateString);
            const diffInSeconds = Math.floor((now - time) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
            return `${Math.floor(diffInSeconds / 86400)} days ago`;
        },
        printSubmission() {
            window.print();
        },
        exportSubmission() {
            const dataStr = JSON.stringify(this.submission, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `submission-${this.submission.id}.json`;
            link.click();
            URL.revokeObjectURL(url);
        },
        deleteSubmission() {
            if (confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
                this.deleting = true;
                axios.delete(`/submissions/${this.id}`)
                    .then(() => {
                        this.$router.push(`/forms/${this.submission.form_id}/submissions`);
                    })
                    .catch(error => {
                        console.error('Error deleting submission:', error);
                        alert('Error deleting submission');
                    })
                    .finally(() => {
                        this.deleting = false;
                    });
            }
        }
    }
}
</script>

<style scoped>
.submission-value {
    min-height: 48px;
    word-break: break-word;
}

@media print {
    .btn, .card-header.bg-secondary {
        display: none !important;
    }
}
</style>