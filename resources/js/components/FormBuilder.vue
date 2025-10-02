<!-- frontend/src/components/FormBuilder.vue -->
<template>
  <div class="form-builder">
    <div class="page-header">
      <h2>{{ isEditing ? 'Edit Form' : 'Create Form' }}</h2>
      <button @click="$emit('form-created')" class="btn btn-outline">← Back to Forms</button>
    </div>

    <div class="builder-container">
      <div class="form-config">
        <div class="config-section">
          <label class="label">Form Title *</label>
          <input 
            v-model="form.title" 
            type="text" 
            class="input" 
            placeholder="Enter form title"
          >
        </div>

        <div class="config-section">
          <label class="label">Description</label>
          <textarea 
            v-model="form.description" 
            class="textarea" 
            placeholder="Enter form description"
            rows="3"
          ></textarea>
        </div>

        <div class="config-section">
          <h3>Add Fields</h3>
          <div class="field-types">
            <button 
              v-for="fieldType in fieldTypes" 
              :key="fieldType.type"
              @click="addField(fieldType.type)"
              class="btn btn-outline"
            >
              + {{ fieldType.label }}
            </button>
          </div>
        </div>
      </div>

      <div class="fields-preview">
        <h3>Form Fields</h3>
        
        <draggable 
          v-model="form.fields" 
          item-key="id"
          @end="updateFieldOrders"
        >
          <template #item="{ element: field, index }">
            <div class="field-item">
              <div class="field-header">
                <span class="field-type">{{ getFieldTypeLabel(field.type) }}</span>
                <button @click="removeField(index)" class="btn btn-danger btn-sm">Remove</button>
              </div>
              
              <div class="field-config">
                <div class="config-row">
                  <label class="label">Label</label>
                  <input 
                    v-model="field.label" 
                    type="text" 
                    class="input" 
                    placeholder="Field label"
                  >
                </div>

                <div v-if="hasOptions(field.type)" class="config-row">
                  <label class="label">Options</label>
                  <div v-for="(option, optIndex) in field.options" :key="optIndex" class="option-row">
                    <input 
                      v-model="field.options[optIndex]" 
                      type="text" 
                      class="input" 
                      placeholder="Option text"
                    >
                    <button 
                      @click="removeOption(field, optIndex)" 
                      class="btn btn-danger btn-sm"
                    >
                      Remove
                    </button>
                  </div>
                  <button @click="addOption(field)" class="btn btn-outline btn-sm">
                    + Add Option
                  </button>
                </div>

                <div class="config-row">
                  <label class="checkbox-label">
                    <input 
                      v-model="field.required" 
                      type="checkbox" 
                    >
                    Required field
                  </label>
                </div>
              </div>
            </div>
          </template>
        </draggable>

        <div v-if="form.fields.length === 0" class="empty-fields">
          <p>No fields added yet. Add some fields to your form.</p>
        </div>

        <div class="form-actions">
          <button @click="saveForm" class="btn btn-primary" :disabled="!canSave">
            {{ isEditing ? 'Update Form' : 'Create Form' }}
          </button>
          <button @click="previewForm" class="btn btn-outline">Preview Form</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import draggable from 'vuedraggable'

export default {
  name: 'FormBuilder',
  components: {
    draggable
  },
  props: {
    formId: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      form: {
        title: '',
        description: '',
        fields: []
      },
      fieldTypes: [
        { type: 'text', label: 'Text Input' },
        { type: 'textarea', label: 'Text Area' },
        { type: 'checkbox', label: 'Checkbox' },
        { type: 'radio', label: 'Radio Button' }
      ]
    }
  },
  computed: {
    isEditing() {
      return this.formId !== null
    },
    canSave() {
      return this.form.title.trim() && this.form.fields.length > 0
    }
  },
  async mounted() {
    if (this.isEditing) {
      await this.loadForm()
    }
  },
  methods: {
    async loadForm() {
      try {
        const response = await axios.get(`/api/forms/${this.formId}`)
        this.form = response.data
      } catch (error) {
        console.error('Error loading form:', error)
      }
    },
    addField(type) {
      const baseField = {
        type,
        label: this.getFieldTypeLabel(type),
        required: false,
        order: this.form.fields.length
      }

      if (this.hasOptions(type)) {
        baseField.options = ['Option 1', 'Option 2']
      }

      this.form.fields.push(baseField)
    },
    removeField(index) {
      this.form.fields.splice(index, 1)
      this.updateFieldOrders()
    },
    addOption(field) {
      if (!field.options) {
        field.options = []
      }
      field.options.push(`Option ${field.options.length + 1}`)
    },
    removeOption(field, index) {
      field.options.splice(index, 1)
    },
    hasOptions(type) {
      return ['checkbox', 'radio'].includes(type)
    },
    getFieldTypeLabel(type) {
      const fieldType = this.fieldTypes.find(ft => ft.type === type)
      return fieldType ? fieldType.label : type
    },
    updateFieldOrders() {
      this.form.fields.forEach((field, index) => {
        field.order = index
      })
    },
    async saveForm() {
      try {
        const url = this.isEditing ? `/api/forms/${this.formId}` : '/api/forms'
        const method = this.isEditing ? 'put' : 'post'

        await axios[method](url, this.form)
        this.$emit('form-created')
      } catch (error) {
        console.error('Error saving form:', error)
        alert('Error saving form. Please try again.')
      }
    },
    previewForm() {
      if (this.formId) {
        this.$emit('preview-form', this.formId)
      } else {
        // For new forms, we need to save first
        if (this.canSave) {
          this.saveForm()
        } else {
          alert('Please add a title and at least one field before previewing.')
        }
      }
    }
  }
}
</script>

<style scoped>
.builder-container {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
  margin-top: 2rem;
}

.config-section {
  margin-bottom: 2rem;
}

.config-section h3 {
  margin-bottom: 1rem;
  font-size: 1rem;
  font-weight: 600;
}

.field-types {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.field-item {
  background: white;
  border: 1px solid #e1e5e9;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
}

.field-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.field-type {
  background: #f0f7ff;
  color: #0066cc;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.config-row {
  margin-bottom: 1rem;
}

.label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.input, .textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
}

.option-row {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.option-row .input {
  flex: 1;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.btn-primary {
  background: #0066cc;
  color: white;
  border-color: #0066cc;
}

.btn-primary:hover {
  background: #0052a3;
}

.empty-fields {
  text-align: center;
  padding: 2rem;
  color: #666;
  background: white;
  border: 1px dashed #d1d5db;
  border-radius: 8px;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e1e5e9;
}
</style>