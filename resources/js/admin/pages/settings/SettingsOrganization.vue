<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ t('settings.organization_title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ t('settings.organization_description') }}
            </p>
        </div>

        <form class="space-y-6" @submit.prevent="save">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.main') }}</h2>
                <div class="mt-4 grid gap-5 lg:grid-cols-12">
                    <div class="lg:col-span-7 space-y-4">
                        <div>
                            <label for="organization_name" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.organization_name') }}
                            </label>
                            <input
                                id="organization_name"
                                v-model="form.organization_name"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.organization_name_placeholder')"
                            />
                        </div>

                        <div>
                            <label for="organization_description" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.organization_description_label') }}
                            </label>
                            <textarea
                                id="organization_description"
                                v-model="form.organization_description"
                                rows="3"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.organization_description_placeholder')"
                            />
                            <p class="mt-1 text-xs text-slate-500">{{ t('settings.organization_description_hint') }}</p>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                            <button
                                type="button"
                                class="group flex w-full items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50/40 p-4 text-left shadow-sm transition hover:bg-white hover:shadow"
                                @click="openPicker('organization_logo_light')"
                            >
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white">
                                    <img
                                        v-if="form.organization_logo_light"
                                        :src="form.organization_logo_light"
                                        :alt="t('settings.logo_light')"
                                        class="h-full w-full object-contain p-1"
                                        loading="lazy"
                                        @error="(e) => (e.target.style.display = 'none')"
                                    />
                                    <span v-else class="text-slate-400 text-xl">🖼️</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ t('settings.logo_light') }}</p>
                                        <span class="text-xs font-semibold text-blue-600 group-hover:text-blue-700">
                                            {{ t('settings.pick_from_library') }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ form.organization_logo_light || t('settings.image_not_selected') }}
                                    </p>
                                </div>
                            </button>

                            <button
                                type="button"
                                class="group flex w-full items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50/40 p-4 text-left shadow-sm transition hover:bg-white hover:shadow"
                                @click="openPicker('organization_logo_dark')"
                            >
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white">
                                    <img
                                        v-if="form.organization_logo_dark"
                                        :src="form.organization_logo_dark"
                                        :alt="t('settings.logo_dark')"
                                        class="h-full w-full object-contain p-1"
                                        loading="lazy"
                                        @error="(e) => (e.target.style.display = 'none')"
                                    />
                                    <span v-else class="text-slate-400 text-xl">🖼️</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ t('settings.logo_dark') }}</p>
                                        <span class="text-xs font-semibold text-blue-600 group-hover:text-blue-700">
                                            {{ t('settings.pick_from_library') }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ form.organization_logo_dark || t('settings.image_not_selected') }}
                                    </p>
                                </div>
                            </button>

                            <button
                                type="button"
                                class="group flex w-full items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50/40 p-4 text-left shadow-sm transition hover:bg-white hover:shadow"
                                @click="openPicker('organization_favicon')"
                            >
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white">
                                    <img
                                        v-if="form.organization_favicon"
                                        :src="form.organization_favicon"
                                        :alt="t('settings.favicon')"
                                        class="h-full w-full object-contain p-1"
                                        loading="lazy"
                                        @error="(e) => (e.target.style.display = 'none')"
                                    />
                                    <span v-else class="text-slate-400 text-xl">🔖</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ t('settings.favicon') }}</p>
                                        <span class="text-xs font-semibold text-blue-600 group-hover:text-blue-700">
                                            {{ t('settings.pick_from_library') }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ form.organization_favicon || t('settings.image_not_selected') }}
                                    </p>
                                    <p class="mt-1 text-[11px] text-slate-400">{{ t('settings.favicon_hint') }}</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.contacts') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="organization_email" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.email') }}
                        </label>
                        <input
                            id="organization_email"
                            v-model="form.organization_email"
                            type="email"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.email_placeholder')"
                        />
                    </div>
                    <div class="sm:col-span-1">
                        <label for="organization_phone" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.phone') }}
                        </label>
                        <input
                            id="organization_phone"
                            v-model="form.organization_phone"
                            type="text"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.phone_placeholder')"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="organization_address" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.address') }}
                        </label>
                        <input
                            id="organization_address"
                            v-model="form.organization_address"
                            type="text"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.address_placeholder')"
                        />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.date_time') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="timezone" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.timezone') }}
                        </label>
                        <select
                            id="timezone"
                            v-model="form.timezone"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">{{ t('settings.timezone_placeholder') }}</option>
                            <option value="Europe/Moscow">Europe/Moscow (МСК)</option>
                            <option value="Asia/Yerevan">Asia/Yerevan (Ереван)</option>
                            <option value="Europe/London">Europe/London</option>
                            <option value="Europe/Paris">Europe/Paris</option>
                            <option value="America/New_York">America/New_York</option>
                            <option value="UTC">UTC</option>
                        </select>
                    </div>
                    <div class="sm:col-span-1">
                        <label for="date_format" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.date_format') }}
                        </label>
                        <select
                            id="date_format"
                            v-model="form.date_format"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="d.m.Y">31.12.2025</option>
                            <option value="Y-m-d">2025-12-31</option>
                            <option value="d/m/Y">31/12/2025</option>
                            <option value="F j, Y">December 31, 2025</option>
                        </select>
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

        <AdminMediaPickerModal
            v-model:open="mediaPickerOpen"
            :title="t('settings.pick_from_library')"
            start-tab="library"
            @select="onMediaPick"
        />
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import AdminMediaPickerModal from '../../components/media/AdminMediaPickerModal.vue';

const { t } = useI18n();

const form = reactive({
    organization_name: '',
    organization_description: '',
    organization_logo_light: '',
    organization_logo_dark: '',
    organization_favicon: '',
    organization_email: '',
    organization_phone: '',
    organization_address: '',
    timezone: '',
    date_format: 'd.m.Y',
});

const saving = ref(false);
const saved = ref(false);
const saveError = ref('');

const mediaPickerOpen = ref(false);
const mediaPickerTarget = ref('');

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function openPicker(target) {
    mediaPickerTarget.value = target;
    mediaPickerOpen.value = true;
}

function onMediaPick(item) {
    if (!mediaPickerTarget.value) return;
    const url = item?.url;
    if (!url) return;
    form[mediaPickerTarget.value] = url;
}

async function load() {
    setCsrf();
    try {
        const { data } = await axios.get('/admin/settings/api/organization');
        Object.assign(form, data);
    } catch (e) {
        console.error('Load settings failed', e);
    }
}

async function save() {
    setCsrf();
    saving.value = true;
    saved.value = false;
    saveError.value = '';
    try {
        await axios.post('/admin/settings/api/organization', form);
        saved.value = true;
        setTimeout(() => { saved.value = false; }, 3000);
    } catch (e) {
        saveError.value = e.response?.data?.message || t('settings.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(load);
</script>

