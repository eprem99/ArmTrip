<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.taxonomies.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.taxonomies.description') }}
                </p>
            </div>
            <a
                href="/admin/settings/taxonomies/create"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.taxonomies.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ filteredTaxonomies.length }} {{ t('admin.taxonomies.count') }}
                </p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.taxonomies.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <div v-if="loading" class="p-8 text-center text-sm text-slate-500">
                {{ t('admin.taxonomies.loading') }}
            </div>
            <div v-else-if="error" class="p-8 text-center text-sm text-red-600">
                {{ error }}
            </div>
            <AdminTable v-else>
                <template #head>
                    <th class="w-12 px-4 py-3" />
                    <th class="px-4 py-3">{{ t('admin.taxonomies.col_name') }}</th>
                    <th class="px-4 py-3">{{ t('admin.taxonomies.col_slug') }}</th>
                    <th class="px-4 py-3">{{ t('admin.taxonomies.col_type') }}</th>
                    <th class="px-4 py-3">{{ t('admin.taxonomies.col_terms') }}</th>
                    <th class="px-4 py-3">{{ t('admin.taxonomies.col_created_at') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.taxonomies.action') }}</th>
                </template>

                <tr
                    v-for="row in pagedRows"
                    :key="row.id"
                    class="hover:bg-slate-50"
                >
                    <td class="px-4 py-3 text-slate-500">
                        <component
                            :is="getTaxonomyIconComponent(row.icon)"
                            class="h-5 w-5"
                        />
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ row.name }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ row.slug }}</td>
                    <td class="px-4 py-3">
                        <span
                            class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="row.type === 'tag' ? 'bg-violet-50 text-violet-800' : 'bg-slate-100 text-slate-700'"
                        >
                            {{ row.type === 'tag' ? t('admin.taxonomies.type_tag') : t('admin.taxonomies.type_category') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ row.terms_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ formatDate(row.created_at) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a
                                :href="`/admin/settings/taxonomies/${encodeURIComponent(row.slug)}/edit`"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                :title="t('admin.taxonomies.edit')"
                            >
                                <PencilSquareIcon class="h-4 w-4" />
                            </a>
                            <button
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                :title="t('admin.taxonomies.delete')"
                                @click="openDeleteTaxonomy(row)"
                            >
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="filteredTaxonomies.length === 0">
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.taxonomies.empty') }}
                    </td>
                </tr>
            </AdminTable>

            <div
                v-if="!loading && !error && totalPages > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pageFrom }}–{{ pageTo }} {{ t('admin.taxonomies.pagination_of') }} {{ filteredTaxonomies.length }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="currentPage <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(currentPage - 1)"
                    >
                        {{ t('admin.taxonomies.prev') }}
                    </button>
                    <span class="text-sm text-slate-500">
                        {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                        type="button"
                        :disabled="currentPage >= totalPages"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(currentPage + 1)"
                    >
                        {{ t('admin.taxonomies.next') }}
                    </button>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="deleteTaxonomyModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeDeleteTaxonomy" />
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <TrashIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">
                        {{ t('admin.taxonomies.delete_taxonomy_confirm_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('admin.taxonomies.delete_taxonomy_confirm_message') }}
                        <span v-if="taxonomyToDelete" class="font-medium text-slate-900">«{{ taxonomyToDelete.name }}»</span>
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDeleteTaxonomy"
                        >
                            {{ t('admin.taxonomies.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="taxonomyDeleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDeleteTaxonomy"
                        >
                            {{ taxonomyDeleting ? t('admin.taxonomies.loading') : t('admin.taxonomies.delete_confirm_confirm') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import { PlusIcon, PencilSquareIcon, TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import AdminTable from '../../components/AdminTable.vue';
import { getTaxonomyIconComponent } from '../../data/taxonomyIcons';

const { t } = useI18n();

const taxonomies = ref([]);
const searchQuery = ref('');
const searchDebounce = ref(null);
const currentPage = ref(1);
const perPage = ref(15);
const loading = ref(true);
const error = ref('');

const deleteTaxonomyModalOpen = ref(false);
const taxonomyToDelete = ref(null);
const taxonomyDeleting = ref(false);

function localeForDate() {
    const loc = typeof window !== 'undefined' ? window.__locale : 'en';
    if (loc === 'ru') return 'ru-RU';
    if (loc === 'am') return 'hy-AM';
    return 'en-US';
}

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
        return d.toLocaleDateString(localeForDate(), {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return value;
    }
}

async function load() {
    setCsrf();
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.get('/admin/blog/api/taxonomies');
        taxonomies.value = Array.isArray(data) ? data : [];
        currentPage.value = 1;
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.taxonomies.load_error');
    } finally {
        loading.value = false;
    }
}

const filteredTaxonomies = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return taxonomies.value;
    return taxonomies.value.filter((row) => {
        const name = (row.name || '').toLowerCase();
        const slug = (row.slug || '').toLowerCase();
        const type = (row.type || '').toLowerCase();
        return name.includes(q) || slug.includes(q) || type.includes(q);
    });
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredTaxonomies.value.length / perPage.value)));

const pagedRows = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filteredTaxonomies.value.slice(start, start + perPage.value);
});

const pageFrom = computed(() => {
    if (filteredTaxonomies.value.length === 0) return 0;
    return (currentPage.value - 1) * perPage.value + 1;
});

const pageTo = computed(() => {
    return Math.min(currentPage.value * perPage.value, filteredTaxonomies.value.length);
});

function onSearchInput() {
    if (searchDebounce.value) clearTimeout(searchDebounce.value);
    searchDebounce.value = setTimeout(() => {
        currentPage.value = 1;
    }, 200);
}

function goToPage(p) {
    if (p < 1 || p > totalPages.value) return;
    currentPage.value = p;
}

function openDeleteTaxonomy(row) {
    taxonomyToDelete.value = row;
    deleteTaxonomyModalOpen.value = true;
}

function closeDeleteTaxonomy() {
    deleteTaxonomyModalOpen.value = false;
    taxonomyToDelete.value = null;
}

async function confirmDeleteTaxonomy() {
    if (!taxonomyToDelete.value) return;
    setCsrf();
    taxonomyDeleting.value = true;
    try {
        await axios.delete(`/admin/blog/api/taxonomies/${encodeURIComponent(taxonomyToDelete.value.slug)}`);
        closeDeleteTaxonomy();
        await load();
        window.location.reload();
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.taxonomies.load_error');
        closeDeleteTaxonomy();
    } finally {
        taxonomyDeleting.value = false;
    }
}

onMounted(() => {
    load();
});
</script>
