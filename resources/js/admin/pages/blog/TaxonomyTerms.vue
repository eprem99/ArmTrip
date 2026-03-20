<!--
  Admin: terms (vocabulary items) for one taxonomy.
  Route: /admin/blog/taxonomies/{slug}
-->
<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">
                    <a
                        href="/admin/blog/posts"
                        class="font-medium text-blue-600 hover:text-blue-800"
                    >{{ t('admin.taxonomies.back_blog') }}</a>
                    <span class="mx-1.5 text-slate-400">·</span>
                    <a
                        href="/admin/settings/taxonomies"
                        class="font-medium text-blue-600 hover:text-blue-800"
                    >{{ t('admin.taxonomies.back_taxonomies') }}</a>
                </p>
                <h1 class="mt-2 text-xl font-semibold text-slate-900">
                    {{ taxonomy?.name || '…' }}
                </h1>
            </div>
            <a
                v-if="taxonomy"
                :href="newTermHref"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.taxonomies.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div v-if="loading" class="p-8 text-center text-sm text-slate-500">
                {{ t('admin.taxonomies.loading') }}
            </div>
            <div v-else-if="error" class="p-8 text-center text-sm text-red-600">
                {{ error }}
            </div>
            <template v-else-if="taxonomy">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                    <p class="text-sm text-slate-600">
                        <span class="font-medium text-slate-800">{{ filteredTerms.length }}</span>
                        {{ t('admin.taxonomies.terms_count_label') }}
                        <span
                            v-if="searchQuery.trim() && terms.length !== filteredTerms.length"
                            class="text-slate-400"
                        >
                            ({{ terms.length }} {{ t('admin.taxonomies.terms_total_hint') }})
                        </span>
                    </p>
                    <div class="relative">
                        <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input
                            v-model="searchQuery"
                            type="search"
                            :placeholder="t('admin.taxonomies.search_terms_placeholder')"
                            class="w-full min-w-[12rem] rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:w-64"
                        />
                    </div>
                </div>

                <AdminTable>
                    <template #head>
                        <th class="px-4 py-3">{{ t('admin.taxonomies.col_term_name') }}</th>
                        <th class="px-4 py-3">{{ t('admin.taxonomies.col_term_status') }}</th>
                        <th class="px-4 py-3">{{ t('admin.taxonomies.col_term_slug') }}</th>
                        <th class="px-4 py-3">{{ t('admin.taxonomies.col_term_parent') }}</th>
                        <th class="px-4 py-3">{{ t('admin.taxonomies.col_term_description') }}</th>
                        <th class="px-4 py-3">{{ t('admin.taxonomies.col_term_created_at') }}</th>
                        <th class="px-4 py-3 text-right">{{ t('admin.taxonomies.action') }}</th>
                    </template>
                    <tr
                        v-for="term in filteredTerms"
                        :key="term.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="px-4 py-3 font-medium text-slate-900">{{ term.name }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="termStatusClass(term)"
                            >
                                {{ termStatusLabel(term) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ term.slug }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            <span v-if="term.parent">{{ term.parent.name }}</span>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="max-w-xs px-4 py-3 text-sm text-slate-600">
                            <span class="line-clamp-2" :title="term.description || ''">{{ termDescription(term) }}</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ formatDate(term.created_at) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    :href="editTermHref(term)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                    :title="t('admin.taxonomies.edit')"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </a>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                    :title="t('admin.taxonomies.delete')"
                                    @click="openDeleteTerm(term)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredTerms.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                            {{ terms.length === 0 ? t('admin.taxonomies.terms_empty') : t('admin.taxonomies.search_no_results') }}
                        </td>
                    </tr>
                </AdminTable>
            </template>
        </div>

        <Teleport to="body">
            <div
                v-if="deleteTermModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeDeleteTerm" />
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <TrashIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">
                        {{ t('admin.taxonomies.delete_term_confirm_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('admin.taxonomies.delete_term_confirm_message') }}
                        <span v-if="termToDelete" class="font-medium text-slate-900">«{{ termToDelete.name }}»</span>
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDeleteTerm"
                        >
                            {{ t('admin.taxonomies.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="termDeleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDeleteTerm"
                        >
                            {{ termDeleting ? t('admin.taxonomies.loading') : t('admin.taxonomies.delete_confirm_confirm') }}
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

const { t } = useI18n();

const taxonomy = ref(null);
const searchQuery = ref('');
const loading = ref(true);
const error = ref('');

const terms = computed(() => taxonomy.value?.terms || []);

const filteredTerms = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return terms.value;
    return terms.value.filter((term) => {
        const name = (term.name || '').toLowerCase();
        const slug = (term.slug || '').toLowerCase();
        const desc = (term.description || '').toLowerCase();
        const parentName = (term.parent?.name || '').toLowerCase();
        const st = (term.status || '').toLowerCase();
        return name.includes(q) || slug.includes(q) || desc.includes(q) || parentName.includes(q) || st.includes(q);
    });
});

const newTermHref = computed(() => {
    const slug = taxonomy.value?.slug || taxonomySlugFromPath();
    return slug ? `/admin/blog/taxonomies/${encodeURIComponent(slug)}/terms/create` : '#';
});

function editTermHref(term) {
    const slug = taxonomy.value?.slug || taxonomySlugFromPath();
    return `/admin/blog/taxonomies/${encodeURIComponent(slug)}/terms/${term.id}/edit`;
}

const deleteTermModalOpen = ref(false);
const termToDelete = ref(null);
const termDeleting = ref(false);

function taxonomySlugFromPath() {
    if (typeof window === 'undefined') return '';
    const m = window.location.pathname.match(/^\/admin\/blog\/taxonomies\/([^/]+)\/?$/);
    return m ? m[1] : '';
}

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

function termDescription(term) {
    const d = term.description;
    if (!d || !String(d).trim()) return '—';
    return String(d).trim();
}

function termStatusLabel(term) {
    return term.status === 'draft'
        ? t('admin.taxonomies.term_status_draft')
        : t('admin.taxonomies.term_status_published');
}

function termStatusClass(term) {
    return term.status === 'draft'
        ? 'bg-amber-100 text-amber-900'
        : 'bg-emerald-100 text-emerald-800';
}

async function load() {
    setCsrf();
    const slug = taxonomySlugFromPath();
    if (!slug) {
        error.value = t('admin.taxonomies.detail_not_found');
        loading.value = false;
        return;
    }

    loading.value = true;
    error.value = '';
    taxonomy.value = null;

    try {
        const { data } = await axios.get(`/admin/blog/api/taxonomies/${encodeURIComponent(slug)}`);
        taxonomy.value = data;
    } catch (e) {
        if (e.response?.status === 404) {
            error.value = t('admin.taxonomies.detail_not_found');
        } else {
            error.value = e.response?.data?.message || t('admin.taxonomies.detail_load_error');
        }
    } finally {
        loading.value = false;
    }
}

function openDeleteTerm(term) {
    termToDelete.value = term;
    deleteTermModalOpen.value = true;
}

function closeDeleteTerm() {
    deleteTermModalOpen.value = false;
    termToDelete.value = null;
}

async function confirmDeleteTerm() {
    if (!termToDelete.value) return;
    const tSlug = taxonomy.value?.slug || taxonomySlugFromPath();
    if (!tSlug) return;

    setCsrf();
    termDeleting.value = true;
    try {
        await axios.delete(
            `/admin/blog/api/taxonomies/${encodeURIComponent(tSlug)}/terms/${termToDelete.value.id}`,
        );
        closeDeleteTerm();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.taxonomies.save_error');
        closeDeleteTerm();
    } finally {
        termDeleting.value = false;
    }
}

onMounted(() => {
    load();
});
</script>
