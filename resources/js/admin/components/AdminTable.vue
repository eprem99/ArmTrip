<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                <tr>
                    <slot name="head" />
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <slot />
            </tbody>
        </table>
    </div>
</template>

<script setup>
// Simple reusable admin table shell for list pages (Content, Media, etc.)
</script>

