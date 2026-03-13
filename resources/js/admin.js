import './bootstrap';

import { createApp } from 'vue';
import AdminDashboard from './components/AdminDashboard.vue';

const el = document.getElementById('admin-app');

if (el) {
    createApp(AdminDashboard).mount(el);
}

