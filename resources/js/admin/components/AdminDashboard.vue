<template>
    <div class="min-h-screen bg-slate-100 flex">
        <AdminSidebar v-model:collapsed="sidebarCollapsed" />
        <div
            class="flex flex-1 flex-col transition-[padding] duration-200"
            :class="sidebarCollapsed ? 'pl-20' : 'pl-64'"
        >
            <TopNavbar :collapsed="sidebarCollapsed" />
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
import Dashboard from '../pages/dashboard/Dashboard.vue';
import SettingsLayout from '../pages/settings/SettingsLayout.vue';
import Media from '../pages/media/Media.vue';
import MediaForm from '../pages/media/MediaForm.vue';
import Content from '../pages/content/Content.vue';
import ContentPageForm from '../pages/content/ContentPageForm.vue';
import Users from '../pages/users/Users.vue';
import UserForm from '../pages/users/UserForm.vue';
import Subscribers from '../pages/subscribers/Subscribers.vue';
import BlogPosts from '../pages/blog/BlogPosts.vue';
import BlogPostForm from '../pages/blog/BlogPostForm.vue';
import TaxonomyTerms from '../pages/blog/TaxonomyTerms.vue';
import TermForm from '../pages/blog/TermForm.vue';
import Rentals from '../pages/rentals/Rentals.vue';
import RentalTypes from '../pages/rentals/RentalTypes.vue';
import RentalAmenities from '../pages/rentals/RentalAmenities.vue';

const sidebarCollapsed = ref(false);

const currentPage = computed(() => {
    if (typeof window === 'undefined') return Dashboard;
    const path = window.location.pathname;
    if (path.startsWith('/admin/settings')) return SettingsLayout;
    if (path === '/admin/media/create' || /^\/admin\/media\/\d+\/edit$/.test(path)) return MediaForm;
    if (path.startsWith('/admin/media')) return Media;
    if (path === '/admin/content/pages/create' || /^\/admin\/content\/pages\/\d+\/edit$/.test(path)) return ContentPageForm;
    if (path.startsWith('/admin/content')) return Content;
    if (path.startsWith('/admin/rentals/amenities')) return RentalAmenities;
    if (path.startsWith('/admin/rentals/types')) return RentalTypes;
    if (path.startsWith('/admin/rentals')) return Rentals;
    if (path.startsWith('/admin/subscribers')) return Subscribers;
    if (path === '/admin/users/create' || /^\/admin\/users\/\d+\/edit$/.test(path)) return UserForm;
    if (path.startsWith('/admin/users')) return Users;
    if (path === '/admin/blog/posts/create' || /^\/admin\/blog\/posts\/\d+\/edit$/.test(path)) return BlogPostForm;
    if (/^\/admin\/blog\/taxonomies\/.+\/terms\/create$/.test(path)) return TermForm;
    if (/^\/admin\/blog\/taxonomies\/.+\/terms\/\d+\/edit$/.test(path)) return TermForm;
    if (/^\/admin\/blog\/taxonomies\/.+/.test(path)) return TaxonomyTerms;
    if (path.startsWith('/admin/blog/posts') || path === '/admin/blog') return BlogPosts;
    return Dashboard;
});
</script>
