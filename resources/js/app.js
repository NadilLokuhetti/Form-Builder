import Vue from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Set up axios defaults
axios.defaults.baseURL = '/api';

// Add a request interceptor to include the auth token
axios.interceptors.request.use(
    config => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Add a response interceptor to handle auth errors
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('auth_token');
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

Vue.config.productionTip = false;

// Create and mount the Vue instance
new Vue({
    router,
    render: h => h(App),
}).$mount('#app');

// Log to confirm Vue is loaded
console.log('Vue application mounted');