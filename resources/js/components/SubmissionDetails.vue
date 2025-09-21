<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Submission Details</h2>
            <button class="btn btn-secondary" @click="$router.go(-1)">
                <i class="fas fa-arrow-left me-1"></i> Back to Submissions
            </button>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Data</h5>
                    </div>
                    <div class="card-body">
                        <div v-for="(value, key) in submission.data" :key="key" class="mb-3">
                            <label class="form-label"><strong>{{ key }}</strong></label>
                            <p>{{ Array.isArray(value) ? value.join(', ') : value }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Submission Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Submitted</strong></label>
                            <p>{{ formatDate(submission.created_at) }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>IP Address</strong></label>
                            <p>{{ submission.ip_address }}</p>
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
    name: 'SubmissionDetails',
    props: ['id'],
    data() {
        return {
            submission: {
                data: {},
                ip_address: '',
                created_at: ''
            }
        }
    },
    mounted() {
        this.fetchSubmission();
    },
    methods: {
        fetchSubmission() {
            axios.get(`/submissions/${this.id}`)
                .then(response => {
                    this.submission = response.data;
                })
                .catch(error => {
                    console.error('Error fetching submission:', error);
                });
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        }
    }
}
</script>