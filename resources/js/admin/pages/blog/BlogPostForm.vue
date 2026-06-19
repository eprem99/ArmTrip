<template>
    <div class="rounded-lg bg-[#f0f0f1] p-4">
        <div class="mb-4">
            <a
                href="/admin/blog/posts"
                class="text-sm text-[#2271b1] hover:underline"
            >
                ← {{ t('admin.blog_posts.back') }}
            </a>
        </div>

        <div v-if="loading" class="rounded-lg border border-[#c3c4c7] bg-white p-12 text-center text-sm text-slate-600 shadow-sm">
            {{ t('admin.blog_posts.loading') }}
        </div>

        <form
            v-else
            class="flex flex-row gap-4 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start lg:gap-6"
            @submit.prevent="save"
        >
            <!-- Main content (left) -->
            <div class="flex min-w-0 flex-col gap-4 lg:order-1">
                <ContentTitlePermalinkCard
                    :title="form.title"
                    :slug="form.slug"
                    :is-home="false"
                    :permalink-base="permalinkBase"
                    :slug-locked="slugLocked"
                    :slug-checking="slugChecking"
                    :slug-available="slugAvailable"
                    :title-error="errors.title"
                    :slug-error="errors.slug"
                    :title-placeholder="t('admin.blog_posts.form_title_placeholder')"
                    :permalink-label="t('admin.blog_posts.permalink')"
                    :slug-sample="t('admin.blog_posts.slug_sample')"
                    :slug-edit-label="t('admin.blog_posts.slug_edit')"
                    :slug-checking-label="t('admin.blog_posts.slug_checking')"
                    :slug-available-label="t('admin.blog_posts.slug_available')"
                    :slug-taken-label="t('admin.blog_posts.slug_taken')"
                    @update:title="(v) => (form.title = v)"
                    @update:slug="(v) => (form.slug = v)"
                    @unlock-slug="unlockSlug"
                    @lock-slug="lockSlug"
                />

                <ContentExcerptCard
                    v-model="form.excerpt"
                    :label="t('admin.blog_posts.form_excerpt')"
                    id="excerpt"
                />

                <ContentAddMediaButton @click="openMediaPickerForEditor">
                    {{ t('admin.content.add_media') }}
                </ContentAddMediaButton>

                <!-- Editor (TinyMCE) -->
                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-2 text-sm font-medium text-slate-700">
                        {{ t('admin.blog_posts.form_content') }}
                    </div>
                    <Editor
                        v-model="form.content"
                        :init="tinymceInit"
                        :tinymce-script-src="tinymceScriptSrc"
                        @init="handleEditorInit"
                    />
                    <div class="flex items-center justify-between border-t border-[#c3c4c7] bg-[#f6f7f7] px-4 py-2 text-xs text-slate-500">
                        <span>{{ t('admin.content.word_count') }}: {{ wordCount }}</span>
                    </div>
                </div>

                <!-- SEO -->
                <SeoBlock
                    v-model:seoTitle="seoTitle"
                    v-model:seoDescription="seoDescription"
                    :title-label="t('admin.blog_posts.seo_title')"
                    :meta-title-label="t('admin.blog_posts.seo_meta_title')"
                    :meta-description-label="t('admin.blog_posts.seo_meta_description')"
                />
            </div>

            <!-- Sidebar (right) -->
            <aside class="flex w-auto flex-col gap-4 lg:order-2 lg:sticky lg:top-20">
                <ContentPublishCard
                    :title="t('admin.content.publish_box')"
                    :status="form.status"
                    :status-edit-open="statusEditOpen"
                    :is-edit="isEdit"
                    :saving="saving"
                    :save-error="saveError"
                    :status-text="form.status === 'published' ? t('admin.blog_posts.status_published') : t('admin.blog_posts.status_draft')"
                    :published-on="isEdit && postLoaded?.published_at ? formatDate(postLoaded.published_at) : ''"
                    :preview-label="t('admin.content.preview_changes')"
                    :status-label="t('admin.blog_posts.form_status')"
                    :edit-label="t('admin.content.edit_link')"
                    :draft-label="t('admin.blog_posts.status_draft')"
                    :published-label="t('admin.blog_posts.status_published')"
                    :visibility-label="t('admin.content.visibility_public')"
                    :published-on-label="t('admin.blog_posts.form_published_at')"
                    :trash-label="t('admin.blog_posts.delete')"
                    :submit-text="saving ? t('admin.blog_posts.form_saving') : t('admin.blog_posts.form_save')"
                    @toggle-status-edit="statusEditOpen = !statusEditOpen"
                    @update:status="(v) => { form.status = v; statusEditOpen = false; }"
                />

                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.blog_posts.form_published_at') }}</h3>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2">
                                <input
                                    id="publish_date"
                                    v-model="publishDate"
                                    type="date"
                                    class="block w-1/2 rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                                />
                                <input
                                    id="publish_time"
                                    v-model="publishTime"
                                    type="time"
                                    class="block w-1/2 rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                                    step="60"
                                />
                            </div>
                            <p class="mt-1 text-xs text-slate-500" v-if="form.status !== 'published'">
                                {{ t('admin.blog_posts.publish_date_hint') }}
                            </p>
                        </div>
                    </div>
                </div>

                <ContentFeaturedImageCard
                    v-model="form.featured_image"
                    :title="t('admin.content.featured_image')"
                    :alt="form.title"
                    v-model:imageError="featuredImageError"
                    v-model:showUrl="showFeaturedUrl"
                    :preview-label="t('admin.content.featured_image_preview')"
                    :image-load-error-label="t('admin.content.featured_image_load_error')"
                    :remove-label="t('admin.content.remove_image')"
                    :set-label="t('admin.content.set_featured_image')"
                    :set-url-label="t('admin.content.set_featured_image_url')"
                    @remove="() => { form.featured_image = ''; featuredImageError = false; }"
                    @pick="openMediaPickerForFeatured"
                />

                <!-- Language (new post / translation) -->
                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.content.form_language') }}</h3>
                    </div>
                    <div class="space-y-3 p-4">
                        <div v-if="!isEdit">
                            <label for="post_language_id" class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.content.form_language') }}</label>
                            <select
                                id="post_language_id"
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
                        <div v-else-if="postLoaded?.language">
                            <p class="text-sm text-slate-600">
                                <span class="font-medium text-slate-800">{{ t('admin.content.form_language') }}:</span>
                                {{ postLoaded.language.native_name || postLoaded.language.name }}
                                ({{ postLoaded.language.lcode }})
                            </p>
                        </div>
                    </div>
                </div>

                <PostTaxonomyPicker
                    v-model="selectedTermIds"
                    :taxonomies="taxonomyOptions"
                    :loading="taxonomiesLoading"
                    :loading-label="t('admin.blog_posts.taxonomies_loading')"
                    :empty-label="t('admin.blog_posts.taxonomies_empty')"
                />

            </aside>
        </form>

        <AdminMediaPickerModal
            v-model:open="mediaPickerOpen"
            :title="mediaPickerMode === 'editor' ? t('admin.content.insert_media') : t('admin.content.select_media')"
            :allow-url-insert="mediaPickerMode === 'editor'"
            @select="onMediaPickerSelect"
            @insert-url="onMediaPickerInsertUrl"
        />
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Editor from '@tinymce/tinymce-vue';
import { useI18n } from '../../composables/useI18n';
import ContentAddMediaButton from '../../components/ContentAddMediaButton.vue';
import AdminMediaPickerModal from '../../components/media/AdminMediaPickerModal.vue';
import SeoBlock from '../../components/SeoBlock.vue';
import ContentTitlePermalinkCard from '../../components/content/ContentTitlePermalinkCard.vue';
import ContentExcerptCard from '../../components/content/ContentExcerptCard.vue';
import ContentPublishCard from '../../components/content/ContentPublishCard.vue';
import ContentFeaturedImageCard from '../../components/content/ContentFeaturedImageCard.vue';
import PostTaxonomyPicker from '../../components/blog/PostTaxonomyPicker.vue';

const { t, locale } = useI18n();

const tinymceScriptSrc = typeof window !== 'undefined' ? window.location.origin + '/tinymce/tinymce.min.js' : '';

const tinymceInit = {
    base_url: '/tinymce',
    suffix: '.min',
    height: 420,
    menubar: false,
    plugins: ['lists', 'link', 'image', 'code', 'table'],
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote | link armtripmedia | code',
    content_style: 'body { font-family: inherit; font-size: 16px; line-height: 1.6; }',
    placeholder: 'Start writing or paste HTML…',
    file_picker_types: 'image',
    file_picker_callback: (callback, _value, _meta) => {
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

const editId = (() => {
    if (typeof window === 'undefined') return null;
    const m = window.location.pathname.match(/^\/admin\/blog\/posts\/(\d+)\/edit$/);
    return m ? m[1] : null;
})();
const isEdit = !!editId;

const loading = ref(!!editId);
const saving = ref(false);
const saveError = ref('');
const errors = reactive({ title: '', slug: '' });

const form = reactive({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image: '',
    status: 'draft',
    published_at: '',
    language_id: null,
});

const activeLanguages = ref([]);
const translationGroupFromQuery = ref('');
const languageLocked = ref(false);

const featuredImageError = ref(false);
const showFeaturedUrl = ref(false);
const mediaPickerOpen = ref(false);
const mediaPickerMode = ref('featured'); // featured | editor
const statusEditOpen = ref(false);
const postLoaded = ref(null);
const editorInstance = ref(null);
const tinymceFilePicker = ref(null); // (url, meta) => void

const seoTitle = ref('');
const seoDescription = ref('');

const taxonomyOptions = ref([]);
const taxonomiesLoading = ref(false);
const selectedTermIds = ref([]);

const permalinkBase = computed(() => {
    if (typeof window === 'undefined') return '';
    return window.location.origin + '/blog/';
});

const wordCount = computed(() => {
    const text = (form.content || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    return text ? text.split(/\s/).length : 0;
});

const slugLocked = ref(true);
const slugChecking = ref(false);
const slugAvailable = ref(null); // null | boolean
const slugSuggest = ref('');
let slugCheckTimer = null;
const slugTitleSyncReady = ref(!isEdit);
let titleSlugTimer = null;

const publishDate = ref('');
const publishTime = ref('');

async function applySuggestedPostSlug() {
    if (!slugLocked.value || !slugTitleSyncReady.value) return;
    setCsrf();
    try {
        const { data } = await axios.get('/admin/blog/api/posts/suggest-slug', {
            params: {
                title: form.title,
                ignore_id: editId ? Number(editId) : undefined,
            },
            headers: { Accept: 'application/json' },
        });
        if (data?.slug) {
            form.slug = data.slug;
        }
    } catch (_) {
        /* ignore */
    }
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
        const res = await axios.get('/admin/blog/api/posts/check-slug', {
            params: {
                slug,
                ignore_id: editId ? Number(editId) : null,
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
    () => form.title,
    () => {
        if (!slugLocked.value) return;
        if (titleSlugTimer) clearTimeout(titleSlugTimer);
        titleSlugTimer = setTimeout(() => {
            applySuggestedPostSlug();
        }, 400);
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
    if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

function handleEditorInit(_evt, editor) {
    editorInstance.value = editor;
}

function toDatetimeLocal(value) {
    if (!value) return '';
    try {
        const d = new Date(value);
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    } catch {
        return '';
    }
}

watch(
    () => form.status,
    (s) => {
        if (s !== 'published') {
            form.published_at = '';
            publishDate.value = '';
            publishTime.value = '';
        }
    },
);

function openMediaPickerForFeatured() {
    mediaPickerMode.value = 'featured';
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

    if (mediaPickerMode.value === 'featured') {
        form.featured_image = item.url;
        featuredImageError.value = false;
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

function formatDate(value) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        return d.toLocaleDateString(locale.value === 'ru' ? 'ru-RU' : 'en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return value;
    }
}

async function loadPost() {
    if (!editId) return;
    setCsrf();
    try {
        const { data } = await axios.get(`/admin/blog/api/posts/${editId}`);
        postLoaded.value = data;
        form.title = data.title ?? '';
        form.slug = data.slug ?? '';
        form.excerpt = data.excerpt ?? '';
        form.content = data.content ?? '';
        form.featured_image = data.featured_image ?? '';
        form.status = data.status ?? 'draft';
        if (data.published_at) {
            const dt = new Date(data.published_at);
            if (!Number.isNaN(dt.getTime())) {
                const pad = (n) => String(n).padStart(2, '0');
                publishDate.value = `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}`;
                publishTime.value = `${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
                form.published_at = `${publishDate.value} ${publishTime.value}:00`;
            }
        }
        selectedTermIds.value = Array.isArray(data.term_ids) ? [...data.term_ids] : [];
    } catch (e) {
        saveError.value = e.response?.data?.message || t('admin.blog_posts.save_error');
    } finally {
        loading.value = false;
        slugTitleSyncReady.value = true;
    }
}

function mapLocaleToLcode(loc) {
    if (loc === 'ru') return 'ru';
    if (loc === 'am') return 'am';
    return 'en';
}

function lcodeForTaxonomies() {
    if (isEdit && postLoaded.value?.language?.lcode) {
        return postLoaded.value.language.lcode;
    }
    const lang = activeLanguages.value.find((l) => l.id === form.language_id);
    if (lang?.lcode) return lang.lcode;
    return mapLocaleToLcode(typeof window !== 'undefined' ? window.__locale : 'en');
}

function pruneSelectedTermIds() {
    const valid = new Set();
    for (const tax of taxonomyOptions.value) {
        for (const term of tax.terms || []) {
            valid.add(term.id);
        }
    }
    selectedTermIds.value = selectedTermIds.value.filter((id) => valid.has(id));
}

async function loadTaxonomyOptions() {
    taxonomiesLoading.value = true;
    setCsrf();
    try {
        const { data } = await axios.get('/admin/blog/api/posts/taxonomy-terms', {
            params: { lang: lcodeForTaxonomies() },
            headers: { Accept: 'application/json' },
        });
        taxonomyOptions.value = Array.isArray(data) ? data : [];
        pruneSelectedTermIds();
    } catch (_) {
        taxonomyOptions.value = [];
        selectedTermIds.value = [];
    } finally {
        taxonomiesLoading.value = false;
    }
}

watch(
    () => form.language_id,
    () => {
        if (isEdit || languageLocked.value) return;
        loadTaxonomyOptions();
    },
);

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

async function save() {
    setCsrf();
    saving.value = true;
    saveError.value = '';
    errors.title = '';
    errors.slug = '';

    const payload = {
        title: (form.title || '').trim(),
        slug: (form.slug || '').trim() || null,
        excerpt: (form.excerpt || '').trim() || null,
        content: form.content || null,
        featured_image: (form.featured_image || '').trim() || null,
        status: form.status,
    };

    if (form.status === 'published' && publishDate.value && publishTime.value) {
        payload.published_at = `${publishDate.value} ${publishTime.value}:00`;
    } else {
        payload.published_at = null;
    }

    if (!isEdit) {
        payload.language_id = form.language_id;
        if (translationGroupFromQuery.value) {
            payload.translation_group_id = translationGroupFromQuery.value;
        }
    }

    payload.term_ids = [...selectedTermIds.value];

    try {
        if (isEdit) {
            await axios.put(`/admin/blog/api/posts/${editId}`, payload);
        } else {
            await axios.post('/admin/blog/api/posts', payload);
        }
        window.location.href = '/admin/blog/posts';
    } catch (e) {
        const data = e.response?.data;
        if (data?.errors) {
            if (data.errors.title) errors.title = data.errors.title[0];
            if (data.errors.slug) errors.slug = data.errors.slug[0];
        }
        saveError.value = data?.message || t('admin.blog_posts.save_error');
    } finally {
        saving.value = false;
    }
}

async function boot() {
    setCsrf();
    try {
        const { data } = await axios.get('/admin/settings/api/languages');
        activeLanguages.value = (data || []).filter((l) => l.status === 'active');
    } catch (_) {
        activeLanguages.value = [];
    }
    if (isEdit) {
        await loadPost();
        await loadTaxonomyOptions();
    } else {
        applyCreateLanguageFromQuery();
        await loadTaxonomyOptions();
        loading.value = false;
    }
}

onMounted(boot);
</script>

