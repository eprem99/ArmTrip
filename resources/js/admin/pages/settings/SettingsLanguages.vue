<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('settings.languages_title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('settings.languages_description') }}
                </p>
            </div>
            <a
                href="/admin/settings/languages/create"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('settings.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div v-if="loading" class="p-8 text-center text-sm text-slate-500">
                {{ t('settings.loading') }}
            </div>
            <div v-else-if="error" class="p-8 text-center text-sm text-red-600">
                {{ error }}
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ t('settings.languages_code') }}</th>
                            <th class="px-4 py-3">{{ t('settings.languages_name') }}</th>
                            <th class="px-4 py-3">{{ t('settings.languages_native_name') }}</th>
                            <th class="px-4 py-3">{{ t('settings.languages_locale') }}</th>
                            <th class="px-4 py-3">{{ t('settings.languages_direction') }}</th>
                            <th class="px-4 py-3">{{ t('settings.languages_status') }}</th>
                            <th class="px-4 py-3 text-right">{{ t('settings.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr
                            v-for="lang in languages"
                            :key="lang.id"
                            class="hover:bg-slate-50"
                        >
                            <td class="px-4 py-3 font-medium text-slate-900">{{ lang.lcode }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ lang.name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ lang.native_name || '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ lang.locale || '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="lang.direction === 'rtl' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700'"
                                >
                                    {{ lang.direction || 'ltr' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="lang.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                >
                                    {{ lang.status === 'active' ? t('settings.languages_status_active') : t('settings.languages_status_inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        :href="`/admin/settings/languages/${lang.id}/edit`"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                        :title="t('settings.edit')"
                                    >
                                        <PencilSquareIcon class="h-4 w-4" />
                                    </a>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                        :title="t('settings.delete')"
                                        @click="openDeleteModal(lang)"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="languages.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                {{ t('settings.languages_empty') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <Teleport to="body">
            <div
                v-if="deleteModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                aria-modal="true"
                role="dialog"
            >
                <div
                    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                    @click="closeDeleteModal"
                />
                <div
                    class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <TrashIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">
                        {{ t('settings.delete_confirm_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('settings.delete_confirm_message') }}
                        <span v-if="languageToDelete" class="font-medium text-slate-900">
                            «{{ languageToDelete.name }}» ({{ languageToDelete.lcode }})
                        </span>?
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDeleteModal"
                        >
                            {{ t('settings.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDelete"
                        >
                            {{ deleting ? t('settings.loading') : t('settings.delete_confirm_confirm') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import { PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

const languages = ref([]);
const loading = ref(true);
const error = ref('');
const deleteModalOpen = ref(false);
const languageToDelete = ref(null);
const deleting = ref(false);

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

async function load() {
    setCsrf();
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.get('/admin/settings/api/languages');
        languages.value = data;
    } catch (e) {
        error.value = e.response?.data?.message || t('settings.load_error');
    } finally {
        loading.value = false;
    }
}

function openDeleteModal(lang) {
    languageToDelete.value = lang;
    deleteModalOpen.value = true;
}

function closeDeleteModal() {
    if (!deleting.value) {
        deleteModalOpen.value = false;
        languageToDelete.value = null;
    }
}

async function confirmDelete() {
    if (!languageToDelete.value) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/settings/api/languages/${languageToDelete.value.id}`);
        closeDeleteModal();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || t('settings.load_error');
    } finally {
        deleting.value = false;
    }
}

onMounted(load);
</script>

