<template>
    <div class="space-y-6">
        <div>
            <a
                href="/admin/media"
                class="text-sm text-[#2271b1] hover:underline"
            >
                ← {{ t('admin.media_form.back') }}
            </a>
        </div>

        <div v-if="loading" class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-600 shadow-sm">
            {{ t('admin.media.loading') }}
        </div>

        <form v-else class="rounded-2xl border border-slate-200 bg-white shadow-sm" @submit.prevent="save">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h1 class="text-lg font-semibold text-slate-900">
                    {{ isEdit ? t('admin.media_form.form_edit_title') : t('admin.media_form.form_create_title') }}
                </h1>
            </div>
            <div class="space-y-6 p-6">
                <!-- Create: file upload (drop zone) -->
                <div v-if="!isEdit">
                    <label for="media-form-file" class="block text-sm font-medium text-slate-700">
                        {{ t('admin.media_form.form_file') }} *
                    </label>
                    <input
                        id="media-form-file"
                        ref="fileInput"
                        type="file"
                        class="sr-only"
                        @change="onFileChange"
                    />
                    <div
                        class="mt-2 rounded-xl border-2 border-dashed transition-colors outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                        :class="
                            isDragOver
                                ? 'border-blue-500 bg-blue-50'
                                : 'border-slate-300 bg-slate-50 hover:border-slate-400 hover:bg-slate-100/80'
                        "
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
                        <div v-if="!selectedFile" class="flex cursor-pointer flex-col items-center justify-center gap-2 px-6 py-12 text-center">
                            <ArrowUpTrayIcon
                                class="h-10 w-10 text-slate-400"
                                :class="{ 'text-blue-500': isDragOver }"
                                aria-hidden="true"
                            />
                            <p class="text-sm font-medium text-slate-700">
                                {{ isDragOver ? t('admin.media_form.form_drop_active') : t('admin.media_form.form_drop_title') }}
                            </p>
                            <p class="text-xs text-slate-500">
                                <span>{{ t('admin.media_form.form_drop_or') }}</span>
                                <span class="font-medium text-blue-600"> {{ t('admin.media_form.form_drop_browse') }}</span>
                            </p>
                        </div>
                        <div v-else class="flex cursor-pointer flex-col items-stretch gap-3 p-4 sm:flex-row sm:items-center">
                            <div
                                v-if="selectedPreviewUrl"
                                class="mx-auto h-24 w-24 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white sm:mx-0"
                            >
                                <img :src="selectedPreviewUrl" :alt="selectedFile.name" class="h-full w-full object-cover" />
                            </div>
                            <div class="min-w-0 flex-1 text-center sm:text-left">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    {{ t('admin.media_form.form_selected_label') }}
                                </p>
                                <p class="mt-1 truncate text-sm font-medium text-slate-900" :title="selectedFile.name">
                                    {{ selectedFile.name }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ formatSize(selectedFile.size) }}
                                    <span v-if="selectedFile.type"> · {{ selectedFile.type }}</span>
                                </p>
                                <button
                                    type="button"
                                    class="mt-2 text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline"
                                    @click.stop="openFilePicker"
                                >
                                    {{ t('admin.media_form.form_change_file') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ t('admin.media_form.form_file_hint') }}</p>
                    <p v-if="errors.file" class="mt-1 text-xs text-red-600">{{ errors.file }}</p>
                </div>

                <!-- Edit: current file info -->
                <div v-if="isEdit && media" class="flex items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-white">
                        <img
                            v-if="media.mime_type && media.mime_type.startsWith('image/')"
                            :src="media.url"
                            :alt="media.alt || media.title || media.filename"
                            class="h-full w-full object-cover"
                            @error="(e) => e.target.style.display = 'none'"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-slate-400">
                            <DocumentIcon class="h-8 w-8" />
                        </div>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-slate-900">{{ media.filename }}</p>
                        <p class="text-sm text-slate-500">{{ media.mime_type }} · {{ formatSize(media.size) }}</p>
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700">{{ t('admin.media_form.form_title') }}</label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        :placeholder="media?.filename"
                    />
                </div>
                <div>
                    <label for="alt" class="block text-sm font-medium text-slate-700">{{ t('admin.media_form.form_alt') }}</label>
                    <input
                        id="alt"
                        v-model="form.alt"
                        type="text"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label for="caption" class="block text-sm font-medium text-slate-700">{{ t('admin.media_form.form_caption') }}</label>
                    <textarea
                        id="caption"
                        v-model="form.caption"
                        rows="3"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                </div>
                <p v-if="saveError" class="text-sm text-red-600">{{ saveError }}</p>
                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ saving ? t('admin.media_form.form_saving') : t('admin.media_form.form_save') }}
                    </button>
                    <a
                        href="/admin/media"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        {{ t('admin.media_form.form_cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import { ArrowUpTrayIcon, DocumentIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();
const fileInput = ref(null);

const editId = (() => {
    if (typeof window === 'undefined') return null;
    const m = window.location.pathname.match(/^\/admin\/media\/(\d+)\/edit$/);
    return m ? m[1] : null;
})();
const isEdit = !!editId;

const media = ref(null);
const loading = ref(isEdit);
const saving = ref(false);
const saveError = ref('');
const errors = reactive({ file: '' });
const form = reactive({
    title: '',
    alt: '',
    caption: '',
});
const selectedFile = ref(null);
const selectedPreviewUrl = ref('');
const isDragOver = ref(false);
let dragDepth = 0;

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

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function formatSize(bytes) {
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

function openFilePicker() {
    fileInput.value?.click();
}

function setSelectedFile(file) {
    if (!file) {
        selectedFile.value = null;
        return;
    }
    selectedFile.value = file;
    errors.file = '';
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
        if (fileInput.value) {
            try {
                const dt = new DataTransfer();
                dt.items.add(f);
                fileInput.value.files = dt.files;
            } catch {
                /* sync optional */
            }
        }
    }
}

async function loadMedia() {
    if (!editId) return;
    setCsrf();
    try {
        const { data } = await axios.get(`/admin/media/api/${editId}`);
        media.value = data;
        form.title = data.title ?? '';
        form.alt = data.alt ?? '';
        form.caption = data.caption ?? '';
    } catch (e) {
        saveError.value = e.response?.data?.message || t('admin.media_form.save_error');
    } finally {
        loading.value = false;
    }
}

async function save() {
    setCsrf();
    saveError.value = '';
    errors.file = '';
    if (!isEdit) {
        if (!selectedFile.value) {
            errors.file = t('admin.media_form.file_required');
            return;
        }
    }
    saving.value = true;
    try {
        if (isEdit) {
            await axios.put(`/admin/media/api/${editId}`, {
                title: form.title || null,
                alt: form.alt || null,
                caption: form.caption || null,
            });
            window.location.href = '/admin/media';
        } else {
            const fd = new FormData();
            fd.append('file', selectedFile.value);
            if (form.title) fd.append('title', form.title);
            if (form.alt) fd.append('alt', form.alt);
            if (form.caption) fd.append('caption', form.caption);
            await axios.post('/admin/media/api', fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            window.location.href = '/admin/media';
        }
    } catch (e) {
        const msg = e.response?.data?.message || e.response?.data?.errors?.file?.[0] || t('admin.media_form.save_error');
        saveError.value = msg;
        if (e.response?.data?.errors?.file) {
            errors.file = e.response.data.errors.file[0];
        }
    } finally {
        saving.value = false;
    }
}

onMounted(loadMedia);
</script>

