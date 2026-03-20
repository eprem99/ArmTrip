<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.users.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.users.description') }}
                </p>
            </div>
            <a
                href="/admin/users/create"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.users.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ pagination.total }} {{ t('admin.users.count') }}
                </p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.users.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <AdminTable>
                <template #head>
                    <th class="px-4 py-3">{{ t('admin.users.col_name') }}</th>
                    <th class="px-4 py-3">{{ t('admin.users.col_email') }}</th>
                    <th class="px-4 py-3">{{ t('admin.users.col_type') }}</th>
                    <th class="px-4 py-3">{{ t('admin.users.col_created_at') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.users.action') }}</th>
                </template>

                <tr v-if="loading">
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.users.loading') }}
                    </td>
                </tr>
                <tr v-else-if="error">
                    <td colspan="4" class="px-4 py-6 text-center">
                        <p class="text-sm text-red-600">{{ error }}</p>
                        <button
                            type="button"
                            class="mt-3 rounded-lg bg-slate-200 px-3 py-1.5 text-sm hover:bg-slate-300"
                            @click="load(1)"
                        >
                            {{ t('admin.users.retry') }}
                        </button>
                    </td>
                </tr>
                <template v-else>
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="px-4 py-3 font-medium text-slate-900">{{ user.name }}</td>
                        <td class="px-4 py-3 text-slate-700 font-mono text-xs">{{ user.email }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                {{ user.type || 'user' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ formatDate(user.created_at) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    :href="`/admin/users/${user.id}/edit`"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                    :title="t('admin.users.edit')"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </a>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                    :title="t('admin.users.delete')"
                                    @click="openDeleteModal(user)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="users.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            {{ t('admin.users.empty') }}
                        </td>
                    </tr>
                </template>
            </AdminTable>

            <div
                v-if="!loading && !error && pagination.last_page > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pagination.from }}–{{ pagination.to }} {{ t('admin.users.pagination_of') }} {{ pagination.total }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="pagination.current_page <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        {{ t('admin.users.prev') }}
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
                        {{ t('admin.users.next') }}
                    </button>
                </div>
            </div>
        </div>

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
                        {{ t('admin.users.delete_confirm_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('admin.users.delete_confirm_message') }}
                        <span v-if="userToDelete" class="font-medium text-slate-900">
                            «{{ userToDelete.name }}»
                        </span>?
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDeleteModal"
                        >
                            {{ t('admin.users.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDelete"
                        >
                            {{ deleting ? t('admin.users.loading') : t('admin.users.delete_confirm_confirm') }}
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
import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import AdminTable from '../../components/AdminTable.vue';

const { t, locale } = useI18n();

const users = ref([]);
const loading = ref(true);
const error = ref('');
const searchQuery = ref('');
const searchDebounce = ref(null);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0,
});
const deleteModalOpen = ref(false);
const userToDelete = ref(null);
const deleting = ref(false);

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

function goToPage(page) {
    if (page < 1 || page > pagination.value.last_page) return;
    load(page);
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
    const params = { page, per_page: 15 };
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
    try {
        const res = await axios.get('/admin/users/api', {
            params,
            headers: { Accept: 'application/json' },
            validateStatus: () => true,
        });
        if (res.status >= 200 && res.status < 300) {
            const data = res.data;
            const list = Array.isArray(data) ? data : (data?.data ?? []);
            users.value = Array.isArray(list) ? list : [];
            pagination.value = {
                current_page: data?.current_page ?? 1,
                last_page: data?.last_page ?? 1,
                per_page: data?.per_page ?? 15,
                total: data?.total ?? users.value.length,
                from: data?.from ?? (users.value.length ? 1 : 0),
                to: data?.to ?? users.value.length,
            };
            error.value = '';
        } else {
            error.value = res.data?.message || `HTTP ${res.status}`;
        }
    } catch (e) {
        error.value = e.response?.data?.message || e.message || t('admin.users.load_error');
    } finally {
        loading.value = false;
    }
}

function openDeleteModal(user) {
    userToDelete.value = user;
    deleteModalOpen.value = true;
}

function closeDeleteModal() {
    if (!deleting.value) {
        deleteModalOpen.value = false;
        userToDelete.value = null;
    }
}

async function confirmDelete() {
    if (!userToDelete.value) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/users/api/${userToDelete.value.id}`);
        closeDeleteModal();
        await load(pagination.value.current_page);
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.users.load_error');
    } finally {
        deleting.value = false;
    }
}

onMounted(() => load(1));
</script>

