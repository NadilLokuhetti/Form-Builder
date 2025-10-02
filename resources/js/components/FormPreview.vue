<!-- frontend/src/components/FormPreview.vue -->
<template>
  <div class="form-preview">
    <div class="page-header">
      <h2>Preview: {{ form.title }}</h2>
      <button @click="$emit('back')" class="btn btn-outline">← Back to Forms</button>
    </div>

    <div class="preview-container">
      <div class="form-wrapper">
        <form @submit.prevent="submitForm" class="preview-form">
          <div class="form-description" v-if="form.description">
            {{ form.description }}
          </div>

          <div 
            v-for="field in form.fields" 
            :key="field.id" 
            class="field-preview"
          >
            <label class="field-label">
              {{ field.label }}
              <span v-if="field.required" class="required">*</span>
            </label>

            <!-- Text Input -->
            <input 
              v-if="field.type === 'text'"
              v-model="formData[field.id]"
              type="text" 
              class="input"
              :required="field.required"
            >

            <!-- Text Area -->
            <textarea 
              v-else-if="field.type === 'textarea'"
              v-model="formData[field.id]"
              class="textarea"
              :required="field.required"
              rows="4"
            ></textarea>

            <!-- Radio Buttons -->
            <div v-else-if="field.type === 'radio'" class="options-group">
              <label 
                v-for="option in field.options" 
                :key="option" 
                class="option-label"
              >
                <input 
                  type="radio" 
                  :name="`field_${field.id}`"
                  :value="option"
                  v-model="formData[field.id]"
                  :required="field.required"
                >
                {{ option }}
              </label>
            </div>

            <!-- Checkboxes -->
            <div v-else-if="field.type === 'checkbox'" class="options-group">
              <label 
                v-for="option in field.options" 
                :key="option" 
                class="option-label"
              >
                <input 
                  type="checkbox" 
                  :value="option"
                  v-model="formData[field.id]"
                >
                {{ option }}
              </label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-large">Submit Form</button>
        </form>

        <div v-if="submissionSuccess" class="success-message">
          <h3>Form Submitted Successfully!</h3>
          <p>Thank you for your submission.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'FormPreview',
  props: {
    formId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      form: {},
      formData: {},
      submissionSuccess: false
    }
  },
  async mounted() {
    await this.loadForm()
  },
  methods: {
    async loadForm() {
      try {
        const response = await axios.get(`/api/forms/${this.formId}`)
        this.form = response.data
        
        // Initialize form data structure
        this.form.fields.forEach(field => {
          if (field.type === 'checkbox') {
            this.formData[field.id] = []
          } else {
            this.formData[field.id] = ''
          }
        })
      } catch (error) {
        console.error('Error loading form:', error)
      }
    },
    async submitForm() {
      try {
        const submissionData = {}
        Object.keys(this.formData).forEach(fieldId => {
          submissionData[`field_${fieldId}`] = this.formData[fieldId]
        })

        await axios.post(`/api/forms/${this.formId}/submissions`, submissionData)
        this.submissionSuccess = true
        
        // Reset form after successful submission
        setTimeout(() => {
          this.submissionSuccess = false
          this.loadForm() // Reload form to reset data
        }, 3000)
      } catch (error) {
        console.error('Error submitting form:', error)
        alert('Error submitting form. Please check your inputs and try again.')
      }
    }
  }
}
</script>

<style scoped>
.preview-container {
  max-width: 600px;
  margin: 2rem auto 0;
}

.form-wrapper {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-description {
  margin-bottom: 2rem;
  color: #666;
  line-height: 1.5;
}

.field-preview {
  margin-bottom: 1.5rem;
}

.field-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.required {
  color: #dc2626;
}

.options-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.option-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.btn-large {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  width: 100%;
}

.success-message {
  text-align: center;
  padding: 2rem;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  color: #166534;
}

.success-message h3 {
  margin-bottom: 0.5rem;
}
</style>