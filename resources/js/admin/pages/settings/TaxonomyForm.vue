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
                    <ContentTitlePermalinkCard
                        :title="form.name"
                        :slug="form.slug"
                        :is-home="false"
                        :permalink-base="permalinkBase"
                        :slug-locked="slugLocked"
                        :slug-checking="slugChecking"
                        :slug-available="slugAvailable"
                        :title-error="errors.name"
                        :slug-error="errors.slug"
                        :title-placeholder="t('admin.taxonomies.taxonomy_form_title_placeholder')"
                        :permalink-label="t('admin.content.permalink')"
                        :slug-sample="t('admin.content.slug_sample')"
                        :slug-edit-label="t('admin.content.slug_edit')"
                        :slug-checking-label="t('admin.content.slug_checking')"
                        :slug-available-label="t('admin.content.slug_available')"
                        :slug-taken-label="t('admin.content.slug_taken')"
                        @update:title="(v) => (form.name = v)"
                        @update:slug="(v) => (form.slug = v)"
                        @unlock-slug="unlockSlug"
                        @lock-slug="lockSlug"
                    />

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                        <div class="space-y-4">
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
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                        <h3 class="mb-3 text-sm font-semibold text-slate-800">{{ t('admin.content.form_language') }}</h3>
                        <div v-if="!isEdit">
                            <label for="taxonomy_language_id" class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.content.form_language') }}</label>
                            <select
                                id="taxonomy_language_id"
                                v-model.number="form.language_id"
                                :disabled="languageLocked"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-slate-100"
                            >
                                <option v-for="lang in activeLanguages" :key="lang.id" :value="lang.id">
                                    {{ lang.native_name || lang.name }} ({{ lang.lcode }})
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">{{ t('admin.content.form_language_hint') }}</p>
                        </div>
                        <div v-else-if="taxonomyLanguageLabel">
                            <p class="text-sm text-slate-600">
                                <span class="font-medium text-slate-800">{{ t('admin.content.form_language') }}:</span>
                                {{ taxonomyLanguageLabel }}
                            </p>
                        </div>
                    </div>
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
import { reactive, ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import AdminMediaPickerModal from '../../components/media/AdminMediaPickerModal.vue';
import ContentFeaturedImageCard from '../../components/content/ContentFeaturedImageCard.vue';
import ContentTitlePermalinkCard from '../../components/content/ContentTitlePermalinkCard.vue';
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
const taxonomyId = ref(null);

const pageLoading = ref(true);
const pageError = ref('');
const saving = ref(false);
const saveError = ref('');

const errors = reactive({ name: '', slug: '' });

const form = reactive({
    name: '',
    slug: '',
    type: 'category',
    description: '',
    icon: null,
    image: '',
    language_id: null,
});

const activeLanguages = ref([]);
const translationGroupFromQuery = ref('');
const languageLocked = ref(false);
const taxonomyLanguageLabel = ref('');

const listLang = computed(() => {
    if (typeof window === 'undefined') return 'en';
    return window.__locale || 'en';
});

const seoTitle = ref('');
const seoDescription = ref('');

const heroImageError = ref(false);
const showHeroImageUrl = ref(false);

const mediaPickerOpen = ref(false);

const slugLocked = ref(true);
const slugChecking = ref(false);
const slugAvailable = ref(null);
const slugSuggest = ref('');
let slugCheckTimer = null;

const permalinkBase = computed(() => {
    if (typeof window === 'undefined') return '';
    return `${window.location.origin}/blog/`;
});

function slugify(input) {
    const str = (input || '').toString().trim().toLowerCase();
    return str
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .replace(/-{2,}/g, '-');
}

function unlockSlug() {
    slugLocked.value = false;
}

function lockSlug() {
    slugLocked.value = true;
}

async function checkSlugUnique(slug) {
    if (!slug) {
        slugAvailable.value = null;
        slugSuggest.value = '';
        return;
    }
    slugChecking.value = true;
    try {
        const res = await axios.get('/admin/blog/api/taxonomies/check-slug', {
            params: {
                slug,
                ignore_id: isEdit && taxonomyId.value ? taxonomyId.value : null,
            },
            headers: { Accept: 'application/json' },
        });
        slugAvailable.value = !!res.data?.available;
        slugSuggest.value = res.data?.suggested || '';
    } catch (_) {
        slugAvailable.value = null;
        slugSuggest.value = '';
    } finally {
        slugChecking.value = false;
    }
}

watch(
    () => form.name,
    (name) => {
        if (!slugLocked.value) return;
        const next = slugify(name);
        if (!next) return;
        form.slug = next;
    },
);

watch(
    () => form.slug,
    (slug) => {
        if (slugCheckTimer) clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(async () => {
            await checkSlugUnique((slug || '').trim());
            if (slugLocked.value && slugAvailable.value === false && slugSuggest.value) {
                form.slug = slugSuggest.value;
            }
        }, 350);
    },
);

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

async function loadLanguages() {
    setCsrf();
    try {
        const { data } = await axios.get('/admin/settings/api/languages');
        activeLanguages.value = (data || []).filter((l) => l.status === 'active');
    } catch (_) {
        activeLanguages.value = [];
    }
}

function mapLocaleToLcode(loc) {
    if (loc === 'ru') return 'ru';
    if (loc === 'am') return 'am';
    return 'en';
}

function applyCreateLanguageFromQuery() {
    if (isEdit || !activeLanguages.value.length) return;
    const qp = new URLSearchParams(typeof window !== 'undefined' ? window.location.search : '');
    translationGroupFromQuery.value = qp.get('translation_group') || '';
    const wanted = qp.get('lang');
    const byQuery = wanted ? activeLanguages.value.find((l) => l.lcode === wanted) : null;
    const byLocale = activeLanguages.value.find(
        (l) => l.lcode === mapLocaleToLcode(typeof window !== 'undefined' ? window.__locale : 'en'),
    );
    const pick = byQuery || byLocale || activeLanguages.value[0];
    if (pick) {
        form.language_id = pick.id;
    }
    languageLocked.value = !!(translationGroupFromQuery.value && wanted);
}

async function loadTaxonomy() {
    if (!isEdit || !editSlug) {
        pageLoading.value = false;
        return;
    }
    setCsrf();
    pageError.value = '';
    try {
        const { data } = await axios.get(`/admin/blog/api/taxonomies/${encodeURIComponent(editSlug)}`, {
            params: { lang: listLang.value },
        });
        originalSlug.value = data.slug || editSlug;
        taxonomyId.value = data.id ?? null;
        form.name = data.name ?? '';
        form.slug = data.slug ?? '';
        form.type = data.type === 'tag' ? 'tag' : 'category';
        form.description = data.description ?? '';
        form.icon = data.icon || null;
        form.image = data.image ?? '';
        seoTitle.value = data.seo_title ?? '';
        seoDescription.value = data.seo_description ?? '';
        const lang = data.language;
        taxonomyLanguageLabel.value = lang
            ? `${lang.native_name || lang.name} (${lang.lcode})`
            : '';
    } catch (e) {
        pageError.value = e.response?.data?.message || t('admin.taxonomies.detail_not_found');
    } finally {
        pageLoading.value = false;
    }
}

async function save() {
    saveError.value = '';
    errors.name = '';
    errors.slug = '';
    if (!form.name?.trim()) {
        saveError.value = t('admin.taxonomies.form_name');
        errors.name = saveError.value;
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

        if (!isEdit) {
            payload.language_id = form.language_id;
            if (translationGroupFromQuery.value) {
                payload.translation_group_id = translationGroupFromQuery.value;
            }
        }

        if (isEdit && originalSlug.value) {
            await axios.put(`/admin/blog/api/taxonomies/${encodeURIComponent(originalSlug.value)}`, payload);
        } else {
            await axios.post('/admin/blog/api/taxonomies', payload);
        }

        window.location.href = '/admin/settings/taxonomies';
        window.location.reload();
    } catch (e) {
        const msg = e.response?.data?.message;
        const errBag = e.response?.data?.errors;
        if (errBag) {
            errors.name = (errBag.name && errBag.name[0]) || '';
            errors.slug = (errBag.slug && errBag.slug[0]) || '';
        }
        saveError.value =
            (errBag && Object.values(errBag).flat().join(' ')) || msg || t('admin.taxonomies.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    await loadLanguages();
    if (isEdit && editSlug) {
        await loadTaxonomy();
    } else {
        applyCreateLanguageFromQuery();
        pageLoading.value = false;
    }
});
</script>
