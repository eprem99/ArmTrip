<template>
    <div class="space-y-6">
        <div>
            <a
                href="/admin/settings/taxonomies"
                class="text-sm font-medium text-blue-600 hover:text-blue-800"
            >
                ← {{ t('admin.taxonomies.form_back') }}
            </a>
            <h1 class="mt-2 text-xl font-semibold text-slate-900">
                {{ isEdit ? t('admin.taxonomies.taxonomy_form_edit_title') : t('admin.taxonomies.taxonomy_form_create_title') }}
            </h1>
        </div>

        <div v-if="pageLoading" class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
            {{ t('admin.taxonomies.loading') }}
        </div>
        <div v-else-if="pageError" class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-red-600">
            {{ pageError }}
        </div>
        <template v-else>
            <div class="grid gap-6 lg:grid-cols-3">
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
                                <label class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.taxonomies.form_type') }}</label>
                                <select
                                    v-model="form.type"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                >
                                    <option value="category">{{ t('admin.taxonomies.type_category') }}</option>
                                    <option value="tag">{{ t('admin.taxonomies.type_tag') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.taxonomies.form_description') }}</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <TaxonomyIconSelect
                        v-model="form.icon"
                        :label="t('admin.taxonomies.form_icon_label')"
                        :hint="t('admin.taxonomies.form_icon_hint')"
                        :empty-label="t('admin.taxonomies.form_icon_none')"
                        :suggested-icon-key="suggestedIconForSlug"
                    />
                    <ContentFeaturedImageCard
                        v-model="form.image"
                        :title="t('admin.taxonomies.form_hero_image')"
                        :alt="form.name"
                        v-model:imageError="heroImageError"
                        v-model:showUrl="showHeroImageUrl"
                        :preview-label="t('admin.content.featured_image_preview')"
                        :image-load-error-label="t('admin.content.featured_image_load_error')"
                        :remove-label="t('admin.content.remove_image')"
                        :set-label="t('admin.content.set_featured_image')"
                        :set-url-label="t('admin.content.set_featured_image_url')"
                        @pick="openMediaPicker"
                        @remove="form.image = ''"
                    />
                </div>
            </div>

            <SeoBlock
                v-model:seoTitle="seoTitle"
                v-model:seoDescription="seoDescription"
                :title-label="t('admin.taxonomies.seo_title')"
                :meta-title-label="t('admin.taxonomies.seo_meta_title')"
                :meta-description-label="t('admin.taxonomies.seo_meta_description')"
            />

            <p v-if="saveError" class="text-sm text-red-600">{{ saveError }}</p>

            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                    @click="save"
                >
                    {{ saving ? t('admin.taxonomies.form_saving') : t('admin.taxonomies.form_save') }}
                </button>
                <a
                    href="/admin/settings/taxonomies"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    {{ t('admin.taxonomies.form_cancel') }}
                </a>
            </div>
        </template>

        <AdminMediaPickerModal
            v-model:open="mediaPickerOpen"
            :title="t('admin.content.select_media')"
            @select="onHeroImageSelected"
        />
    </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import AdminMediaPickerModal from '../../components/media/AdminMediaPickerModal.vue';
import ContentFeaturedImageCard from '../../components/content/ContentFeaturedImageCard.vue';
import SeoBlock from '../../components/SeoBlock.vue';
import TaxonomyIconSelect from '../../components/taxonomy/TaxonomyIconSelect.vue';
import { taxonomySuggestedIconBySlug } from '../../data/taxonomyIcons';

const { t } = useI18n();

const suggestedIconForSlug = computed(() => {
    const s = (form.slug || '').trim().toLowerCase();
    return s && taxonomySuggestedIconBySlug[s] ? taxonomySuggestedIconBySlug[s] : null;
});

const path = typeof window !== 'undefined' ? window.location.pathname : '';

const isEdit = /^\/admin\/settings\/taxonomies\/.+\/edit$/.test(path);

const editSlugMatch = path.match(/^\/admin\/settings\/taxonomies\/([^/]+)\/edit$/);
const editSlug = editSlugMatch ? editSlugMatch[1] : null;

const originalSlug = ref('');

const pageLoading = ref(isEdit);
const pageError = ref('');
const saving = ref(false);
const saveError = ref('');

const form = reactive({
    name: '',
    slug: '',
    type: 'category',
    description: '',
    icon: null,
    image: '',
});

const seoTitle = ref('');
const seoDescription = ref('');

const heroImageError = ref(false);
const showHeroImageUrl = ref(false);

const mediaPickerOpen = ref(false);

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function openMediaPicker() {
    mediaPickerOpen.value = true;
}

function onHeroImageSelected(item) {
    if (!item?.url) return;
    form.image = item.url;
    heroImageError.value = false;
}

async function loadTaxonomy() {
    if (!isEdit || !editSlug) {
        pageLoading.value = false;
        return;
    }
    setCsrf();
    pageError.value = '';
    try {
        const { data } = await axios.get(`/admin/blog/api/taxonomies/${encodeURIComponent(editSlug)}`);
        originalSlug.value = data.slug || editSlug;
        form.name = data.name ?? '';
        form.slug = data.slug ?? '';
        form.type = data.type === 'tag' ? 'tag' : 'category';
        form.description = data.description ?? '';
        form.icon = data.icon || null;
        form.image = data.image ?? '';
        seoTitle.value = data.seo_title ?? '';
        seoDescription.value = data.seo_description ?? '';
    } catch (e) {
        pageError.value = e.response?.data?.message || t('admin.taxonomies.detail_not_found');
    } finally {
        pageLoading.value = false;
    }
}

async function save() {
    saveError.value = '';
    if (!form.name?.trim()) {
        saveError.value = t('admin.taxonomies.form_name');
        return;
    }

    setCsrf();
    saving.value = true;
    try {
        const payload = {
            name: form.name.trim(),
            slug: form.slug?.trim() || null,
            type: form.type,
            description: form.description?.trim() || null,
            icon: form.icon || null,
            image: form.image?.trim() || null,
            seo_title: seoTitle.value?.trim() ?? '',
            seo_description: seoDescription.value?.trim() ?? '',
        };

        if (isEdit && originalSlug.value) {
            await axios.put(`/admin/blog/api/taxonomies/${encodeURIComponent(originalSlug.value)}`, payload);
        } else {
            await axios.post('/admin/blog/api/taxonomies', payload);
        }

        window.location.href = '/admin/settings/taxonomies';
        window.location.reload();
    } catch (e) {
        const msg = e.response?.data?.message;
        const errors = e.response?.data?.errors;
        saveError.value =
            (errors && Object.values(errors).flat().join(' ')) || msg || t('admin.taxonomies.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    loadTaxonomy();
});
</script>
