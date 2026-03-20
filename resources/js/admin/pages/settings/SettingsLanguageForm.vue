<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-4">
            <a
                href="/admin/settings/languages"
                class="text-sm font-medium text-slate-600 hover:text-slate-900"
            >
                ← {{ t('settings.languages_back') }}
            </a>
        </div>
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ isEdit ? t('settings.languages_edit_title') : t('settings.languages_create_title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ isEdit ? t('settings.languages_edit_description') : t('settings.languages_create_description') }}
            </p>
        </div>

        <div v-if="loading" class="py-8 text-center text-sm text-slate-500">
            {{ t('settings.loading') }}
        </div>
        <form v-else class="space-y-6" @submit.prevent="save">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="space-y-4">
                    <div>
                        <label for="lcode" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.languages_code') }} *
                        </label>
                        <input
                            id="lcode"
                            v-model="form.lcode"
                            type="text"
                            maxlength="10"
                            class="mt-1 block w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="'en'"
                        />
                        <p v-if="errors.lcode" class="mt-1 text-xs text-red-600">{{ errors.lcode }}</p>
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.languages_name') }} *
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="'English'"
                        />
                        <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                    </div>
                    <div>
                        <label for="native_name" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.languages_native_name') }}
                        </label>
                        <input
                            id="native_name"
                            v-model="form.native_name"
                            type="text"
                            class="mt-1 block w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="'English'"
                        />
                    </div>
                    <div>
                        <label for="locale" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.languages_locale') }}
                        </label>
                        <input
                            id="locale"
                            v-model="form.locale"
                            type="text"
                            maxlength="20"
                            class="mt-1 block w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :placeholder="'en'"
                        />
                    </div>
                    <div>
                        <label for="direction" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.languages_direction') }}
                        </label>
                        <select
                            id="direction"
                            v-model="form.direction"
                            class="mt-1 block w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="ltr">LTR</option>
                            <option value="rtl">RTL</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.languages_status') }}
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="active">{{ t('settings.languages_status_active') }}</option>
                            <option value="inactive">{{ t('settings.languages_status_inactive') }}</option>
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
                <a
                    href="/admin/settings/languages"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    {{ t('settings.cancel') }}
                </a>
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

const editId = (() => {
    if (typeof window === 'undefined') return null;
    const m = window.location.pathname.match(/^\/admin\/settings\/languages\/(\d+)\/edit$/);
    return m ? m[1] : null;
})();
const isEdit = !!editId;

const form = reactive({
    lcode: '',
    name: '',
    native_name: '',
    locale: '',
    direction: 'ltr',
    status: 'active',
});

const errors = reactive({
    lcode: '',
    name: '',
});

const saving = ref(false);
const saveError = ref('');
const loading = ref(!!editId);

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

async function loadLanguage() {
    if (!editId) return;
    setCsrf();
    try {
        const { data } = await axios.get(`/admin/settings/api/languages/${editId}`);
        form.lcode = data.lcode ?? '';
        form.name = data.name ?? '';
        form.native_name = data.native_name ?? '';
        form.locale = data.locale ?? '';
        form.direction = data.direction ?? 'ltr';
        form.status = data.status ?? 'active';
    } catch (e) {
        saveError.value = e.response?.data?.message || t('settings.load_error');
    } finally {
        loading.value = false;
    }
}

async function save() {
    setCsrf();
    errors.lcode = '';
    errors.name = '';
    saveError.value = '';
    saving.value = true;
    try {
        const payload = {
            lcode: form.lcode.trim(),
            name: form.name.trim(),
            native_name: form.native_name?.trim() || null,
            locale: form.locale?.trim() || null,
            direction: form.direction,
            status: form.status,
        };
        if (isEdit) {
            await axios.put(`/admin/settings/api/languages/${editId}`, payload);
        } else {
            await axios.post('/admin/settings/api/languages', payload);
        }
        window.location.href = '/admin/settings/languages';
    } catch (e) {
        const data = e.response?.data;
        if (data?.errors) {
            if (data.errors.lcode) errors.lcode = data.errors.lcode[0];
            if (data.errors.name) errors.name = data.errors.name[0];
        }
        saveError.value = data?.message || t('settings.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    setCsrf();
    if (isEdit) loadLanguage();
});
</script>

