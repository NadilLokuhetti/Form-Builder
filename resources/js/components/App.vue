<!-- resources/js/components/App.vue -->
<template>
  <div id="app">
    <div class="container">
      <header class="app-header">
        <h1>Form Builder</h1>
        <p>Manage your forms</p>
      </header>
      
      <nav class="sidebar">
        <ul>
          <li><a href="#" :class="{ active: currentView === 'forms' }" @click="currentView = 'forms'">Forms</a></li>
          <li><a href="#" :class="{ active: currentView === 'create' }" @click="currentView = 'create'">Create Form</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <FormsList 
          v-if="currentView === 'forms'" 
          @edit-form="editForm"
          @view-submissions="viewSubmissions"
          @preview-form="previewForm"
        />
        <FormBuilder 
          v-else-if="currentView === 'create'" 
          :form-id="selectedFormId"
          @form-created="currentView = 'forms'"
        />
        <FormSubmissions 
          v-else-if="currentView === 'submissions'"
          :form-id="selectedFormId"
          @back="currentView = 'forms'"
        />
        <FormPreview 
          v-else-if="currentView === 'preview'"
          :form-id="selectedFormId"
          @back="currentView = 'forms'"
        />
      </main>
    </div>
  </div>
</template>

<script>
import FormsList from './FormsList.vue'
import FormBuilder from './FormBuilder.vue'
import FormSubmissions from './FormSubmissions.vue'
import FormPreview from './FormPreview.vue'

export default {
  name: 'App',
  components: {
    FormsList,
    FormBuilder,
    FormSubmissions,
    FormPreview
  },
  data() {
    return {
      currentView: 'forms',
      selectedFormId: null
    }
  },
  methods: {
    editForm(formId) {
      this.selectedFormId = formId
      this.currentView = 'create'
    },
    viewSubmissions(formId) {
      this.selectedFormId = formId
      this.currentView = 'submissions'
    },
    previewForm(formId) {
      this.selectedFormId = formId
      this.currentView = 'preview'
    }
  }
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background-color: #f5f5f5;
}

.container {
  display: grid;
  grid-template-columns: 250px 1fr;
  grid-template-rows: auto 1fr;
  min-height: 100vh;
}

.app-header {
  grid-column: 1 / -1;
  background: white;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e1e5e9;
}

.app-header h1 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a1a1a;
}

.app-header p {
  color: #666;
  margin-top: 0.25rem;
}

.sidebar {
  background: white;
  border-right: 1px solid #e1e5e9;
  padding: 1.5rem 0;
}

.sidebar ul {
  list-style: none;
}

.sidebar li a {
  display: block;
  padding: 0.75rem 1.5rem;
  color: #333;
  text-decoration: none;
  transition: background-color 0.2s;
}

.sidebar li a:hover,
.sidebar li a.active {
  background-color: #f0f7ff;
  color: #0066cc;
}

.main-content {
  padding: 2rem;
  background: #f8f9fa;
}
</style>