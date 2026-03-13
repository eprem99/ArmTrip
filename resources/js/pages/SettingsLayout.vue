<template>
    <div class="flex min-h-0 flex-1 gap-6">
        <!-- Подпанель меню настроек -->
        <aside class="w-56 shrink-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                Настройки
            </p>
            <nav class="mt-2 space-y-0.5">
                <a
                    href="/admin/settings/organization"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                        isOrganization
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                >
                    <BuildingOffice2Icon class="h-5 w-5 shrink-0" />
                    <span>Организация</span>
                </a>
            </nav>
        </aside>

        <!-- Контент страницы настроек -->
        <div class="min-w-0 flex-1">
            <SettingsOrganization />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { BuildingOffice2Icon } from '@heroicons/vue/24/outline';
import SettingsOrganization from './SettingsOrganization.vue';

const isOrganization = computed(() => {
    if (typeof window === 'undefined') return true;
    return (
        window.location.pathname === '/admin/settings/organization' ||
        window.location.pathname === '/admin/settings'
    );
});
</script>
