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
        component: FormList
    },
    {
        path: '/forms/create',
        component: FormBuilder
    },
    {
        path: '/forms/:id/edit',
        component: FormBuilder,
        props: true
    },
    {
        path: '/forms/:id/preview',
        component: FormPreview,
        props: true
    },
    {
        path: '/forms/:id/submissions',
        component: Submissions,
        props: true
    },
    {
        path: '/submissions/:id',
        component: SubmissionDetails,
        props: true
    },
    // Add a catch-all route for previewing unsaved forms
    {
        path: '/preview',
        component: FormPreview
    }
];

const router = new VueRouter({
    mode: 'history',
    routes
});

export default router;