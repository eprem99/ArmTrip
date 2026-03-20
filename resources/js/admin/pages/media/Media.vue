<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.sidebar.media') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.media_description') }}
                </p>
            </div>
            <a
                href="/admin/media/create"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.media.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ pagination.total }} {{ t('admin.media.count') }}
                </p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.media.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>
            <AdminTable>
                <template #head>
                    <th class="w-16 shrink-0 px-4 py-3">{{ t('admin.media.col_preview') }}</th>
                    <th class="px-4 py-3">{{ t('admin.media.col_title') }}</th>
                    <th class="px-4 py-3">{{ t('admin.media.col_filename') }}</th>
                    <th class="px-4 py-3">{{ t('admin.media.col_type') }}</th>
                    <th class="px-4 py-3">{{ t('admin.media.col_size') }}</th>
                    <th class="px-4 py-3">{{ t('admin.media.col_date') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.media.action') }}</th>
                </template>

                <tr v-if="loading">
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.media.loading') }}
                    </td>
                </tr>
                <tr v-else-if="error">
                    <td colspan="7" class="px-4 py-6 text-center">
                        <p class="text-sm text-red-600">{{ error }}</p>
                        <button
                            type="button"
                            class="mt-3 rounded-lg bg-slate-200 px-3 py-1.5 text-sm hover:bg-slate-300"
                            @click="load(1)"
                        >
                            {{ t('admin.media.retry') }}
                        </button>
                    </td>
                </tr>
                <template v-else>
                    <tr
                        v-for="item in mediaList"
                        :key="item.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="px-4 py-3">
                            <div class="h-12 w-12 overflow-hidden rounded-lg bg-slate-100">
                                <img
                                    v-if="item.mime_type && item.mime_type.startsWith('image/')"
                                    :src="item.url"
                                    :alt="item.alt || item.title || item.filename"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    @error="(e) => e.target.style.display = 'none'"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-slate-400"
                                >
                                    <DocumentIcon class="h-6 w-6" />
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ item.title || item.filename }}
                        </td>
                        <td class="px-4 py-3 text-slate-700 font-mono text-xs">
                            {{ item.filename }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ item.mime_type }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ formatSize(item.size) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ formatDate(item.created_at) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    :href="`/admin/media/${item.id}/edit`"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                    :title="t('admin.media.edit')"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </a>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                    :title="t('admin.media.delete')"
                                    @click="openDeleteModal(item)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="mediaList.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            {{ t('admin.media.empty') }}
                        </td>
                    </tr>
                </template>
            </AdminTable>

            <div
                v-if="!loading && !error && pagination.last_page > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pagination.from }}–{{ pagination.to }} {{ t('admin.media.pagination_of') }} {{ pagination.total }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="pagination.current_page <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        {{ t('admin.media.prev') }}
                    </button>
                    <span class="text-sm text-slate-500">
                        {{ pagination.current_page }} / {{ pagination.last_page }}
                    </span>
                    <button
                        type="button"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page + 1)"
                    >
                        {{ t('admin.media.next') }}
                    </button>
                </div>
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
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeDeleteModal" />
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">{{ t('admin.media.delete_confirm_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('admin.media.delete_confirm_message') }}
                        <span v-if="mediaToDelete" class="font-medium text-slate-900">«{{ mediaToDelete.title || mediaToDelete.filename }}»</span>?
                    </p>
                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            @click="closeDeleteModal"
                        >
                            {{ t('admin.media.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDelete"
                        >
                            {{ deleting ? t('admin.media.loading') : t('admin.media.delete_confirm_confirm') }}
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
import { PlusIcon, DocumentIcon, PencilSquareIcon, TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import AdminTable from '../../components/AdminTable.vue';

const { t, locale } = useI18n();

const mediaList = ref([]);
const loading = ref(true);
const error = ref('');
const searchQuery = ref('');
const searchDebounce = ref(null);
const deleteModalOpen = ref(false);
const mediaToDelete = ref(null);
const deleting = ref(false);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0,
});

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function formatDate(value) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        return d.toLocaleDateString(locale.value === 'ru' ? 'ru-RU' : 'en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return value;
    }
}

function formatSize(bytes) {
    if (bytes == null || bytes === 0) return '—';
    const units = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    let n = bytes;
    while (n >= 1024 && i < units.length - 1) {
        n /= 1024;
        i++;
    }
    return (i === 0 ? n : n.toFixed(1)) + ' ' + units[i];
}

function goToPage(page) {
    if (page < 1 || page > pagination.value.last_page) return;
    load(page);
}

function openDeleteModal(item) {
    mediaToDelete.value = item;
    deleteModalOpen.value = true;
}

function closeDeleteModal() {
    if (!deleting.value) {
        deleteModalOpen.value = false;
        mediaToDelete.value = null;
    }
}

async function confirmDelete() {
    if (!mediaToDelete.value) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/media/api/${mediaToDelete.value.id}`);
        closeDeleteModal();
        await load(pagination.value.current_page);
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.media.load_error');
    } finally {
        deleting.value = false;
    }
}

function onSearchInput() {
    if (searchDebounce.value) clearTimeout(searchDebounce.value);
    searchDebounce.value = setTimeout(() => {
        load(1);
    }, 300);
}

async function load(page = 1) {
    setCsrf();
    loading.value = true;
    error.value = '';
    const url = (typeof window !== 'undefined' ? window.location.origin : '') + '/admin/media-list-json';
    const params = { page, per_page: 15 };
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
    try {
        const res = await axios.get(url, {
            params,
            headers: { Accept: 'application/json' },
            validateStatus: () => true,
        });
        if (res.status >= 200 && res.status < 300) {
            const data = res.data;
            const list = Array.isArray(data) ? data : (data?.data ?? []);
            mediaList.value = Array.isArray(list) ? list : [];
            pagination.value = {
                current_page: data?.current_page ?? 1,
                last_page: data?.last_page ?? 1,
                per_page: data?.per_page ?? 15,
                total: data?.total ?? 0,
                from: data?.from ?? 0,
                to: data?.to ?? 0,
            };
            error.value = '';
        } else {
            const body = res.data?.message || (typeof res.data === 'string' ? res.data.slice(0, 150) : '');
            error.value = body ? `HTTP ${res.status}: ${body}` : `HTTP ${res.status}`;
        }
    } catch (e) {
        const msg = e.response
            ? `HTTP ${e.response.status}: ${e.response.data?.message || ''}`
            : (e.message || t('admin.media.load_error'));
        error.value = msg;
    } finally {
        loading.value = false;
    }
}

onMounted(() => load(1));
</script>

