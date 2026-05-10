<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.subscribers.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.subscribers.description') }}
                </p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ pagination.total }} {{ t('admin.subscribers.count') }}
                </p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.subscribers.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <AdminTable>
                <template #head>
                    <th class="px-4 py-3">{{ t('admin.subscribers.col_email') }}</th>
                    <th class="px-4 py-3">{{ t('admin.subscribers.col_source') }}</th>
                    <th class="px-4 py-3">{{ t('admin.subscribers.col_ip') }}</th>
                    <th class="px-4 py-3">{{ t('admin.subscribers.col_created_at') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.subscribers.action') }}</th>
                </template>

                <tr v-if="loading">
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.subscribers.loading') }}
                    </td>
                </tr>
                <tr v-else-if="error">
                    <td colspan="5" class="px-4 py-6 text-center">
                        <p class="text-sm text-red-600">{{ error }}</p>
                        <button
                            type="button"
                            class="mt-3 rounded-lg bg-slate-200 px-3 py-1.5 text-sm hover:bg-slate-300"
                            @click="load(1)"
                        >
                            {{ t('admin.subscribers.retry') }}
                        </button>
                    </td>
                </tr>
                <template v-else>
                    <tr
                        v-for="row in subscribers"
                        :key="row.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="px-4 py-3 font-mono text-xs text-slate-800">{{ row.email }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                {{ row.source || '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ row.ip || '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ formatDate(row.created_at) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                    :title="t('admin.subscribers.delete')"
                                    @click="openDeleteModal(row)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="subscribers.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            {{ t('admin.subscribers.empty') }}
                        </td>
                    </tr>
                </template>
            </AdminTable>

            <div
                v-if="!loading && !error && pagination.last_page > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pagination.from }}–{{ pagination.to }} {{ t('admin.subscribers.pagination_of') }} {{ pagination.total }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="pagination.current_page <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        {{ t('admin.subscribers.prev') }}
                    </button>
                    <button
                        type="button"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page + 1)"
                    >
                        {{ t('admin.subscribers.next') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete confirm -->
        <div v-if="deleteOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/50" @click="closeDeleteModal" />
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900">{{ t('admin.subscribers.delete_confirm_title') }}</h3>
                <p class="mt-2 text-sm text-slate-600">
                    {{ t('admin.subscribers.delete_confirm_message') }}
                    <span class="font-medium text-slate-900">{{ subscriberToDelete?.email }}</span>?
                </p>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        @click="closeDeleteModal"
                    >
                        {{ t('admin.subscribers.delete_confirm_cancel') }}
                    </button>
                    <button
                        type="button"
                        :disabled="deleting"
                        class="rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                        @click="confirmDelete"
                    >
                        {{ deleting ? t('admin.subscribers.loading') : t('admin.subscribers.delete_confirm_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import AdminTable from '../../components/AdminTable.vue';
import { useI18n } from '../../composables/useI18n';

const { t } = useI18n();

const loading = ref(false);
const error = ref('');
const subscribers = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const searchQuery = ref('');
let searchTimer = null;

const deleteOpen = ref(false);
const subscriberToDelete = ref(null);
const deleting = ref(false);

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    try {
        return new Date(dateStr).toLocaleString();
    } catch {
        return dateStr;
    }
}

async function load(page = 1) {
    setCsrf();
    loading.value = true;
    error.value = '';
    try {
        const res = await axios.get('/admin/subscribers/api', {
            params: { page, search: searchQuery.value || '', per_page: 15 },
        });
        const data = res.data;
        subscribers.value = Array.isArray(data?.data) ? data.data : [];
        pagination.value = {
            current_page: data.current_page ?? page,
            last_page: data.last_page ?? 1,
            total: data.total ?? 0,
            from: data.from ?? 0,
            to: data.to ?? 0,
        };
    } catch (e) {
        error.value = e.response?.data?.message || e.message || t('admin.subscribers.load_error');
    } finally {
        loading.value = false;
    }
}

function goToPage(page) {
    load(page);
}

function onSearchInput() {
    if (searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(() => load(1), 300);
}

function openDeleteModal(row) {
    subscriberToDelete.value = row;
    deleteOpen.value = true;
}

function closeDeleteModal() {
    deleteOpen.value = false;
    subscriberToDelete.value = null;
}

async function confirmDelete() {
    if (!subscriberToDelete.value) return;
    deleting.value = true;
    setCsrf();
    try {
        await axios.delete(`/admin/subscribers/api/${subscriberToDelete.value.id}`);
        closeDeleteModal();
        load(pagination.value.current_page || 1);
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.subscribers.load_error');
    } finally {
        deleting.value = false;
    }
}

load(1);
</script>

