<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ t('settings.footer_title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ t('settings.footer_description') }}</p>
        </div>

        <form class="space-y-6" @submit.prevent="save">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.footer_copyright') }}</h2>
                <div class="mt-4">
                    <label for="footer_copyright" class="block text-sm font-medium text-slate-700">
                        {{ t('settings.footer_copyright_text') }}
                    </label>
                    <input
                        id="footer_copyright"
                        v-model="form.footer_copyright"
                        type="text"
                        class="mt-1 block w-full max-w-2xl rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        :placeholder="t('settings.footer_copyright_placeholder')"
                    />
                    <p class="mt-1 text-xs text-slate-500">{{ t('settings.footer_copyright_hint') }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.footer_social') }}</h2>
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                        @click="addSocial"
                    >
                        {{ t('settings.footer_social_add') }}
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <div
                        v-for="(row, idx) in form.footer_social_links"
                        :key="idx"
                        class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50/40 p-4 sm:grid-cols-12"
                    >
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                {{ t('settings.footer_social_label') }}
                            </label>
                            <input
                                v-model="row.label"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.footer_social_label_placeholder')"
                            />
                        </div>
                        <div class="sm:col-span-7">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                {{ t('settings.footer_social_url') }}
                            </label>
                            <input
                                v-model="row.url"
                                type="url"
                                class="mt-1 block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="https://…"
                            />
                        </div>
                        <div class="sm:col-span-1 flex items-end justify-end">
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-900"
                                :aria-label="t('settings.footer_social_remove')"
                                @click="removeSocial(idx)"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <p v-if="form.footer_social_links.length === 0" class="text-sm text-slate-500">
                        {{ t('settings.footer_social_empty') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="saving"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ saving ? t('settings.saving') : t('settings.save') }}
                </button>
                <p v-if="saved" class="text-sm text-green-600">{{ t('settings.saved') }}</p>
                <p v-if="saveError" class="text-sm text-red-600">{{ saveError }}</p>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';

const { t } = useI18n();

const form = reactive({
    footer_copyright: '',
    footer_social_links: [],
});

const saving = ref(false);
const saved = ref(false);
const saveError = ref('');

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function addSocial() {
    form.footer_social_links.push({ label: '', url: '' });
}

function removeSocial(idx) {
    form.footer_social_links.splice(idx, 1);
}

async function load() {
    setCsrf();
    try {
        const { data } = await axios.get('/admin/settings/api/footer');
        form.footer_copyright = data?.footer_copyright ?? '';
        form.footer_social_links = Array.isArray(data?.footer_social_links) ? data.footer_social_links : [];
    } catch (e) {
        console.error('Load footer settings failed', e);
    }
}

async function save() {
    setCsrf();
    saving.value = true;
    saved.value = false;
    saveError.value = '';
    try {
        await axios.post('/admin/settings/api/footer', form);
        saved.value = true;
        setTimeout(() => (saved.value = false), 3000);
    } catch (e) {
        saveError.value = e.response?.data?.message || t('settings.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(load);
</script>

