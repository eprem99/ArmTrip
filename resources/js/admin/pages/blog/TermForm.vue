<!--
  Admin: create / edit term for a taxonomy.
  Routes: /admin/blog/taxonomies/{slug}/terms/create
          /admin/blog/taxonomies/{slug}/terms/{id}/edit
-->
<template>
    <div class="rounded-lg bg-[#f0f0f1] p-4">
        <div class="mb-4">
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <a
                    href="/admin/blog/posts"
                    class="text-[#2271b1] hover:underline"
                >{{ t('admin.taxonomies.back_blog') }}</a>
                <a
                    href="/admin/settings/taxonomies"
                    class="text-[#2271b1] hover:underline"
                >{{ t('admin.taxonomies.back_taxonomies') }}</a>
            </div>
            <a
                :href="termsListHref"
                class="mt-2 inline-block text-sm text-[#2271b1] hover:underline"
            >
                ← {{ t('admin.taxonomies.term_form_back') }}
            </a>
            <h1 class="mt-2 text-lg font-semibold text-slate-800">
                <template v-if="taxonomy">
                    {{
                        (isEdit
                            ? t('admin.taxonomies.edit_term_in_taxonomy')
                            : t('admin.taxonomies.new_term_in_taxonomy')
                        ).replace(':name', taxonomy.name)
                    }}
                </template>
                <template v-else>{{ t('admin.taxonomies.loading') }}</template>
            </h1>
        </div>

        <div v-if="pageLoading" class="rounded-lg border border-[#c3c4c7] bg-white p-12 text-center text-sm text-slate-600 shadow-sm">
            {{ t('admin.taxonomies.loading') }}
        </div>
        <div v-else-if="pageError" class="rounded-lg border border-[#c3c4c7] bg-white p-12 text-center text-sm text-red-600 shadow-sm">
            {{ pageError }}
        </div>
        <form
            v-else-if="taxonomy"
            class="flex flex-row gap-4 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start lg:gap-6"
            @submit.prevent="save"
        >
            <!-- Main (same flow as BlogPostForm: title → meta → editor → SEO) -->
            <div class="flex min-w-0 flex-col gap-4 lg:order-1">
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
                    :title-placeholder="t('admin.taxonomies.term_form_title_placeholder')"
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

                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.taxonomies.form_parent') }}</h3>
                    </div>
                    <div class="p-4">
                        <select
                            v-model="formParentSelect"
                            class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
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
                </div>

                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.taxonomies.form_hero_title') }}</h3>
                    </div>
                    <div class="p-4">
                        <input
                            id="term_hero_title"
                            v-model="form.hero_title"
                            type="text"
                            class="block w-full rounded border border-[#8c8f94] px-3 py-2 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                            :placeholder="t('admin.taxonomies.form_hero_title_placeholder')"
                        />
                        <p class="mt-1 text-xs text-slate-500">{{ t('admin.taxonomies.form_hero_title_hint') }}</p>
                    </div>
                </div>

                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.taxonomies.form_short_description') }}</h3>
                    </div>
                    <div class="p-4">
                        <textarea
                            id="term_short_description"
                            v-model="form.short_description"
                            rows="3"
                            class="block w-full rounded border border-[#8c8f94] px-3 py-2 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                            :placeholder="t('admin.taxonomies.form_short_description_placeholder')"
                        />
                    </div>
                </div>

                <ContentAddMediaButton @click="openMediaPickerForEditor">
                    {{ t('admin.content.add_media') }}
                </ContentAddMediaButton>

                <div class="overflow-hidden rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-2 text-sm font-medium text-slate-700">
                        {{ t('admin.taxonomies.form_description') }}
                    </div>
                    <Editor
                        v-model="form.description"
                        :init="tinymceInit"
                        :tinymce-script-src="tinymceScriptSrc"
                        @init="handleEditorInit"
                    />
                    <div class="flex items-center justify-between border-t border-[#c3c4c7] bg-[#f6f7f7] px-4 py-2 text-xs text-slate-500">
                        <span>{{ t('admin.content.word_count') }}: {{ descriptionWordCount }}</span>
                    </div>
                </div>

                <SeoBlock
                    v-model:seoTitle="seoTitle"
                    v-model:seoDescription="seoDescription"
                    :title-label="t('admin.taxonomies.seo_title')"
                    :meta-title-label="t('admin.taxonomies.seo_meta_title')"
                    :meta-description-label="t('admin.taxonomies.seo_meta_description')"
                />
            </div>

            <aside class="flex w-auto flex-col gap-4 lg:order-2 lg:sticky lg:top-20">
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
                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.content.form_language') }}</h3>
                    </div>
                    <div class="space-y-3 p-4">
                        <div v-if="!isEdit">
                            <label for="term_language_id" class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.content.form_language') }}</label>
                            <select
                                id="term_language_id"
                                v-model.number="form.language_id"
                                :disabled="languageLocked"
                                class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1] disabled:bg-slate-100"
                            >
                                <option v-for="lang in activeLanguages" :key="lang.id" :value="lang.id">
                                    {{ lang.native_name || lang.name }} ({{ lang.lcode }})
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">{{ t('admin.content.form_language_hint') }}</p>
                        </div>
                        <div v-else-if="termLanguageLabel">
                            <p class="text-sm text-slate-600">
                                <span class="font-medium text-slate-800">{{ t('admin.content.form_language') }}:</span>
                                {{ termLanguageLabel }}
                            </p>
                        </div>
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
                    @pick="openMediaPickerForHero"
                    @remove="form.image = ''"
                />
            </aside>
        </form>

        <AdminMediaPickerModal
            v-if="taxonomy"
            v-model:open="mediaPickerOpen"
            :title="mediaPickerMode === 'editor' ? t('admin.content.insert_media') : t('admin.content.select_media')"
            :allow-url-insert="mediaPickerMode === 'editor'"
            @select="onMediaPickerSelect"
            @insert-url="onMediaPickerInsertUrl"
        />
    </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Editor from '@tinymce/tinymce-vue';
import { useI18n } from '../../composables/useI18n';
import ContentAddMediaButton from '../../components/ContentAddMediaButton.vue';
import ContentTitlePermalinkCard from '../../components/content/ContentTitlePermalinkCard.vue';
import ContentFeaturedImageCard from '../../components/content/ContentFeaturedImageCard.vue';
import AdminMediaPickerModal from '../../components/media/AdminMediaPickerModal.vue';
import SeoBlock from '../../components/SeoBlock.vue';
import TermPublishCard from '../../components/taxonomy/TermPublishCard.vue';

const { t } = useI18n();

const tinymceScriptSrc = typeof window !== 'undefined' ? window.location.origin + '/tinymce/tinymce.min.js' : '';

const tinymceInit = {
    base_url: '/tinymce',
    suffix: '.min',
    height: 420,
    menubar: false,
    plugins: ['lists', 'link', 'image', 'code', 'table'],
    toolbar:
        'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote | link armtripmedia | code',
    content_style: 'body { font-family: inherit; font-size: 16px; line-height: 1.6; }',
    placeholder: '…',
    file_picker_types: 'image',
    file_picker_callback: (callback) => {
        tinymceFilePicker.value = callback;
        openMediaPickerForEditor();
    },
    setup: (editor) => {
        editor.ui.registry.addButton('armtripmedia', {
            icon: 'image',
            tooltip: 'Add media',
            onAction: () => openMediaPickerForEditor(),
        });
    },
};

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

const errors = reactive({ name: '', slug: '' });

const form = reactive({
    name: '',
    hero_title: '',
    slug: '',
    status: 'published',
    parent_id: null,
    short_description: '',
    description: '',
    image: '',
    language_id: null,
});

const activeLanguages = ref([]);
const translationGroupFromQuery = ref('');
const languageLocked = ref(false);
const termLanguageLabel = ref('');

const listLang = computed(() => {
    if (typeof window === 'undefined') return 'en';
    return window.__locale || 'en';
});

const seoTitle = ref('');
const seoDescription = ref('');

const heroImageError = ref(false);
const showHeroImageUrl = ref(false);
const mediaPickerOpen = ref(false);
const mediaPickerMode = ref('hero'); // hero | editor
const editorInstance = ref(null);
const tinymceFilePicker = ref(null);

const slugLocked = ref(true);
const slugChecking = ref(false);
const slugAvailable = ref(null);
const slugSuggest = ref('');
let slugCheckTimer = null;

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

const permalinkBase = computed(() => {
    if (typeof window === 'undefined' || !taxonomySlugFromPath) return '';
    let base = `${window.location.origin}/${taxonomySlugFromPath}/`;
    if (form.parent_id) {
        const parent = parentOptions.value.find((p) => p.id === form.parent_id);
        if (parent?.slug) {
            base += `${parent.slug}/`;
        }
    }

    return base;
});

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

const descriptionWordCount = computed(() => {
    const text = (form.description || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    return text ? text.split(/\s/).length : 0;
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
    if (!slug || !taxonomySlugFromPath) {
        slugAvailable.value = null;
        slugSuggest.value = '';
        return;
    }
    slugChecking.value = true;
    try {
        const res = await axios.get(
            `/admin/blog/api/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}/terms/check-slug`,
            {
                params: {
                    slug,
                    ignore_id: isEdit && termIdFromPath ? termIdFromPath : null,
                },
                headers: { Accept: 'application/json' },
            },
        );
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

function handleEditorInit(_evt, editor) {
    editorInstance.value = editor;
}

function openMediaPickerForHero() {
    mediaPickerMode.value = 'hero';
    mediaPickerOpen.value = true;
}

function openMediaPickerForEditor() {
    mediaPickerMode.value = 'editor';
    mediaPickerOpen.value = true;
}

function insertImageIntoEditor(url, alt) {
    if (typeof tinymceFilePicker.value === 'function') {
        const cb = tinymceFilePicker.value;
        tinymceFilePicker.value = null;
        cb(url, { alt });
    } else if (editorInstance.value) {
        editorInstance.value.insertContent(`<img src="${url}" alt="${String(alt).replace(/"/g, '&quot;')}" />`);
    }
}

function onMediaPickerSelect(item) {
    if (!item?.url) return;

    if (mediaPickerMode.value === 'hero') {
        form.image = item.url;
        heroImageError.value = false;
        return;
    }

    const url = item.url;
    const alt = item.alt || item.title || item.filename || '';
    insertImageIntoEditor(url, alt);
}

function onMediaPickerInsertUrl({ url, alt }) {
    const u = (url || '').trim();
    const a = (alt || '').trim();
    if (!u) return;
    insertImageIntoEditor(u, a);
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
    if (!taxonomySlugFromPath) {
        pageError.value = t('admin.taxonomies.detail_not_found');
        pageLoading.value = false;
        return;
    }
    setCsrf();
    pageError.value = '';
    try {
        const { data } = await axios.get(`/admin/blog/api/taxonomies/${encodeURIComponent(taxonomySlugFromPath)}`, {
            params: { lang: listLang.value },
        });
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
        form.hero_title = data.hero_title ?? '';
        form.slug = data.slug ?? '';
        form.status = data.status === 'draft' ? 'draft' : 'published';
        form.parent_id = data.parent_id ?? null;
        form.short_description = data.short_description ?? '';
        form.description = data.description ?? '';
        form.image = data.image ?? '';
        termUpdatedAt.value = data.updated_at ?? '';
        seoTitle.value = data.seo_title ?? '';
        seoDescription.value = data.seo_description ?? '';
        const lang = data.language;
        termLanguageLabel.value = lang
            ? `${lang.native_name || lang.name} (${lang.lcode})`
            : '';
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
    errors.name = '';
    errors.slug = '';
    if (!form.name?.trim()) {
        saveError.value = t('admin.taxonomies.form_name');
        errors.name = saveError.value;
        return;
    }
    if (!taxonomySlugFromPath) return;

    setCsrf();
    saving.value = true;
    try {
        const payload = {
            name: form.name.trim(),
            hero_title: form.hero_title?.trim() || null,
            slug: form.slug?.trim() || null,
            status: form.status === 'draft' ? 'draft' : 'published',
            parent_id: form.parent_id || null,
            short_description: form.short_description?.trim() || null,
            description: form.description || null,
            image: form.image?.trim() || null,
            seo_title: (seoTitle.value || '').trim(),
            seo_description: (seoDescription.value || '').trim(),
        };
        if (!isEdit) {
            payload.language_id = form.language_id;
            if (translationGroupFromQuery.value) {
                payload.translation_group_id = translationGroupFromQuery.value;
            }
        }
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
    pageLoading.value = true;
    pageError.value = '';
    await loadLanguages();
    await loadTaxonomy();
    if (!pageError.value && taxonomy.value) {
        if (isEdit) {
            await loadTerm();
        } else {
            applyCreateLanguageFromQuery();
        }
    }
    pageLoading.value = false;
});
</script>
