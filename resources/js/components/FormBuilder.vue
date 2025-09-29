<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">{{ isEditing ? 'Edit Form' : 'Create New Form' }}</h2>
            <button class="btn btn-secondary" @click="$router.push('/forms')">
                <i class="fas fa-arrow-left me-1"></i> Back to Forms
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="formTitle" class="form-label">Form Title</label>
                    <input type="text" class="form-control" id="formTitle" v-model="form.title" placeholder="Enter form title">
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Form Fields</h6>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="addFieldDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus me-1"></i> Add Field
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="addFieldDropdown">
                        <li><a class="dropdown-item" href="#" @click.prevent="addField('text')">Text Input</a></li>
                        <li><a class="dropdown-item" href="#" @click.prevent="addField('textarea')">Text Area</a></li>
                        <li><a class="dropdown-item" href="#" @click.prevent="addField('checkbox')">Checkbox</a></li>
                        <li><a class="dropdown-item" href="#" @click.prevent="addField('radio')">Radio Button</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <draggable v-model="form.fields" handle=".drag-handle" @end="updateFieldOrder">
                    <div v-for="(field, index) in form.fields" :key="field.id || index" class="form-field card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-grip-lines drag-handle me-2"></i>
                                    {{ getFieldTypeName(field.type) }}
                                </h6>
                                <button class="btn btn-sm btn-danger" @click="removeField(field, index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label :for="'fieldLabel'+index" class="form-label">Label</label>
                                <input type="text" class="form-control" :id="'fieldLabel'+index" v-model="field.label" placeholder="Enter field label">
                            </div>
                            
                            <div class="mb-3" v-if="field.type === 'checkbox' || field.type === 'radio'">
                                <label class="form-label">Options</label>
                                <div class="field-options mb-2">
                                    <div v-for="(option, optIndex) in field.options" :key="optIndex" class="d-flex align-items-center mb-2">
                                        <input type="text" class="form-control form-control-sm me-2" v-model="field.options[optIndex]" placeholder="Option text">
                                        <button class="btn btn-sm btn-danger" @click="removeOption(field, optIndex)" :disabled="field.options.length <= 1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <button class="btn btn-sm btn-secondary" @click="addOption(field)">
                                        <i class="fas fa-plus me-1"></i> Add Option
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" :id="'fieldRequired'+index" v-model="field.required">
                                <label class="form-check-label" :for="'fieldRequired'+index">Required field</label>
                            </div>
                        </div>
                    </div>
                </draggable>
                
                <div v-if="form.fields.length === 0" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No fields added yet. Use the "Add Field" button to get started.</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button class="btn btn-info" @click="previewForm" :disabled="!form.title || form.fields.length === 0">
                <i class="fas fa-eye me-1"></i> Preview Form
            </button>
            <button class="btn btn-success" @click="saveForm" :disabled="!form.title || form.fields.length === 0">
                <i class="fas fa-save me-1"></i> Save Form
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import draggable from 'vuedraggable';

export default {
    name: 'FormBuilder',
    components: {
        draggable
    },
    data() {
        return {
            form: {
                id: null,
                title: '',
                fields: []
            },
            isEditing: false
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.isEditing = true;
            this.fetchForm(this.$route.params.id);
        }
    },
    methods: {
        fetchForm(id) {
            axios.get(`/api/forms/${id}`)
                .then(response => {
                    this.form = response.data;
                })
                .catch(error => {
                    console.error('Error fetching form:', error);
                    alert('Error loading form data');
                });
        },
        getFieldTypeName(type) {
            const names = {
                'text': 'Text Input',
                'textarea': 'Text Area',
                'checkbox': 'Checkbox',
                'radio': 'Radio Button'
            };
            return names[type] || type;
        },
        addField(type) {
            const newField = {
                type: type,
                label: this.getDefaultLabel(type),
                required: false,
                order: this.form.fields.length
            };
            
            if (type === 'checkbox' || type === 'radio') {
                newField.options = ['Option 1', 'Option 2'];
            }
            
            this.form.fields.push(newField);
        },
        getDefaultLabel(type) {
            const labels = {
                'text': 'Text Field',
                'textarea': 'Text Area',
                'checkbox': 'Checkbox Group',
                'radio': 'Radio Group'
            };
            return labels[type] || 'New Field';
        },
        removeField(field, index) {
            if (confirm('Are you sure you want to remove this field?')) {
                if (field.id) {
                    axios.delete(`/api/fields/${field.id}`)
                        .then(() => {
                            this.form.fields.splice(index, 1);
                            this.updateFieldOrder();
                        })
                        .catch(error => {
                            console.error('Error deleting field:', error);
                            alert('Error deleting field');
                        });
                } else {
                    this.form.fields.splice(index, 1);
                    this.updateFieldOrder();
                }
            }
        },
        addOption(field) {
            if (!field.options) {
                field.options = [];
            }
            field.options.push(`Option ${field.options.length + 1}`);
        },
        removeOption(field, index) {
            if (field.options.length > 1) {
                field.options.splice(index, 1);
            }
        },
        updateFieldOrder() {
            this.form.fields.forEach((field, index) => {
                field.order = index;
            });
        },
        previewForm() {
            if (!this.validateForm()) return;
            
            if (this.isEditing) {
                this.$router.push(`/forms/${this.form.id}/preview`);
            } else {
                localStorage.setItem('formPreview', JSON.stringify(this.form));
                this.$router.push('/preview');
            }
        },
        saveForm() {
            if (!this.validateForm()) return;
            
            const request = this.isEditing 
                ? axios.put(`/api/forms/${this.form.id}`, this.form)
                : axios.post('/api/forms', this.form);
            
            request.then(response => {
                this.$router.push('/forms');
            }).catch(error => {
                console.error('Error saving form:', error);
                alert('Error saving form. Please try again.');
            });
        },
        validateForm() {
            if (!this.form.title.trim()) {
                alert('Please enter a form title');
                return false;
            }
            
            if (this.form.fields.length === 0) {
                alert('Please add at least one field to the form');
                return false;
            }
            
            // Validate field labels
            for (const field of this.form.fields) {
                if (!field.label.trim()) {
                    alert('All fields must have a label');
                    return false;
                }
            }
            
            return true;
        }
    }
}
</script>