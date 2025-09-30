import Vue from 'vue';
import VueRouter from 'vue-router';
import FormList from '../components/FormList.vue';
import FormBuilder from '../components/FormBuilder.vue';
import FormPreview from '../components/FormPreview.vue';
import Submissions from '../components/Submissions.vue';
import SubmissionDetails from '../components/SubmissionDetails.vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        redirect: '/forms'
    },
    {
        path: '/dashboard',
        component: { template: '<div>Dashboard</div>' }
    },
    {
        path: '/forms',
        name: 'FormList',
        component: FormList
    },
    {
        path: '/forms/create',
        name: 'FormCreate',
        component: FormBuilder
    },
    {
        path: '/forms/:id/edit',
        name: 'FormEdit',
        component: FormBuilder,
        props: true
    },
    {
        path: '/forms/:id/preview',
        name: 'FormPreview',
        component: FormPreview,
        props: true
    },
    {
        path: '/forms/:id/submissions',
        name: 'Submissions',
        component: Submissions,
        props: true
    },
    {
        path: '/submissions/:id',
        name: 'SubmissionDetails',
        component: SubmissionDetails,
        props: true
    },
    // Add a catch-all route for previewing unsaved forms
    {
        path: '/preview',
        name: 'Preview',
        component: FormPreview
    },
    // Add error route
    {
        path: '/error',
        name: 'Error',
        component: { template: '<div>Error loading page</div>' }
    }
];

const router = new VueRouter({
    mode: 'history',
    routes
});

// Add global error handling for route navigation
router.onError((error) => {
    console.error('Router error:', error);
    // You can redirect to an error page or show a notification
});

export default router;