<!-- resources/js/components/FormsList.vue -->
<template>
  <div class="forms-list">
    <div class="page-header">
      <h2>Forms</h2>
      <p>Manage your forms and view submissions</p>
    </div>

    <div class="forms-grid">
      <div 
        v-for="form in forms" 
        :key="form.id" 
        class="form-card"
      >
        <div class="form-header">
          <h3>{{ form.title }}</h3>
          <span class="submission-count">{{ form.submissions_count }} submissions</span>
        </div>
        <p v-if="form.description" class="form-description">{{ form.description }}</p>
        
        <div class="form-actions">
          <button @click="$emit('edit-form', form.id)" class="btn btn-outline">Edit</button>
          <button @click="$emit('preview-form', form.id)" class="btn btn-outline">Preview</button>
          <button @click="$emit('view-submissions', form.id)" class="btn btn-outline">Submissions</button>
          <button @click="deleteForm(form.id)" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>

    <div v-if="forms.length === 0" class="empty-state">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14,2 14,8 20,8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10,9 9,9 8,9"/>
      </svg>
      <h3>No forms created yet</h3>
      <p>Create your first form to get started</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'FormsList',
  data() {
    return {
      forms: []
    }
  },
  async mounted() {
    await this.loadForms()
  },
  methods: {
    async loadForms() {
      try {
        const response = await axios.get('/api/forms')
        this.forms = response.data
      } catch (error) {
        console.error('Error loading forms:', error)
      }
    },
    async deleteForm(formId) {
      if (confirm('Are you sure you want to delete this form? All submissions will also be deleted.')) {
        try {
          await axios.delete(`/api/forms/${formId}`)
          await this.loadForms()
        } catch (error) {
          console.error('Error deleting form:', error)
          alert('Error deleting form. Please try again.')
        }
      }
    }
  }
}
</script>

<style scoped>
.forms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.form-card {
  background: white;
  border: 1px solid #e1e5e9;
  border-radius: 8px;
  padding: 1.5rem;
  transition: box-shadow 0.2s;
}

.form-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.form-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
  flex: 1;
  margin-right: 1rem;
}

.submission-count {
  background: #f0f7ff;
  color: #0066cc;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
}

.form-description {
  color: #666;
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1rem;
}

.form-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
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

.btn-danger {
  border-color: #dc2626;
  color: #dc2626;
}

.btn-danger:hover {
  background: #fef2f2;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.empty-state svg {
  margin-bottom: 1rem;
  color: #9ca3af;
}

.empty-state h3 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #374151;
}

.empty-state p {
  font-size: 0.875rem;
}
</style>