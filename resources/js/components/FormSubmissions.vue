<!-- resources/js/components/FormSubmissions.vue -->
<template>
  <div class="form-submissions">
    <div class="page-header">
      <h2>Form Submissions</h2>
      <button @click="$emit('back')" class="btn btn-outline">← Back to Forms</button>
    </div>

    <div class="submissions-container">
      <div class="submissions-list">
        <h3>Submissions ({{ submissions.length }})</h3>
        
        <div class="search-box">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search submissions..." 
            class="input"
          >
        </div>

        <div class="submissions-grid">
          <div 
            v-for="submission in filteredSubmissions" 
            :key="submission.id"
            class="submission-card"
            :class="{ active: selectedSubmission?.id === submission.id }"
            @click="selectSubmission(submission)"
          >
            <div class="submission-header">
              <h4>Submission #{{ submission.id }}</h4>
              <span class="submission-date">
                {{ formatDate(submission.created_at) }}
              </span>
            </div>
            
            <div class="submission-preview">
              <div 
                v-for="answer in getPreviewAnswers(submission.answers)" 
                :key="answer.field_id"
                class="preview-field"
              >
                <strong>{{ answer.field.label }}:</strong> 
                {{ formatAnswerValue(answer) }}
              </div>
              <div v-if="submission.answers.length > 2" class="more-fields">
                +{{ submission.answers.length - 2 }} more fields
              </div>
            </div>
          </div>
        </div>

        <div v-if="filteredSubmissions.length === 0" class="empty-state">
          <p v-if="searchQuery">No submissions match your search</p>
          <p v-else>No submissions yet</p>
        </div>
      </div>

      <div class="submission-details" v-if="selectedSubmission">
        <h3>Submission Details</h3>
        
        <div class="detail-section">
          <div class="detail-item">
            <strong>Submitted:</strong>
            {{ formatDateTime(selectedSubmission.created_at) }}
          </div>
          <div class="detail-item">
            <strong>Submission ID:</strong>
            #{{ selectedSubmission.id }}
          </div>
        </div>

        <div class="detail-section">
          <h4>Form Data</h4>
          <div class="form-data">
            <div 
              v-for="answer in selectedSubmission.answers" 
              :key="answer.field_id"
              class="data-field"
            >
              <label>{{ answer.field.label }}</label>
              <div class="data-value">{{ formatAnswerValue(answer) }}</div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="no-selection">
        <div class="no-selection-content">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 10.5V19.5C21 20.3284 20.3284 21 19.5 21H4.5C3.67157 21 3 20.3284 3 19.5V10.5"/>
            <path d="M21 10.5L12 15.75L3 10.5"/>
            <path d="M3 4.5L12 9.75L21 4.5"/>
            <path d="M21 4.5V10.5"/>
            <path d="M3 4.5V10.5"/>
            <rect x="3" y="4.5" width="18" height="6" rx="1"/>
          </svg>
          <h3>Select a Submission</h3>
          <p>Click on a submission from the list to view its details</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'FormSubmissions',
  props: {
    formId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      submissions: [],
      selectedSubmission: null,
      searchQuery: ''
    }
  },
  computed: {
    filteredSubmissions() {
      if (!this.searchQuery) return this.submissions
      
      const query = this.searchQuery.toLowerCase()
      return this.submissions.filter(submission => 
        submission.answers.some(answer => 
          answer.field.label.toLowerCase().includes(query) ||
          String(answer.value).toLowerCase().includes(query)
        )
      )
    }
  },
  async mounted() {
    await this.loadSubmissions()
  },
  methods: {
    async loadSubmissions() {
      try {
        const response = await axios.get(`/api/forms/${this.formId}/submissions`)
        this.submissions = response.data
        // Auto-select the first submission if available
        if (this.submissions.length > 0 && !this.selectedSubmission) {
          this.selectedSubmission = this.submissions[0]
        }
      } catch (error) {
        console.error('Error loading submissions:', error)
      }
    },
    selectSubmission(submission) {
      this.selectedSubmission = submission
    },
    getPreviewAnswers(answers) {
      return answers.slice(0, 2) // Show first 2 answers in preview
    },
    formatAnswerValue(answer) {
      try {
        // Handle array values (for checkboxes)
        const value = JSON.parse(answer.value)
        return Array.isArray(value) ? value.join(', ') : value
      } catch {
        // Handle string values
        return answer.value || 'No answer provided'
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },
    formatDateTime(dateString) {
      return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>

<style scoped>
.submissions-container {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 2rem;
  margin-top: 2rem;
  height: calc(100vh - 200px);
}

.submissions-list {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  overflow-y: auto;
  border: 1px solid #e1e5e9;
}

.submissions-list h3 {
  margin-bottom: 1rem;
  font-size: 1.125rem;
  font-weight: 600;
  color: #1a1a1a;
}

.search-box {
  margin-bottom: 1.5rem;
}

.search-box .input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
}

.submissions-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.submission-card {
  border: 1px solid #e1e5e9;
  border-radius: 6px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s;
  background: white;
}

.submission-card:hover {
  border-color: #0066cc;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.submission-card.active {
  border-color: #0066cc;
  background: #f0f7ff;
}

.submission-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.submission-header h4 {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.submission-date {
  font-size: 0.75rem;
  color: #666;
  white-space: nowrap;
}

.submission-preview {
  space-y: 0.5rem;
}

.preview-field {
  font-size: 0.75rem;
  line-height: 1.4;
}

.preview-field strong {
  color: #374151;
}

.more-fields {
  font-size: 0.75rem;
  color: #666;
  font-style: italic;
  margin-top: 0.25rem;
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.submission-details {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  overflow-y: auto;
  border: 1px solid #e1e5e9;
}

.submission-details h3 {
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a1a1a;
}

.detail-section {
  margin-bottom: 2rem;
}

.detail-section h4 {
  margin-bottom: 1rem;
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
}

.detail-item {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.detail-item strong {
  color: #374151;
  margin-right: 0.5rem;
}

.form-data {
  space-y: 1.5rem;
}

.data-field {
  padding-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.data-field:last-child {
  border-bottom: none;
}

.data-field label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.data-value {
  background: #f8f9fa;
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid #e9ecef;
  font-size: 0.875rem;
  line-height: 1.5;
  white-space: pre-wrap;
  word-wrap: break-word;
}

.no-selection {
  background: white;
  border-radius: 8px;
  border: 1px solid #e1e5e9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.no-selection-content {
  text-align: center;
  color: #666;
  padding: 3rem;
}

.no-selection-content svg {
  color: #9ca3af;
  margin-bottom: 1rem;
}

.no-selection-content h3 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #374151;
}

.no-selection-content p {
  font-size: 0.875rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
}

.btn:hover {
  background: #f9fafb;
}

.btn-outline {
  border-color: #0066cc;
  color: #0066cc;
}

.btn-outline:hover {
  background: #f0f7ff;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.page-header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

/* Responsive design */
@media (max-width: 1024px) {
  .submissions-container {
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr;
    height: auto;
  }
  
  .submissions-list {
    max-height: 400px;
  }
}
</style>