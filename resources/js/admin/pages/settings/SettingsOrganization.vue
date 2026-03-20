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
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="organization_name" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.organization_name') }}
                        </label>
                        <input
                            id="organization_name"
                            v-model="form.organization_name"
                            type="text"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.organization_name_placeholder')"
                        />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="organization_logo_light" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.logo_light') }}
                            </label>
                            <input
                                id="organization_logo_light"
                                v-model="form.organization_logo_light"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.logo_placeholder')"
                            />
                        </div>
                        <div>
                            <label for="organization_logo_dark" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.logo_dark') }}
                            </label>
                            <input
                                id="organization_logo_dark"
                                v-model="form.organization_logo_dark"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.logo_placeholder')"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.contacts') }}</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="organization_email" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.email') }}
                        </label>
                        <input
                            id="organization_email"
                            v-model="form.organization_email"
                            type="email"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.email_placeholder')"
                        />
                    </div>
                    <div>
                        <label for="organization_phone" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.phone') }}
                        </label>
                        <input
                            id="organization_phone"
                            v-model="form.organization_phone"
                            type="text"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="t('settings.phone_placeholder')"
                        />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.date_time') }}</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.timezone') }}
                        </label>
                        <select
                            id="timezone"
                            v-model="form.timezone"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
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
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.date_format') }}
                        </label>
                        <select
                            id="date_format"
                            v-model="form.date_format"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
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
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';

const { t } = useI18n();

const form = reactive({
    organization_name: '',
    organization_logo_light: '',
    organization_logo_dark: '',
    organization_email: '',
    organization_phone: '',
    timezone: '',
    date_format: 'd.m.Y',
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

