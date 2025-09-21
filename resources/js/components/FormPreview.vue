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
            
            <form @submit.prevent="submitForm">
                <div v-for="(field, index) in form.fields" :key="index" class="mb-3">
                    <label class="form-label">{{ field.label }} <span v-if="field.required" class="text-danger">*</span></label>
                    
                    <!-- Text Input -->
                    <input v-if="field.type === 'text'" 
                        type="text" 
                        class="form-control" 
                        v-model="previewData[field.label]"
                        :required="field.required">
                    
                    <!-- Textarea -->
                    <textarea v-else-if="field.type === 'textarea'" 
                        class="form-control" 
                        rows="3"
                        v-model="previewData[field.label]"
                        :required="field.required"></textarea>
                    
                    <!-- Checkbox Group -->
                    <div v-else-if="field.type === 'checkbox'" class="ms-3">
                        <div v-for="(option, optIndex) in field.options" :key="optIndex" class="form-check">
                                            <input class="form-check-input" 
                                                type="checkbox" 
                                                :id="'previewCheckbox'+index+optIndex"
                                                :value="option"
                                                v-model="previewData[field.label]">
                                            <label class="form-check-label" :for="'previewCheckbox'+index+optIndex">
                                                {{ option }}
                                            </label>
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
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mt-3">Submit Form</button>
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
                fields: []
            },
            previewData: {}
        }
    },
    mounted() {
        if (this.id) {
            this.fetchForm(this.id);
        } else {
            // For new forms not yet saved
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
                })
                .catch(error => {
                    console.error('Error fetching form:', error);
                });
        },
        submitForm() {
            // Simple validation
            for (const field of this.form.fields) {
                if (field.required) {
                    const value = this.previewData[field.label];
                    if (!value || (Array.isArray(value) && value.length === 0)) {
                        alert(`Please fill in the required field: ${field.label}`);
                        return;
                    }
                }
            }
            
            if (this.id) {
                // Submit to backend
                axios.post(`/forms/${this.id}/submissions`, {
                    data: this.previewData
                })
                .then(response => {
                    alert('Form submitted successfully!');
                    this.previewData = {};
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Error submitting form. Please try again.');
                });
            } else {
                // For demo purposes with unsaved forms
                alert('Form submitted successfully! (Demo mode - form not saved to backend)');
                this.previewData = {};
            }
        }
    }
}
</script>