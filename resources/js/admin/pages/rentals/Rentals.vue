<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.rentals.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.rentals.description') }}
                </p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ pagination.total }} {{ t('admin.rentals.count') }}
                </p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.rentals.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <AdminTable>
                <template #head>
                    <th class="px-4 py-3">{{ t('admin.rentals.col_title') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals.col_slug') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals.col_type_location') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals.col_owner') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals.col_status') }}</th>
                    <th class="px-4 py-3">{{ t('admin.rentals.col_price') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.rentals.action') }}</th>
                </template>

                <tr v-if="loading">
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.rentals.loading') }}
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
                            {{ t('admin.rentals.retry') }}
                        </button>
                    </td>
                </tr>
                <template v-else>
                    <tr
                        v-for="row in rentals"
                        :key="row.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="px-4 py-3 font-medium text-slate-900">{{ row.title }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ row.slug }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            <span v-if="row.type && row.location">{{ row.type.name }} · {{ row.location.name }}</span>
                            <span v-else>—</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            <span v-if="row.owner">{{ row.owner.name }}</span>
                            <span v-else>—</span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="statusClass(row)"
                            >
                                {{ statusLabel(row) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            {{ row.base_price }} {{ row.currency }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    v-if="row.type && row.location"
                                    :href="publicUrl(row)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                    :title="t('admin.rentals.view_public')"
                                >
                                    <ArrowTopRightOnSquareIcon class="h-4 w-4" />
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="rentals.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            {{ t('admin.rentals.empty') }}
                        </td>
                    </tr>
                </template>
            </AdminTable>

            <div
                v-if="!loading && !error && pagination.last_page > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pagination.from }}–{{ pagination.to }} {{ t('admin.rentals.pagination_of') }} {{ pagination.total }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="pagination.current_page <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        {{ t('admin.rentals.prev') }}
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
                        {{ t('admin.rentals.next') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import { MagnifyingGlassIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline';
import AdminTable from '../../components/AdminTable.vue';

const { t } = useI18n();

const rentals = ref([]);
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

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function publicUrl(row) {
    const ts = row.type?.slug;
    const ls = row.location?.slug;
    const s = row.slug;
    if (!ts || !ls || !s) return '#';
    return `/rentals/${ts}/${ls}/${s}`;
}

function statusLabel(row) {
    if (!row.is_active) {
        return t('admin.rentals.status_inactive');
    }
    if (!row.published_at) {
        return t('admin.rentals.status_draft');
    }
    const pub = new Date(row.published_at);
    if (pub > new Date()) {
        return t('admin.rentals.status_scheduled');
    }
    return t('admin.rentals.status_published');
}

function statusClass(row) {
    if (!row.is_active) {
        return 'bg-red-50 text-red-700';
    }
    if (!row.published_at) {
        return 'bg-slate-100 text-slate-600';
    }
    const pub = new Date(row.published_at);
    if (pub > new Date()) {
        return 'bg-amber-50 text-amber-800';
    }
    return 'bg-emerald-50 text-emerald-700';
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
        const res = await axios.get('/admin/rentals/api', {
            params,
            headers: { Accept: 'application/json' },
            validateStatus: () => true,
        });
        if (res.status === 403) {
            error.value = t('admin.rentals.forbidden');
            rentals.value = [];
            return;
        }
        if (res.status >= 200 && res.status < 300) {
            const payload = res.data;
            rentals.value = Array.isArray(payload.data) ? payload.data : [];
            const meta = payload.meta ?? {};
            pagination.value = {
                current_page: meta.current_page ?? 1,
                last_page: meta.last_page ?? 1,
                per_page: meta.per_page ?? 15,
                total: meta.total ?? rentals.value.length,
                from: meta.from ?? (rentals.value.length ? 1 : 0),
                to: meta.to ?? rentals.value.length,
            };
            error.value = '';
        } else {
            error.value = payloadMessage(res.data) || `HTTP ${res.status}`;
        }
    } catch (e) {
        error.value = e.response?.data?.message || e.message || t('admin.rentals.load_error');
    } finally {
        loading.value = false;
    }
}

function payloadMessage(data) {
    if (!data || typeof data !== 'object') return '';
    return data.message || '';
}

onMounted(() => load(1));
</script>
