<template>
    <div class="min-h-screen bg-slate-100 flex">
        <AdminSidebar v-model:collapsed="sidebarCollapsed" />
        <div
            class="flex flex-1 flex-col transition-[padding] duration-200"
            :class="sidebarCollapsed ? 'pl-20' : 'pl-64'"
        >
            <TopNavbar />
            <main class="flex-1 px-4 pb-6 pt-20 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
                <component :is="currentPage" />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import AdminSidebar from './AdminSidebar.vue';
import TopNavbar from './TopNavbar.vue';
import Dashboard from '../pages/Dashboard.vue';
import SettingsLayout from '../pages/SettingsLayout.vue';

const sidebarCollapsed = ref(false);

const currentPage = computed(() => {
    if (typeof window === 'undefined') return Dashboard;
    const path = window.location.pathname;
    if (path.startsWith('/admin/settings')) return SettingsLayout;
    return Dashboard;
});
</script>

