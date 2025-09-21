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
                        <li><a class="dropdown-item" href="#" @click="addField('text')">Text Input</a></li>
                        <li><a class="dropdown-item" href="#" @click="addField('textarea')">Text Area</a></li>
                        <li><a class="dropdown-item" href="#" @click="addField('checkbox')">Checkbox</a></li>
                        <li><a class="dropdown-item" href="#" @click="addField('radio')">Radio Button</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <draggable v-model="form.fields" handle=".drag-handle" @end="updateFieldOrder">
                    <div v-for="(field, index) in form.fields" :key="index" class="form-field card mb-3">
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
                                        <button class="btn btn-sm btn-danger" @click="removeOption(field, optIndex)">
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
            <button class="btn btn-info" @click="previewForm">
                <i class="fas fa-eye me-1"></i> Preview Form
            </button>
            <button class="btn btn-success" @click="saveForm">
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
            axios.get(`/forms/${id}`)
                .then(response => {
                    this.form = response.data;
                })
                .catch(error => {
                    console.error('Error fetching form:', error);
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
                label: 'New Field',
                required: false,
                order: this.form.fields.length
            };
            
            if (type === 'checkbox' || type === 'radio') {
                newField.options = ['Option 1', 'Option 2'];
            }
            
            this.form.fields.push(newField);
        },
        removeField(field, index) {
            if (field.id) {
                axios.delete(`/fields/${field.id}`)
                    .then(() => {
                        this.form.fields.splice(index, 1);
                    })
                    .catch(error => {
                        console.error('Error deleting field:', error);
                    });
            } else {
                this.form.fields.splice(index, 1);
            }
        },
        addOption(field) {
            field.options.push(`Option ${field.options.length + 1}`);
        },
        removeOption(field, index) {
            if (field.options.length > 1) {
                field.options.splice(index, 1);
            }
        },
        updateFieldOrder() {
            // Update the order property of each field
            this.form.fields.forEach((field, index) => {
                field.order = index;
            });
            
            if (this.isEditing) {
                axios.post(`/forms/${this.form.id}/reorder-fields`, {
                    fields: this.form.fields.map(field => ({
                        id: field.id,
                        order: field.order
                    }))
                }).catch(error => {
                    console.error('Error updating field order:', error);
                });
            }
        },
        previewForm() {
            if (!this.form.title.trim()) {
                alert('Please enter a form title');
                return;
            }
            
            if (this.form.fields.length === 0) {
                alert('Please add at least one field to the form');
                return;
            }
            
            // If editing, save first then preview
            if (this.isEditing) {
                this.saveForm(() => {
                    this.$router.push(`/forms/${this.form.id}/preview`);
                });
            } else {
                // For new forms, store in local storage for preview
                localStorage.setItem('formPreview', JSON.stringify(this.form));
                this.$router.push('/preview');
            }
        },
        saveForm(callback = null) {
            if (!this.form.title.trim()) {
                alert('Please enter a form title');
                return;
            }
            
            if (this.form.fields.length === 0) {
                alert('Please add at least one field to the form');
                return;
            }
            
            const request = this.isEditing 
                ? axios.put(`/forms/${this.form.id}`, this.form)
                : axios.post('/forms', this.form);
            
            request.then(response => {
                alert('Form saved successfully!');
                if (callback) {
                    callback();
                } else {
                    this.$router.push('/forms');
                }
            }).catch(error => {
                console.error('Error saving form:', error);
                alert('Error saving form. Please try again.');
            });
        }
    }
}
</script>