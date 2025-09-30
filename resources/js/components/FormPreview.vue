<template>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Preview Form: {{ form.title }}</h2>
            <button class="btn btn-secondary" @click="$router.go(-1)">
                <i class="fas fa-arrow-left me-1"></i> Back to Editor
            </button>
        </div>

        <div class="preview-container">
            <h3 class="mb-4">{{ form.title }}</h3>
            <p class="text-muted mb-4" v-if="form.description">{{ form.description }}</p>
            
            <form @submit.prevent="submitForm" class="needs-validation" :class="{'was-validated': submitted}">
                <div v-for="(field, index) in form.fields" :key="index" class="mb-4">
                    <label class="form-label fw-semibold">
                        {{ field.label }}
                        <span v-if="field.required" class="text-danger">*</span>
                    </label>
                    
                    <!-- Text Input -->
                    <input v-if="field.type === 'text'" 
                        type="text" 
                        class="form-control" 
                        :class="{'is-invalid': field.required && !previewData[field.label]}"
                        v-model="previewData[field.label]"
                        :required="field.required"
                        :placeholder="`Enter ${field.label.toLowerCase()}`">

                    <!-- Textarea -->
                    <textarea v-else-if="field.type === 'textarea'" 
                        class="form-control" 
                        :class="{'is-invalid': field.required && !previewData[field.label]}"
                        rows="4"
                        v-model="previewData[field.label]"
                        :required="field.required"
                        :placeholder="`Enter ${field.label.toLowerCase()}`"></textarea>
                    
                    <!-- Checkbox Group -->
                    <div v-else-if="field.type === 'checkbox'" class="ms-3">
                        <div v-for="(option, optIndex) in field.options" :key="optIndex" class="form-check">
                            <input class="form-check-input" 
                                type="checkbox" 
                                :id="'previewCheckbox'+index+optIndex"
                                :value="option"
                                v-model="previewData[field.label]"
                                :required="field.required && (!previewData[field.label] || previewData[field.label].length === 0)">
                            <label class="form-check-label" :for="'previewCheckbox'+index+optIndex">
                                {{ option }}
                            </label>
                        </div>
                        <div v-if="field.required" class="invalid-feedback d-block">
                            Please select at least one option.
                        </div>
                    </div>
                    
                    <!-- Radio Group -->
                    <div v-else-if="field.type === 'radio'" class="ms-3">
                        <div v-for="(option, optIndex) in field.options" :key="optIndex" class="form-check">
                            <input class="form-check-input" 
                                type="radio" 
                                :name="'previewRadio'+index"
                                :id="'previewRadio'+index+optIndex"
                                :value="option"
                                v-model="previewData[field.label]"
                                :required="field.required">
                            <label class="form-check-label" :for="'previewRadio'+index+optIndex">
                                {{ option }}
                            </label>
                        </div>
                        <div v-if="field.required" class="invalid-feedback d-block">
                            Please select an option.
                        </div>
                    </div>

                    <div class="form-text text-muted" v-if="field.required">
                        This field is required
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-outline-secondary" @click="$router.go(-1)">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                        Submit Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FormPreview',
    props: ['id'],
    data() {
        return {
            form: {
                title: '',
                description: '',
                fields: []
            },
            previewData: {},
            submitted: false,
            submitting: false
        }
    },
    mounted() {
        if (this.id) {
            this.fetchForm(this.id);
        } else {
            const savedForm = localStorage.getItem('formPreview');
            if (savedForm) {
                this.form = JSON.parse(savedForm);
            }
        }
    },
    methods: {
        fetchForm(id) {
            axios.get(`/forms/${id}`)
                .then(response => {
                    this.form = response.data;
                    this.initializeFormData();
                })
                .catch(error => {
                    console.error('Error fetching form:', error);
                    alert('Error loading form');
                });
        },
        initializeFormData() {
            this.form.fields.forEach(field => {
                if (field.type === 'checkbox') {
                    this.$set(this.previewData, field.label, []);
                } else {
                    this.$set(this.previewData, field.label, '');
                }
            });
        },
        submitForm() {
            this.submitted = true;
            
            if (!this.validateForm()) {
                return;
            }

            this.submitting = true;

            if (this.id) {
                axios.post(`/forms/${this.id}/submissions`, {
                    data: this.previewData
                })
                .then(response => {
                    this.showSuccess();
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Error submitting form. Please try again.');
                })
                .finally(() => {
                    this.submitting = false;
                });
            } else {
                // Demo mode for unsaved forms
                setTimeout(() => {
                    this.showSuccess();
                    this.submitting = false;
                }, 1000);
            }
        },
        validateForm() {
            for (const field of this.form.fields) {
                if (field.required) {
                    const value = this.previewData[field.label];
                    if (!value || (Array.isArray(value) && value.length === 0)) {
                        return false;
                    }
                }
            }
            return true;
        },
        showSuccess() {
            alert('Form submitted successfully!');
            this.previewData = {};
            this.submitted = false;
            this.initializeFormData();
            
            if (this.id) {
                this.$router.push(`/forms/${this.id}/submissions`);
            } else {
                this.$router.push('/forms');
            }
        }
    }
}
</script>