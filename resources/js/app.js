import Vue from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Set up axios defaults - add your API base URL
axios.defaults.baseURL = process.env.NODE_ENV === 'production' 
    ? 'https://your-production-api.com' 
    : 'http://localhost:8000/api';

// Add a request interceptor to include the auth token
axios.interceptors.request.use(
    config => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        // Add content-type for JSON requests
        if (!config.headers['Content-Type']) {
            config.headers['Content-Type'] = 'application/json';
        }
        return config;
    },
    error => {
        console.error('Request error:', error);
        return Promise.reject(error);
    }
);

// Add a response interceptor to handle auth errors
axios.interceptors.response.use(
    response => response,
    error => {
        console.error('Response error:', error);
        
        if (error.response) {
            // Server responded with error status
            if (error.response.status === 401) {
                localStorage.removeItem('auth_token');
                router.push('/login');
            } else if (error.response.status === 404) {
                console.error('Resource not found:', error.config.url);
            } else if (error.response.status >= 500) {
                console.error('Server error:', error.response.status);
            }
        } else if (error.request) {
            // Request was made but no response received
            console.error('No response received:', error.request);
        } else {
            // Something else happened
            console.error('Error:', error.message);
        }
        
        return Promise.reject(error);
    }
);

// Global error handler for Vue
Vue.config.errorHandler = (err, vm, info) => {
    console.error('Vue error:', err);
    console.error('Error info:', info);
};

Vue.config.productionTip = false;

// Create and mount the Vue instance with error handling
new Vue({
    router,
    render: h => h(App),
}).$mount('#app');

// Log to confirm Vue is loaded
console.log('Vue application mounted');