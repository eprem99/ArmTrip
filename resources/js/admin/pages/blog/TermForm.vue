<!--
  Admin: create / edit term for a taxonomy.
  Routes: /admin/blog/taxonomies/{slug}/terms/create
          /admin/blog/taxonomies/{slug}/terms/{id}/edit
-->
<template>
    <div class="space-y-6">
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
            <a
                :href="termsListHref"
                class="mt-2 inline-block text-sm font-medium text-blue-600 hover:text-blue-800"
            >
                ← {{ t('admin.taxonomies.term_form_back') }}
            </a>
            <h1 class="mt-2 text-xl font-semibold text-slate-900">
                {{ isEdit ? t('admin.taxonomies.edit_term') : t('admin.taxonomies.new_term') }}
            </h1>
            <p
                v-if="taxonomy"
                class="mt-1 text-sm text-slate-600"
            >
                <span class="font-medium text-slate-800">{{ taxonomy.name }}</span>
                <span class="mx-1.5 text-slate-400">·</span>
                <span class="font-mono text-xs text-slate-500">{{ taxonomy.slug }}</span>
            </p>
        </div>

        <div v-if="pageLoading" class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
            {{ t('admin.taxonomies.loading') }}
        </div>
        <div v-else-if="pageError" class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-red-600">
            {{ pageError }}
        </div>
        <form
            v-else-if="taxonomy"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            @submit.prevent="save"
        >
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.taxonomies.form_name') }}</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.taxonomies.form_slug') }}</label>
                            <input
                                v-model="form.slug"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                            <p class="mt-1 text-xs text-slate-500">{{ t('admin.taxonomies.form_slug_hint') }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.taxonomies.form_parent') }}</label>
                            <select
                                v-model="formParentSelect"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">{{ t('admin.taxonomies.form_no_parent') }}</option>
                                <option
                                    v-for="opt in parentOptions"
                                    :key="opt.id"
                                    :value="String(opt.id)"
                                >
                                    {{ opt.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.taxonomies.form_description') }}</label>
                            <textarea
                                v-model="form.description"
                                rows="6"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </div>

                <p
                    v-if="saveError"
                    class="text-sm text-red-600 lg:hidden"
                >
                    {{ saveError }}
                </p>

                <div class="flex flex-wrap gap-3">
                    <a
                        :href="termsListHref"
                        class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                    >
                        {{ t('admin.taxonomies.form_cancel') }}
                    </a>
                </div>
            </div>

            <aside class="lg:sticky lg:top-20 lg:col-span-1">
                <TermPublishCard
                    :title="t('admin.taxonomies.term_publish_box')"
                    :status="form.status"
                    :status-edit-open="statusEditOpen"
                    :is-edit="isEdit"
                    :saving="saving"
                    :save-error="saveError"
                    :status-text="statusDisplayText"
                    :updated-at="formattedTermUpdatedAt"
                    :status-label="t('admin.taxonomies.form_status')"
                    :edit-label="t('admin.content.edit_link')"
                    :draft-label="t('admin.taxonomies.term_status_draft')"
                    :published-label="t('admin.taxonomies.term_status_published')"
                    :visibility-hint="t('admin.taxonomies.term_visibility_hint')"
                    :updated-at-label="t('admin.taxonomies.term_updated_at')"
                    :submit-text="saving ? t('admin.taxonomies.form_saving') : t('admin.taxonomies.form_save')"
                    @toggle-status-edit="statusEditOpen = !statusEditOpen"
                    @update:status="onStatusChange"
                />
            </aside>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import TermPublishCard from '../../components/taxonomy/TermPublishCard.vue';

const { t } = useI18n();

const path = typeof window !== 'undefined' ? window.location.pathname : '';

const createMatch = path.match(/^\/admin\/blog\/taxonomies\/([^/]+)\/terms\/create$/);
const editMatch = path.match(/^\/admin\/blog\/taxonomies\/([^/]+)\/terms\/(\d+)\/edit$/);

const taxonomySlugFromPath = createMatch ? createMatch[1] : editMatch ? editMatch[1] : '';
const termIdFromPath = editMatch ? Number(editMatch[2]) : null;

const isEdit = termIdFromPath != null;

const taxonomy = ref(null);
const pageLoading = ref(true);
const pageError = ref('');
const saving = ref(false);
const saveError = ref('');
const statusEditOpen = ref(false);
const termUpdatedAt = ref('');

const form = reactive({
    name: '',
    slug: '',
    status: 'published',
    parent_id: null,
    description: '',
});

const formParentSelect = computed({
    get() {
        return form.parent_id == null ? '' : String(form.parent_id);
    },
    set(v) {
        form.parent_id = v === '' || v == null ? null : Number(v);
    },
});

const terms = computed(() => taxonomy.value?.terms || []);

const parentOptions = computed(() => {
    const all = terms.value;
    const excludeId = isEdit ? termIdFromPath : null;
    return all
        .filter((x) => x.id !== excludeId)
        .slice()
        .sort((a, b) => (a.name || '').localeCompare(b.name || ''));
});

const termsListHref = computed(() =>
    taxonomySlugFromPath ? `/admin/blog/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}` : '/admin/settings/taxonomies',
);

const statusDisplayText = computed(() =>
    form.status === 'draft'
        ? t('admin.taxonomies.term_status_draft')
        : t('admin.taxonomies.term_status_published'),
);

const formattedTermUpdatedAt = computed(() => {
    if (!termUpdatedAt.value) return '';
    try {
        const d = new Date(termUpdatedAt.value);
        return d.toLocaleString(localeForDate(), {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return termUpdatedAt.value;
    }
});

function localeForDate() {
    const loc = typeof window !== 'undefined' ? window.__locale : 'en';
    if (loc === 'ru') return 'ru-RU';
    if (loc === 'am') return 'hy-AM';
    return 'en-US';
}

function onStatusChange(v) {
    form.status = v === 'draft' ? 'draft' : 'published';
    statusEditOpen.value = false;
}

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

async function loadTaxonomy() {
    if (!taxonomySlugFromPath) {
        pageError.value = t('admin.taxonomies.detail_not_found');
        pageLoading.value = false;
        return;
    }
    setCsrf();
    pageError.value = '';
    try {
        const { data } = await axios.get(
            `/admin/blog/api/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}`,
        );
        taxonomy.value = data;
    } catch (e) {
        if (e.response?.status === 404) {
            pageError.value = t('admin.taxonomies.detail_not_found');
        } else {
            pageError.value = e.response?.data?.message || t('admin.taxonomies.detail_load_error');
        }
    }
}

async function loadTerm() {
    if (!isEdit || !termIdFromPath || !taxonomySlugFromPath) return;
    setCsrf();
    try {
        const { data } = await axios.get(
            `/admin/blog/api/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}/terms/${termIdFromPath}`,
        );
        form.name = data.name ?? '';
        form.slug = data.slug ?? '';
        form.status = data.status === 'draft' ? 'draft' : 'published';
        form.parent_id = data.parent_id ?? null;
        form.description = data.description ?? '';
        termUpdatedAt.value = data.updated_at ?? '';
    } catch (e) {
        if (e.response?.status === 404) {
            pageError.value = t('admin.taxonomies.term_not_found');
        } else {
            pageError.value = e.response?.data?.message || t('admin.taxonomies.detail_load_error');
        }
    }
}

async function save() {
    saveError.value = '';
    if (!form.name?.trim()) {
        saveError.value = t('admin.taxonomies.form_name');
        return;
    }
    if (!taxonomySlugFromPath) return;

    setCsrf();
    saving.value = true;
    try {
        const payload = {
            name: form.name.trim(),
            slug: form.slug?.trim() || null,
            status: form.status === 'draft' ? 'draft' : 'published',
            parent_id: form.parent_id || null,
            description: form.description?.trim() || null,
        };
        if (isEdit && termIdFromPath) {
            await axios.put(
                `/admin/blog/api/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}/terms/${termIdFromPath}`,
                payload,
            );
        } else {
            await axios.post(
                `/admin/blog/api/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}/terms`,
                payload,
            );
        }
        window.location.href = termsListHref.value;
    } catch (e) {
        const msg = e.response?.data?.message;
        const errors = e.response?.data?.errors;
        saveError.value =
            (errors && Object.values(errors).flat().join(' ')) || msg || t('admin.taxonomies.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    pageLoading.value = true;
    pageError.value = '';
    await loadTaxonomy();
    if (!pageError.value && taxonomy.value) {
        if (isEdit) {
            await loadTerm();
        }
    }
    pageLoading.value = false;
});
</script>
