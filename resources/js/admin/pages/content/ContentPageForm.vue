<template>
    <div class="rounded-lg bg-[#f0f0f1] p-4">
        <div class="mb-4">
            <a
                href="/admin/content"
                class="text-sm text-[#2271b1] hover:underline"
            >
                ← {{ t('admin.content.back') }}
            </a>
        </div>

        <div v-if="loading" class="rounded-lg border border-[#c3c4c7] bg-white p-12 text-center text-sm text-slate-600 shadow-sm">
            {{ t('admin.content.loading') }}
        </div>

        <form v-else class="flex flex-row gap-4 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start lg:gap-6" @submit.prevent="save">
            <!-- Main content (left) -->
            <div class="flex min-w-0 flex-col gap-4 lg:order-1">
                <!-- Title + Permalink -->
                <ContentTitlePermalinkCard
                    :title="form.title"
                    :slug="form.slug"
                    :is-home="form.is_home"
                    :permalink-base="permalinkShown"
                    :slug-locked="slugLocked"
                    :slug-checking="slugChecking"
                    :slug-available="slugAvailable"
                    :title-error="errors.title"
                    :slug-error="errors.slug"
                    :title-placeholder="t('admin.content.form_title_placeholder')"
                    :permalink-label="t('admin.content.permalink')"
                    :slug-sample="t('admin.content.slug_sample')"
                    :slug-edit-label="t('admin.content.slug_edit')"
                    :slug-checking-label="t('admin.content.slug_checking')"
                    :slug-available-label="t('admin.content.slug_available')"
                    :slug-taken-label="t('admin.content.slug_taken')"
                    @update:title="(v) => (form.title = v)"
                    @update:slug="(v) => (form.slug = v)"
                    @unlock-slug="unlockSlug"
                    @lock-slug="lockSlug"
                />

                <!-- Add Media -->
                <ContentAddMediaButton @click="openMediaPickerForEditor">
                    {{ t('admin.content.add_media') }}
                </ContentAddMediaButton>

                <!-- Editor (TinyMCE) -->
                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm overflow-hidden">
                    <Editor
                        v-model="form.content"
                        :init="tinymceInit"
                        :tinymce-script-src="tinymceScriptSrc"
                        @init="handleEditorInit"
                    />
                    <div class="flex items-center justify-between border-t border-[#c3c4c7] bg-[#f6f7f7] px-4 py-2 text-xs text-slate-500">
                        <span>{{ t('admin.content.word_count') }}: {{ wordCount }}</span>
                        <span v-if="isEdit && pageLoaded?.updated_at">{{ t('admin.content.last_edited') }} {{ formatDate(pageLoaded.updated_at) }}</span>
                    </div>
                </div>

                <ContentExcerptCard
                    v-if="form.excerpt !== undefined"
                    v-model="form.excerpt"
                    :label="t('admin.content.form_excerpt')"
                    id="excerpt"
                />

                <!-- SEO -->
                <SeoBlock
                    v-model:seoTitle="seoTitle"
                    v-model:seoDescription="seoDescription"
                    :title-label="t('admin.content.seo_title')"
                    :meta-title-label="t('admin.content.seo_meta_title')"
                    :meta-description-label="t('admin.content.seo_meta_description')"
                />
            </div>

            <!-- Sidebar (right) -->
            <aside class="flex w-auto flex-col gap-4 lg:order-2 lg:sticky lg:top-20">
                <!-- Publish -->
                <ContentPublishCard
                    :title="t('admin.content.publish_box')"
                    :status="form.status"
                    :status-edit-open="statusEditOpen"
                    :is-edit="isEdit"
                    :saving="saving"
                    :save-error="saveError"
                    :status-text="form.status === 'published' ? t('admin.content.status_published') : t('admin.content.status_draft')"
                    :published-on="isEdit && pageLoaded?.created_at ? formatDate(pageLoaded.created_at) : ''"
                    :preview-label="t('admin.content.preview_changes')"
                    :status-label="t('admin.content.col_status')"
                    :edit-label="t('admin.content.edit_link')"
                    :draft-label="t('admin.content.status_draft')"
                    :published-label="t('admin.content.status_published')"
                    :visibility-label="t('admin.content.visibility_public')"
                    :published-on-label="t('admin.content.published_on')"
                    :trash-label="t('admin.content.move_to_trash')"
                    :submit-text="saving ? t('admin.content.form_saving') : (isEdit ? t('admin.content.update') : t('admin.content.publish_btn'))"
                    @toggle-status-edit="statusEditOpen = !statusEditOpen"
                    @update:status="(v) => { form.status = v; statusEditOpen = false; }"
                    @trash="confirmTrash"
                />

                <!-- Page Attributes -->
                <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
                    <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">{{ t('admin.content.page_attributes') }}</h3>
                    </div>
                    <div class="space-y-3 p-4">
                        <div v-if="!isEdit">
                            <label for="page_language_id" class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.content.form_language') }}</label>
                            <select
                                id="page_language_id"
                                v-model.number="form.language_id"
                                :disabled="languageLocked"
                                class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1] disabled:bg-slate-100"
                                @change="onLanguageChange"
                            >
                                <option v-for="lang in activeLanguages" :key="lang.id" :value="lang.id">
                                    {{ lang.native_name || lang.name }} ({{ lang.lcode }})
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">{{ t('admin.content.form_language_hint') }}</p>
                        </div>
                        <div v-else-if="pageLoaded?.language">
                            <p class="text-sm text-slate-600">
                                <span class="font-medium text-slate-800">{{ t('admin.content.form_language') }}:</span>
                                {{ pageLoaded.language.native_name || pageLoaded.language.name }}
                                ({{ pageLoaded.language.lcode }})
                            </p>
                        </div>
                        <div>
                            <label for="parent_id" class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.content.parent') }}</label>
                            <select
                                id="parent_id"
                                v-model="form.parent_id"
                                class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                            >
                                <option :value="null">{{ t('admin.content.no_parent') }}</option>
                                <option v-for="p in parentPageOptions" :key="p.id" :value="p.id">{{ p.title }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="sort_order" class="mb-1 block text-sm font-medium text-slate-700">{{ t('admin.content.order') }}</label>
                            <input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <input
                                id="is_home"
                                v-model="form.is_home"
                                type="checkbox"
                                class="h-4 w-4 rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1]"
                            />
                            <label for="is_home" class="text-sm text-slate-700">{{ t('admin.content.form_is_home') }}</label>
                        </div>
                        <p class="text-xs text-slate-500">{{ t('admin.content.help_tab_text') }}</p>
                    </div>
                </div>

                <!-- Featured Image -->
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

            </aside>
        </form>

        <!-- Trash confirm modal -->
        <Teleport to="body">
            <div
                v-if="trashModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                aria-modal="true"
                role="dialog"
            >
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="trashModalOpen = false" />
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">{{ t('admin.content.delete_confirm_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ t('admin.content.delete_confirm_message') }} «{{ form.title || t('admin.content.form_title_placeholder') }}»?</p>
                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            @click="trashModalOpen = false"
                        >
                            {{ t('admin.content.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                            @click="doTrash"
                        >
                            {{ deleting ? t('admin.content.loading') : t('admin.content.delete_confirm_confirm') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

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

const { t, locale } = useI18n();

const tinymceScriptSrc = typeof window !== 'undefined' ? window.location.origin + '/tinymce/tinymce.min.js' : '';

const editorInstance = ref(null);
const mediaPickerMode = ref('featured'); // featured | editor
const tinymceFilePicker = ref(null); // (url, meta) => void

function handleEditorInit(_evt, editor) {
    editorInstance.value = editor;
}

function openMediaPickerForEditor() {
    mediaPickerMode.value = 'editor';
    mediaPickerOpen.value = true;
}

function openMediaPickerForFeatured() {
    mediaPickerMode.value = 'featured';
    mediaPickerOpen.value = true;
}

const tinymceInit = {
    base_url: '/tinymce',
    suffix: '.min',
    height: 400,
    menubar: false,
    plugins: ['lists', 'link', 'image', 'code', 'table'],
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote | link armtripmedia | code',
    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3',
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
    const m = window.location.pathname.match(/^\/admin\/content\/pages\/(\d+)\/edit$/);
    return m ? m[1] : null;
})();
const isEdit = !!editId;

const form = reactive({
    title: '',
    slug: '',
    content: '',
    excerpt: '',
    status: 'draft',
    is_home: false,
    sort_order: 0,
    parent_id: null,
    featured_image: '',
    language_id: null,
});

const errors = reactive({ title: '', slug: '' });
const saving = ref(false);
const saveError = ref('');
const loading = ref(true);
const pageLoaded = ref(null);
const allPages = ref([]);
const showFeaturedUrl = ref(false);
const featuredImageError = ref(false);
const mediaPickerOpen = ref(false);
const statusEditOpen = ref(false);
const trashModalOpen = ref(false);
const deleting = ref(false);

const seoTitle = ref('');
const seoDescription = ref('');

const activeLanguages = ref([]);
const translationGroupFromQuery = ref('');
const languageLocked = ref(false);

const permalinkBase = computed(() => {
    if (typeof window === 'undefined') return '';
    return window.location.origin + '/';
});

const permalinkShown = computed(() => {
    const base = permalinkBase.value;
    if (!base) return '';
    return form.is_home ? base : base;
});

const slugLocked = ref(true);
const slugChecking = ref(false);
const slugAvailable = ref(null); // null | boolean
const slugSuggest = ref('');
let slugCheckTimer = null;
/** After initial load in edit mode, title→slug sync is allowed (avoids overwriting saved slug on load). */
const slugTitleSyncReady = ref(!isEdit);
let titleSlugTimer = null;

async function applySuggestedPageSlug() {
    if (form.is_home || !slugLocked.value || !slugTitleSyncReady.value) return;
    setCsrf();
    try {
        const { data } = await axios.get('/admin/content/api/pages/suggest-slug', {
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
    if (form.language_id == null) {
        return;
    }
    slugChecking.value = true;
    try {
        const res = await axios.get('/admin/content/api/pages/check-slug', {
            params: {
                slug,
                ignore_id: editId ? Number(editId) : null,
                language_id: form.language_id,
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
    () => form.is_home,
    (isHome) => {
        if (isHome) {
            form.slug = '';
            slugLocked.value = true;
        } else {
            applySuggestedPageSlug();
        }
    },
);

watch(
    () => form.title,
    () => {
        if (form.is_home) return;
        if (!slugLocked.value) return;
        if (titleSlugTimer) clearTimeout(titleSlugTimer);
        titleSlugTimer = setTimeout(() => {
            applySuggestedPageSlug();
        }, 400);
    },
);

watch(
    () => form.slug,
    (slug) => {
        if (form.is_home) return;
        if (slugCheckTimer) clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(async () => {
            await checkSlugUnique((slug || '').trim());
            if (slugLocked.value && slugAvailable.value === false && slugSuggest.value) {
                form.slug = slugSuggest.value;
            }
        }, 350);
    },
);

const wordCount = computed(() => {
    const text = (form.content || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    return text ? text.split(/\s/).length : 0;
});

const parentPageOptions = computed(() => {
    const lid = form.language_id;
    return allPages.value.filter((p) => {
        if (p.id === parseInt(editId, 10)) return false;
        if (lid == null) return true;

        return Number(p.language_id) === Number(lid);
    });
});

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
    const byLocale = activeLanguages.value.find((l) => l.lcode === mapLocaleToLcode(typeof window !== 'undefined' ? window.__locale : 'en'));
    const pick = byQuery || byLocale || activeLanguages.value[0];
    if (pick) {
        form.language_id = pick.id;
    }
    languageLocked.value = !!(translationGroupFromQuery.value && wanted);
}

function onLanguageChange() {
    if (!isEdit) {
        loadPages();
    }
}

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
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

async function loadPage() {
    if (!editId) return;
    setCsrf();
    try {
        const { data } = await axios.get(`/admin/content/api/pages/${editId}`);
        pageLoaded.value = data;
        form.title = data.title ?? '';
        form.slug = data.slug ?? '';
        form.content = data.content ?? '';
        form.excerpt = data.excerpt ?? '';
        form.status = data.status ?? 'draft';
        form.is_home = data.is_home ?? false;
        form.sort_order = data.sort_order ?? 0;
        form.parent_id = data.parent_id ?? null;
        form.featured_image = data.featured_image ?? '';
        form.language_id = data.language_id ?? null;
    } catch (e) {
        saveError.value = e.response?.data?.message || t('admin.content.load_error');
    } finally {
        loading.value = false;
        slugTitleSyncReady.value = true;
    }
}

async function loadPages() {
    setCsrf();
    try {
        const lang = activeLanguages.value.find((l) => Number(l.id) === Number(form.language_id));
        const lcode = lang?.lcode || 'en';
        const { data } = await axios.get('/admin/content/api/pages', {
            params: { lang: lcode, flat: 1 },
        });
        allPages.value = data;
    } catch (_) {
        allPages.value = [];
    }
}

async function save() {
    setCsrf();
    errors.title = '';
    errors.slug = '';
    saveError.value = '';
    saving.value = true;
    try {
        const payload = {
            title: form.title.trim(),
            slug: form.slug.trim(),
            content: form.content || '',
            excerpt: form.excerpt?.trim() || null,
            status: form.status,
            is_home: form.is_home,
            sort_order: Number(form.sort_order) || 0,
            parent_id: form.parent_id || null,
            featured_image: form.featured_image?.trim() || null,
        };
        if (!isEdit) {
            payload.language_id = form.language_id;
            if (translationGroupFromQuery.value) {
                payload.translation_group_id = translationGroupFromQuery.value;
            }
        }
        if (isEdit) {
            await axios.put(`/admin/content/api/pages/${editId}`, payload);
        } else {
            await axios.post('/admin/content/api/pages', payload);
        }
        window.location.href = '/admin/content';
    } catch (e) {
        const data = e.response?.data;
        if (data?.errors) {
            if (data.errors.title) errors.title = data.errors.title[0];
            if (data.errors.slug) errors.slug = data.errors.slug[0];
        }
        saveError.value = data?.message || t('admin.content.save_error');
    } finally {
        saving.value = false;
    }
}

function confirmTrash() {
    trashModalOpen.value = true;
}

async function doTrash() {
    if (!editId) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/content/api/pages/${editId}`);
        window.location.href = '/admin/content';
    } catch (e) {
        saveError.value = e.response?.data?.message || t('admin.content.load_error');
    } finally {
        deleting.value = false;
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
        await loadPage();
        await loadPages();
    } else {
        applyCreateLanguageFromQuery();
        await loadPages();
        loading.value = false;
    }
}

onMounted(boot);
</script>

