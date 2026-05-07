<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">
                    <a href="/admin/rentals" class="font-medium text-blue-600 hover:text-blue-800">{{ t('admin.rentals_amenities.back_rentals') }}</a>
                </p>
                <h1 class="mt-2 text-xl font-semibold text-slate-900">{{ t('admin.rentals_amenities.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ t('admin.rentals_amenities.description') }}</p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                @click="openCreate"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.rentals_amenities.new') }}
            </button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">{{ pagination.total }} {{ t('admin.rentals_amenities.count') }}</p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.rentals_amenities.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <AdminTable>
                <template #head>
                    <th class="px-4 py-3">{{ t('admin.rentals_amenities.col_name') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals_amenities.col_slug') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals_amenities.col_icon') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.rentals_amenities.action') }}</th>
                </template>
                <tr v-if="loading">
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ t('admin.rentals_amenities.loading') }}</td>
                </tr>
                <tr v-else-if="error">
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-red-600">{{ error }}</td>
                </tr>
                <template v-else>
                    <tr v-for="row in rows" :key="row.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ row.name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ row.slug }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ row.icon || '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                    :title="t('admin.rentals_amenities.edit')"
                                    @click="openEdit(row)"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                    :title="t('admin.rentals_amenities.delete')"
                                    @click="openDelete(row)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="rows.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ t('admin.rentals_amenities.empty') }}</td>
                    </tr>
                </template>
            </AdminTable>

            <div
                v-if="!loading && !error && pagination.last_page > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pagination.from }}–{{ pagination.to }} {{ t('admin.rentals_amenities.pagination_of') }} {{ pagination.total }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="pagination.current_page <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        {{ t('admin.rentals_amenities.prev') }}
                    </button>
                    <span class="text-sm text-slate-500">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
                    <button
                        type="button"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page + 1)"
                    >
                        {{ t('admin.rentals_amenities.next') }}
                    </button>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="formOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeForm" />
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">
                        {{ editingId ? t('admin.rentals_amenities.form_edit_title') : t('admin.rentals_amenities.form_create_title') }}
                    </h3>
                    <div class="mt-4 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600">{{ t('admin.rentals_amenities.form_name') }}</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">{{ t('admin.rentals_amenities.form_slug') }}</label>
                            <input
                                v-model="form.slug"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm"
                                :placeholder="t('admin.rentals_amenities.form_slug_placeholder')"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">{{ t('admin.rentals_amenities.form_icon') }}</label>
                            <input
                                v-model="form.icon"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm"
                                :placeholder="t('admin.rentals_amenities.form_icon_placeholder')"
                            />
                        </div>
                        <p v-if="formError" class="text-sm text-red-600">{{ formError }}</p>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeForm"
                        >
                            {{ t('admin.rentals_amenities.cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="saving"
                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                            @click="submitForm"
                        >
                            {{ saving ? t('admin.rentals_amenities.saving') : t('admin.rentals_amenities.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <Teleport to="body">
            <div
                v-if="deleteOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeDelete" />
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">{{ t('admin.rentals_amenities.delete_confirm_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ t('admin.rentals_amenities.delete_confirm_message') }}</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDelete"
                        >
                            {{ t('admin.rentals_amenities.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDelete"
                        >
                            {{ deleting ? t('admin.rentals_amenities.loading') : t('admin.rentals_amenities.delete_confirm_confirm') }}
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

const { t } = useI18n();

const rows = ref([]);
const loading = ref(true);
const error = ref('');
const searchQuery = ref('');
const searchDebounce = ref(null);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    from: 0,
    to: 0,
});

const formOpen = ref(false);
const editingId = ref(null);
const form = ref({ name: '', slug: '', icon: '' });
const formError = ref('');
const saving = ref(false);

const deleteOpen = ref(false);
const rowToDelete = ref(null);
const deleting = ref(false);

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

function goToPage(page) {
    if (page < 1 || page > pagination.value.last_page) return;
    load(page);
}

function onSearchInput() {
    if (searchDebounce.value) clearTimeout(searchDebounce.value);
    searchDebounce.value = setTimeout(() => load(1), 300);
}

async function load(page = 1) {
    setCsrf();
    loading.value = true;
    error.value = '';
    try {
        const params = { page, per_page: 50 };
        if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
        const res = await axios.get('/admin/rentals/api/amenities', {
            params,
            headers: { Accept: 'application/json' },
            validateStatus: () => true,
        });
        if (res.status === 403) {
            error.value = t('admin.rentals.forbidden');
            rows.value = [];
            return;
        }
        if (res.status >= 200 && res.status < 300) {
            const data = res.data;
            rows.value = Array.isArray(data.data) ? data.data : [];
            pagination.value = {
                current_page: data.current_page ?? 1,
                last_page: data.last_page ?? 1,
                total: data.total ?? rows.value.length,
                from: data.from ?? (rows.value.length ? 1 : 0),
                to: data.to ?? rows.value.length,
            };
        } else {
            error.value = res.data?.message || `HTTP ${res.status}`;
        }
    } catch (e) {
        error.value = e.response?.data?.message || e.message || t('admin.rentals_amenities.load_error');
    } finally {
        loading.value = false;
    }
}

function openCreate() {
    editingId.value = null;
    form.value = { name: '', slug: '', icon: '' };
    formError.value = '';
    formOpen.value = true;
}

function openEdit(row) {
    editingId.value = row.id;
    form.value = {
        name: row.name || '',
        slug: row.slug || '',
        icon: row.icon || '',
    };
    formError.value = '';
    formOpen.value = true;
}

function closeForm() {
    if (!saving.value) formOpen.value = false;
}

async function submitForm() {
    formError.value = '';
    if (!form.value.name?.trim()) {
        formError.value = t('admin.rentals_amenities.form_name_required');
        return;
    }
    setCsrf();
    saving.value = true;
    try {
        const payload = {
            name: form.value.name.trim(),
            slug: form.value.slug?.trim() || null,
            icon: form.value.icon?.trim() || null,
        };
        if (editingId.value) {
            await axios.put(`/admin/rentals/api/amenities/${editingId.value}`, payload, {
                headers: { Accept: 'application/json' },
            });
        } else {
            await axios.post('/admin/rentals/api/amenities', payload, {
                headers: { Accept: 'application/json' },
            });
        }
        formOpen.value = false;
        await load(pagination.value.current_page);
    } catch (e) {
        const msg = e.response?.data?.message;
        const errs = e.response?.data?.errors;
        formError.value = errs ? Object.values(errs).flat().join(' ') : (msg || e.message);
    } finally {
        saving.value = false;
    }
}

function openDelete(row) {
    rowToDelete.value = row;
    deleteOpen.value = true;
}

function closeDelete() {
    if (!deleting.value) {
        deleteOpen.value = false;
        rowToDelete.value = null;
    }
}

async function confirmDelete() {
    if (!rowToDelete.value) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/rentals/api/amenities/${rowToDelete.value.id}`);
        closeDelete();
        await load(pagination.value.current_page);
    } catch (e) {
        error.value = e.response?.data?.message || e.message;
    } finally {
        deleting.value = false;
    }
}

onMounted(() => load(1));
</script>
