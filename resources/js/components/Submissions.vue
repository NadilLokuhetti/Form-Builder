<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Form Submissions: {{ formTitle }}</h2>
            <div class="d-flex">
                <input type="text" class="form-control me-2" placeholder="Search submissions..." v-model="submissionSearch">
                <button class="btn btn-secondary" @click="$router.push('/forms')">
                    <i class="fas fa-arrow-left me-1"></i> Back to Forms
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div v-if="submissions.length === 0" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No submissions found for this form.</p>
                </div>
                
                <div v-else>
                    <div v-for="submission in filteredSubmissions" :key="submission.id" class="submission-card card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Submission #{{ submission.id }}</h5>
                            <p class="card-text" v-for="(value, key) in submission.data" :key="key">
                                <strong>{{ key }}:</strong> {{ Array.isArray(value) ? value.join(', ') : value }}
                            </p>
                            <button class="btn btn-sm btn-info" @click="viewSubmissionDetails(submission)">
                                View Details
                            </button>
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
            submissionSearch: ''
        }
    },
    computed: {
        filteredSubmissions() {
            if (!this.submissionSearch) {
                return this.submissions;
            }
            
            const searchTerm = this.submissionSearch.toLowerCase();
            return this.submissions.filter(submission => {
                return Object.values(submission.data).some(value => 
                    String(value).toLowerCase().includes(searchTerm)
                );
            });
        }
    },
    mounted() {
        this.fetchSubmissions();
        this.fetchFormTitle();
    },
    methods: {
        fetchSubmissions() {
            axios.get(`/forms/${this.id}/submissions`)
                .then(response => {
                    this.submissions = response.data;
                })
                .catch(error => {
                    console.error('Error fetching submissions:', error);
                });
        },
        fetchFormTitle() {
            axios.get(`/forms/${this.id}`)
                .then(response => {
                    this.formTitle = response.data.title;
                })
                .catch(error => {
                    console.error('Error fetching form title:', error);
                });
        },
        viewSubmissionDetails(submission) {
            this.$router.push(`/submissions/${submission.id}`);
        }
    }
}
</script>