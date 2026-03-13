<template>
    <div class="min-h-screen bg-slate-100 flex flex-col">
        <TopNavbar />
        <main class="flex-1 px-4 pb-6 pt-20 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
            <Dashboard />
        </main>
    </div>
</template>

<script setup>
import TopNavbar from './TopNavbar.vue';
import Dashboard from '../pages/Dashboard.vue';
</script>

