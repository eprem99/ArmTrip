import '../bootstrap';

import tinymce from 'tinymce';
if (typeof window !== 'undefined') {
    window.tinymce = tinymce;
}

import { createApp } from 'vue';
import AdminDashboard from './components/AdminDashboard.vue';

const app = createApp(AdminDashboard);
const el = document.getElementById('admin-app');
if (el) {
    app.mount(el);
}
