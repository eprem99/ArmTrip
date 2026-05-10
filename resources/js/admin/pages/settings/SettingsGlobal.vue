<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ t('settings.global_title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ t('settings.global_description') }}
            </p>
        </div>

        <form class="space-y-6" @submit.prevent="save">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.debug') }}</h2>

                <div class="mt-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ t('settings.debug_toggle') }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ t('settings.debug_hint') }}</p>
                    </div>

                    <label class="relative inline-flex cursor-pointer items-center">
                        <input v-model="form.debug" type="checkbox" class="peer sr-only" />
                        <div
                            class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-focus:outline-none"
                        />
                    </label>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.media_storage') }}</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="media_storage_disk" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.media_storage_disk') }}
                        </label>
                        <select
                            id="media_storage_disk"
                            v-model="form.media_storage_disk"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option v-for="d in disks" :key="d" :value="d">
                                {{ d }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">{{ t('settings.media_storage_disk_hint') }}</p>
                    </div>

                    <div v-if="form.media_storage_disk === 's3'" class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="aws_access_key_id" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.aws_access_key_id') }}
                            </label>
                            <input
                                id="aws_access_key_id"
                                v-model="form.aws_access_key_id"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="aws_secret_access_key" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.aws_secret_access_key') }}
                            </label>
                            <input
                                id="aws_secret_access_key"
                                v-model="form.aws_secret_access_key"
                                type="password"
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="aws_default_region" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.aws_default_region') }}
                            </label>
                            <input
                                id="aws_default_region"
                                v-model="form.aws_default_region"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="aws_bucket" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.aws_bucket') }}
                            </label>
                            <input
                                id="aws_bucket"
                                v-model="form.aws_bucket"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="aws_url" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.aws_url') }}
                            </label>
                            <input
                                id="aws_url"
                                v-model="form.aws_url"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="https://…"
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="aws_endpoint" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.aws_endpoint') }}
                            </label>
                            <input
                                id="aws_endpoint"
                                v-model="form.aws_endpoint"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="https://…"
                            />
                        </div>
                        <div class="flex items-center gap-2 sm:col-span-2">
                            <input
                                id="aws_use_path_style_endpoint"
                                v-model="form.aws_use_path_style_endpoint"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            />
                            <label for="aws_use_path_style_endpoint" class="text-sm text-slate-700">
                                {{ t('settings.aws_use_path_style_endpoint') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.caching') }}</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="cache_store" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.cache_store') }}
                        </label>
                        <select
                            id="cache_store"
                            v-model="form.cache_store"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option v-for="s in cacheStores" :key="s" :value="s">
                                {{ s }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">{{ t('settings.cache_store_hint') }}</p>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="cache_prefix" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.cache_prefix') }}
                        </label>
                        <input
                            id="cache_prefix"
                            v-model="form.cache_prefix"
                            type="text"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div v-if="form.cache_store === 'redis'" class="grid gap-4 sm:col-span-2 sm:grid-cols-2">
                        <div>
                            <label for="redis_host" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.redis_host') }}
                            </label>
                            <input
                                id="redis_host"
                                v-model="form.redis_host"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="redis_port" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.redis_port') }}
                            </label>
                            <input
                                id="redis_port"
                                v-model="form.redis_port"
                                type="text"
                                inputmode="numeric"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="redis_password" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.redis_password') }}
                            </label>
                            <input
                                id="redis_password"
                                v-model="form.redis_password"
                                type="password"
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="redis_db" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.redis_db') }}
                            </label>
                            <input
                                id="redis_db"
                                v-model="form.redis_db"
                                type="text"
                                inputmode="numeric"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="redis_cache_db" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.redis_cache_db') }}
                            </label>
                            <input
                                id="redis_cache_db"
                                v-model="form.redis_cache_db"
                                type="text"
                                inputmode="numeric"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.site_modes') }}</h2>

                <div class="mt-4 space-y-5">
                    <div class="flex items-start justify-between gap-6">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700">{{ t('settings.coming_soon_toggle') }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ t('settings.coming_soon_hint') }}</p>
                        </div>
                        <label class="relative mt-1 inline-flex cursor-pointer items-center">
                            <input v-model="form.site_coming_soon_enabled" type="checkbox" class="peer sr-only" />
                            <div
                                class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-focus:outline-none"
                            />
                        </label>
                    </div>

                    <div v-if="form.site_coming_soon_enabled" class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <label for="coming_soon_title" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.coming_soon_title') }}
                            </label>
                            <input
                                id="coming_soon_title"
                                v-model="form.site_coming_soon_title"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.coming_soon_title_placeholder')"
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="coming_soon_message" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.coming_soon_message') }}
                            </label>
                            <textarea
                                id="coming_soon_message"
                                v-model="form.site_coming_soon_message"
                                rows="3"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.coming_soon_message_placeholder')"
                            />
                        </div>
                    </div>

                    <div class="h-px bg-slate-100" />

                    <div class="flex items-start justify-between gap-6">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700">{{ t('settings.maintenance_toggle') }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ t('settings.maintenance_hint') }}</p>
                        </div>
                        <label class="relative mt-1 inline-flex cursor-pointer items-center">
                            <input v-model="form.site_maintenance_enabled" type="checkbox" class="peer sr-only" />
                            <div
                                class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-focus:outline-none"
                            />
                        </label>
                    </div>

                    <div v-if="form.site_maintenance_enabled">
                        <label for="maintenance_message" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.maintenance_message') }}
                        </label>
                        <textarea
                            id="maintenance_message"
                            v-model="form.site_maintenance_message"
                            rows="3"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.maintenance_message_placeholder')"
                        />
                    </div>
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
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';

const { t } = useI18n();

const disks = ref([]);
const cacheStores = ref([]);

const form = reactive({
    debug: false,
    media_storage_disk: 'uploads',
    aws_access_key_id: '',
    aws_secret_access_key: '',
    aws_default_region: '',
    aws_bucket: '',
    aws_url: '',
    aws_endpoint: '',
    aws_use_path_style_endpoint: false,
    cache_store: 'file',
    cache_prefix: '',
    redis_host: '127.0.0.1',
    redis_port: '6379',
    redis_password: '',
    redis_db: '0',
    redis_cache_db: '1',
    site_coming_soon_enabled: false,
    site_coming_soon_title: '',
    site_coming_soon_message: '',
    site_maintenance_enabled: false,
    site_maintenance_message: '',
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

async function load() {
    setCsrf();
    try {
        const { data } = await axios.get('/admin/settings/api/global');
        disks.value = Array.isArray(data?.disks) ? data.disks : [];
        cacheStores.value = Array.isArray(data?.cache_stores) ? data.cache_stores : [];
        Object.assign(form, data?.values ?? {});
    } catch (e) {
        console.error('Load global settings failed', e);
    }
}

async function save() {
    setCsrf();
    saving.value = true;
    saved.value = false;
    saveError.value = '';
    try {
        await axios.post('/admin/settings/api/global', form);
        saved.value = true;
        setTimeout(() => {
            saved.value = false;
        }, 3000);
    } catch (e) {
        saveError.value = e.response?.data?.message || t('settings.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(load);
</script>

