<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-[60] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="close" />
            <div
                class="relative flex max-h-[85vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200"
            >
                <div class="flex shrink-0 items-start justify-between gap-3 border-b border-slate-200 px-4 py-3">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-semibold text-slate-900">{{ title }}</h3>
                        <div class="mt-2 inline-flex rounded-full bg-slate-100/90 p-1 ring-1 ring-slate-200/60">
                            <button
                                type="button"
                                class="rounded-full px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                                :class="
                                    tab === 'upload'
                                        ? 'bg-white text-blue-700 shadow-sm ring-1 ring-slate-200/80'
                                        : 'text-slate-600 hover:text-slate-900'
                                "
                                @click="tab = 'upload'"
                            >
                                {{ t('admin.content.picker_tab_upload') }}
                            </button>
                            <button
                                type="button"
                                class="rounded-full px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                                :class="
                                    tab === 'library'
                                        ? 'bg-white text-blue-700 shadow-sm ring-1 ring-slate-200/80'
                                        : 'text-slate-600 hover:text-slate-900'
                                "
                                @click="tab = 'library'"
                            >
                                {{ t('admin.content.picker_tab_select') }}
                            </button>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="shrink-0 rounded-lg p-2 text-slate-500 hover:bg-slate-100"
                        :aria-label="t('admin.content.picker_close')"
                        @click="close"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto p-4">
                    <!-- Upload -->
                    <div v-show="tab === 'upload'" class="space-y-5">
                        <div>
                            <label for="picker-file-input" class="block text-sm font-semibold text-slate-800">
                                {{ t('admin.media_form.form_file') }}
                            </label>
                            <input
                                id="picker-file-input"
                                ref="fileInputRef"
                                type="file"
                                accept="image/*"
                                class="sr-only"
                                @change="onFileChange"
                            />
                            <div
                                class="group relative mt-2 overflow-hidden rounded-2xl transition-all duration-200 outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                :class="dropZoneSurfaceClass"
                                role="button"
                                tabindex="0"
                                :aria-label="t('admin.media_form.form_drop_title')"
                                @click="openFilePicker"
                                @keydown.enter.prevent="openFilePicker"
                                @keydown.space.prevent="openFilePicker"
                                @dragenter.prevent="onDragEnter"
                                @dragleave.prevent="onDragLeave"
                                @dragover.prevent="onDragOver"
                                @drop.prevent="onDrop"
                            >
                                <div
                                    class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-gradient-to-br from-sky-400/30 to-blue-500/20 blur-3xl"
                                    aria-hidden="true"
                                />
                                <div
                                    class="pointer-events-none absolute -bottom-8 -left-8 h-28 w-28 rounded-full bg-gradient-to-tr from-violet-400/25 to-indigo-400/15 blur-3xl"
                                    aria-hidden="true"
                                />
                                <div
                                    class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(148,163,184,0.07)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.07)_1px,transparent_1px)] bg-[size:20px_20px]"
                                    aria-hidden="true"
                                />

                                <div v-if="!selectedFile" class="relative flex cursor-pointer flex-col items-center justify-center gap-3 px-6 py-14 text-center">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-md ring-1 ring-slate-200/80 transition-transform duration-200 group-hover:scale-105"
                                        :class="isDragOver ? 'scale-105 ring-blue-200 shadow-blue-500/20' : ''"
                                    >
                                        <ArrowUpTrayIcon
                                            class="h-7 w-7 text-blue-600"
                                            :class="isDragOver ? 'text-blue-700' : 'text-blue-600'"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{
                                                isDragOver
                                                    ? t('admin.media_form.form_drop_active')
                                                    : t('admin.media_form.form_drop_title')
                                            }}
                                        </p>
                                        <p class="mt-1.5 text-xs text-slate-500">
                                            <span>{{ t('admin.media_form.form_drop_or') }}</span>
                                            <span class="font-semibold text-blue-600">
                                                {{ t('admin.media_form.form_drop_browse') }}
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-else
                                    class="relative flex cursor-pointer flex-col items-stretch gap-4 p-5 sm:flex-row sm:items-center"
                                >
                                    <div
                                        class="mx-auto h-28 w-28 shrink-0 overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-inner ring-1 ring-slate-100 sm:mx-0"
                                    >
                                        <img
                                            v-if="selectedPreviewUrl"
                                            :src="selectedPreviewUrl"
                                            :alt="selectedFile.name"
                                            class="h-full w-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center bg-slate-50 text-slate-300"
                                        >
                                            <PhotoIcon class="h-10 w-10" aria-hidden="true" />
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1 text-center sm:text-left">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                            {{ t('admin.media_form.form_selected_label') }}
                                        </p>
                                        <p class="mt-1 truncate text-sm font-semibold text-slate-900" :title="selectedFile.name">
                                            {{ selectedFile.name }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ formatFileSize(selectedFile.size) }}
                                            <span v-if="selectedFile.type"> · {{ selectedFile.type }}</span>
                                        </p>
                                        <button
                                            type="button"
                                            class="mt-3 text-xs font-semibold text-blue-600 underline decoration-blue-600/30 underline-offset-2 hover:text-blue-800"
                                            @click.stop="openFilePicker"
                                        >
                                            {{ t('admin.media_form.form_change_file') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-center text-xs text-slate-500 sm:text-left">
                                {{ t('admin.content.picker_image_hint') }}
                            </p>
                            <p v-if="uploadFileError" class="mt-1 text-xs font-medium text-red-600">{{ uploadFileError }}</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">{{
                                    t('admin.media_form.form_title')
                                }}</label>
                                <input
                                    v-model="uploadTitle"
                                    type="text"
                                    class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">{{
                                    t('admin.media_form.form_alt')
                                }}</label>
                                <input
                                    v-model="uploadAlt"
                                    type="text"
                                    class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                :disabled="uploading || !selectedFile"
                                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-600/25 transition hover:from-blue-700 hover:to-blue-800 disabled:pointer-events-none disabled:opacity-45"
                                @click="doUpload"
                            >
                                {{ uploading ? t('admin.content.picker_uploading') : t('admin.content.picker_upload_btn') }}
                            </button>
                            <p v-if="uploadError" class="text-sm font-medium text-red-600">{{ uploadError }}</p>
                        </div>

                        <template v-if="allowUrlInsert">
                            <div class="relative py-2">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-slate-200" />
                                </div>
                                <div class="relative flex justify-center">
                                    <span class="bg-white px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        {{ t('admin.content.picker_or_by_url') }}
                                    </span>
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">{{
                                        t('admin.content.insert_url')
                                    }}</label>
                                    <input
                                        v-model="editorUrl"
                                        type="url"
                                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                                        placeholder="https://…"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">{{
                                        t('admin.content.insert_alt')
                                    }}</label>
                                    <input
                                        v-model="editorAlt"
                                        type="text"
                                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    type="button"
                                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
                                    @click="submitUrlInsert"
                                >
                                    {{ t('admin.content.insert_btn') }}
                                </button>
                                <p v-if="editorUrlError" class="text-sm font-medium text-red-600">{{ editorUrlError }}</p>
                            </div>
                        </template>
                    </div>

                    <!-- Library -->
                    <div v-show="tab === 'library'">
                        <div v-if="listLoading" class="py-12 text-center text-sm text-slate-500">
                            {{ t('admin.media.loading') }}
                        </div>
                        <div v-else-if="listError" class="py-12 text-center text-sm text-red-600">
                            {{ listError }}
                        </div>
                        <div
                            v-else
                            class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5"
                        >
                            <button
                                v-for="item in imageList"
                                :key="item.id"
                                type="button"
                                class="group relative aspect-square overflow-hidden rounded-lg border-2 border-transparent bg-slate-100 transition hover:border-blue-500 focus:border-blue-500 focus:outline-none"
                                @click="pickFromLibrary(item)"
                            >
                                <img
                                    :src="item.url"
                                    :alt="item.alt || item.title || item.filename"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    @error="(e) => (e.target.style.display = 'none')"
                                />
                                <span
                                    class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-0.5 text-xs text-white opacity-0 transition group-hover:opacity-100"
                                >
                                    {{ item.title || item.filename }}
                                </span>
                            </button>
                        </div>
                        <p
                            v-if="!listLoading && !listError && imageList.length === 0"
                            class="py-8 text-center text-sm text-slate-500"
                        >
                            {{ t('admin.media.empty') }}
                        </p>
                    </div>
                </div>

                <p
                    v-if="tab === 'library'"
                    class="shrink-0 border-t border-slate-200 bg-slate-50 px-4 py-2 text-xs text-slate-500"
                >
                    {{ t('admin.content.select_media_hint') }}
                </p>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, computed, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { ArrowUpTrayIcon, PhotoIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useI18n } from '../../composables/useI18n';

const { t } = useI18n();

const open = defineModel('open', { type: Boolean, default: false });

const props = defineProps({
    title: { type: String, required: true },
    /** TinyMCE / editor: show URL + alt + Insert under upload tab */
    allowUrlInsert: { type: Boolean, default: false },
    /** Initial tab when modal opens: `upload` | `library` */
    startTab: { type: String, default: 'upload' },
});

const emit = defineEmits(['select', 'insert-url']);

const tab = ref('upload');
const fileInputRef = ref(null);
const selectedFile = ref(null);
const selectedPreviewUrl = ref('');
const isDragOver = ref(false);
let dragDepth = 0;
const uploadTitle = ref('');
const uploadAlt = ref('');
const uploading = ref(false);
const uploadError = ref('');
const uploadFileError = ref('');

const dropZoneSurfaceClass = computed(() => {
    if (isDragOver.value) {
        return 'border-2 border-blue-400 border-solid bg-gradient-to-b from-blue-50/95 to-sky-50/80 shadow-lg shadow-blue-500/10 ring-1 ring-blue-200/60';
    }
    if (selectedFile.value) {
        return 'border-2 border-solid border-slate-200/90 bg-gradient-to-b from-white to-slate-50/40 shadow-sm ring-1 ring-slate-200/50';
    }
    return 'border-2 border-dashed border-slate-300/90 bg-gradient-to-b from-slate-50/80 to-white shadow-sm hover:border-slate-400/90 hover:shadow-md';
});

watch(selectedFile, (f) => {
    if (selectedPreviewUrl.value) {
        URL.revokeObjectURL(selectedPreviewUrl.value);
        selectedPreviewUrl.value = '';
    }
    if (f && f.type && f.type.startsWith('image/')) {
        selectedPreviewUrl.value = URL.createObjectURL(f);
    }
});

onBeforeUnmount(() => {
    if (selectedPreviewUrl.value) {
        URL.revokeObjectURL(selectedPreviewUrl.value);
    }
});

function formatFileSize(bytes) {
    if (bytes == null || bytes === 0) return '—';
    const units = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    let n = bytes;
    while (n >= 1024 && i < units.length - 1) {
        n /= 1024;
        i++;
    }
    return (i === 0 ? n : n.toFixed(1)) + ' ' + units[i];
}

function revokePreview() {
    if (selectedPreviewUrl.value) {
        URL.revokeObjectURL(selectedPreviewUrl.value);
        selectedPreviewUrl.value = '';
    }
}

const listLoading = ref(false);
const listError = ref('');
const imageList = ref([]);

const editorUrl = ref('');
const editorAlt = ref('');
const editorUrlError = ref('');

function ensureCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function resetUploadState() {
    revokePreview();
    selectedFile.value = null;
    uploadTitle.value = '';
    uploadAlt.value = '';
    uploadError.value = '';
    uploadFileError.value = '';
    isDragOver.value = false;
    dragDepth = 0;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
}

function resetUrlInsertState() {
    editorUrl.value = '';
    editorAlt.value = '';
    editorUrlError.value = '';
}

function close() {
    open.value = false;
}

async function loadList() {
    listLoading.value = true;
    listError.value = '';
    ensureCsrf();
    const url = (typeof window !== 'undefined' ? window.location.origin : '') + '/admin/media-list-json';
    try {
        const res = await axios.get(url, {
            params: { per_page: 48 },
            headers: { Accept: 'application/json' },
            validateStatus: () => true,
        });
        if (res.status >= 200 && res.status < 300) {
            const data = res.data;
            const list = Array.isArray(data) ? data : (data?.data ?? []);
            const all = Array.isArray(list) ? list : [];
            imageList.value = all.filter((m) => m.mime_type && m.mime_type.startsWith('image/'));
            listError.value = '';
        } else {
            listError.value = res.data?.message || `HTTP ${res.status}`;
        }
    } catch (e) {
        listError.value = e.response?.data?.message || e.message || t('admin.media.load_error');
    } finally {
        listLoading.value = false;
    }
}

watch(
    () => open.value,
    (isOpen) => {
        if (!isOpen) {
            resetUploadState();
            resetUrlInsertState();
            return;
        }
        tab.value = props.startTab === 'library' ? 'library' : 'upload';
        resetUploadState();
        resetUrlInsertState();
        loadList();
    },
);

function openFilePicker() {
    fileInputRef.value?.click();
}

function setSelectedFile(file) {
    uploadFileError.value = '';
    if (!file) {
        selectedFile.value = null;
        return;
    }
    if (!file.type || !file.type.startsWith('image/')) {
        selectedFile.value = null;
        uploadFileError.value = t('admin.content.picker_invalid_image');
        if (fileInputRef.value) fileInputRef.value.value = '';
        return;
    }
    selectedFile.value = file;
}

function onFileChange(e) {
    const f = e.target.files?.[0];
    setSelectedFile(f || null);
}

function onDragEnter() {
    dragDepth += 1;
    isDragOver.value = true;
}

function onDragLeave() {
    dragDepth = Math.max(0, dragDepth - 1);
    if (dragDepth === 0) {
        isDragOver.value = false;
    }
}

function onDragOver() {
    isDragOver.value = true;
}

function onDrop(e) {
    dragDepth = 0;
    isDragOver.value = false;
    const f = e.dataTransfer?.files?.[0];
    if (f) {
        setSelectedFile(f);
        if (fileInputRef.value) {
            try {
                const dt = new DataTransfer();
                dt.items.add(f);
                fileInputRef.value.files = dt.files;
            } catch {
                /* optional sync */
            }
        }
    }
}

async function doUpload() {
    uploadError.value = '';
    uploadFileError.value = '';
    if (!selectedFile.value) {
        uploadFileError.value = t('admin.content.picker_file_required');
        return;
    }
    ensureCsrf();
    uploading.value = true;
    try {
        const fd = new FormData();
        fd.append('file', selectedFile.value);
        if (uploadTitle.value.trim()) fd.append('title', uploadTitle.value.trim());
        if (uploadAlt.value.trim()) fd.append('alt', uploadAlt.value.trim());
        const res = await axios.post('/admin/media/api', fd, {
            headers: { 'Content-Type': 'multipart/form-data' },
            validateStatus: () => true,
        });
        if (res.status >= 200 && res.status < 300) {
            const media = res.data;
            await loadList();
            if (media?.url) {
                emit('select', media);
                close();
            }
        } else {
            const msg =
                res.data?.message ||
                res.data?.errors?.file?.[0] ||
                t('admin.content.picker_upload_error');
            uploadError.value = msg;
        }
    } catch (e) {
        uploadError.value =
            e.response?.data?.message ||
            e.response?.data?.errors?.file?.[0] ||
            t('admin.content.picker_upload_error');
    } finally {
        uploading.value = false;
    }
}

function pickFromLibrary(item) {
    if (!item?.url) return;
    emit('select', item);
    close();
}

function submitUrlInsert() {
    editorUrlError.value = '';
    const url = (editorUrl.value || '').trim();
    const alt = (editorAlt.value || '').trim();
    if (!url) {
        editorUrlError.value = t('admin.content.insert_url_required');
        return;
    }
    emit('insert-url', { url, alt });
    close();
}
</script>
