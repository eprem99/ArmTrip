<template>
    <div class="space-y-6">
        <div>
            <a
                href="/admin/users"
                class="text-sm text-[#2271b1] hover:underline"
            >
                ← {{ t('admin.users.back') }}
            </a>
        </div>

        <div v-if="loading" class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-600 shadow-sm">
            {{ t('admin.users.loading') }}
        </div>

        <form v-else class="rounded-2xl border border-slate-200 bg-white shadow-sm" @submit.prevent="save">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h1 class="text-lg font-semibold text-slate-900">
                    {{ isEdit ? t('admin.users.form_edit_title') : t('admin.users.form_create_title') }}
                </h1>
            </div>
            <div class="space-y-6 p-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">{{ t('admin.users.form_name') }}</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                    <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">{{ t('admin.users.form_email') }}</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                    <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email }}</p>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">
                        {{ t('admin.users.form_password') }}
                        <span v-if="isEdit" class="font-normal text-slate-500">({{ t('admin.users.form_password_optional') }})</span>
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                    <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700">{{ t('admin.users.form_type') }}</label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">{{ t('admin.users.form_type_user') }}</option>
                        <option value="admin">{{ t('admin.users.form_type_admin') }}</option>
                    </select>
                </div>
                <p v-if="saveError" class="text-sm text-red-600">{{ saveError }}</p>
                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ saving ? t('admin.users.form_saving') : t('admin.users.form_save') }}
                    </button>
                    <a
                        href="/admin/users"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        {{ t('admin.users.form_cancel') }}
                    </a>
                </div>
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
    const m = window.location.pathname.match(/^\/admin\/users\/(\d+)\/edit$/);
    return m ? m[1] : null;
})();
const isEdit = !!editId;

const loading = ref(!!editId);
const saving = ref(false);
const saveError = ref('');
const form = reactive({
    name: '',
    email: '',
    password: '',
    type: '',
});
const errors = reactive({
    name: '',
    email: '',
    password: '',
});

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

async function loadUser() {
    if (!editId) return;
    setCsrf();
    try {
        const { data } = await axios.get(`/admin/users/api/${editId}`);
        form.name = data.name ?? '';
        form.email = data.email ?? '';
        form.type = data.type ?? '';
    } catch (e) {
        saveError.value = e.response?.data?.message || t('admin.users.save_error');
    } finally {
        loading.value = false;
    }
}

async function save() {
    setCsrf();
    saving.value = true;
    saveError.value = '';
    errors.name = '';
    errors.email = '';
    errors.password = '';

    const payload = {
        name: form.name,
        email: form.email,
        type: form.type || null,
    };
    if (form.password) {
        payload.password = form.password;
    }

    try {
        if (isEdit) {
            await axios.put(`/admin/users/api/${editId}`, payload);
        } else {
            await axios.post('/admin/users/api', {
                ...payload,
                // for create password is required, but if empty just send as is to get validation error
                password: form.password || '',
            });
        }
        window.location.href = '/admin/users';
    } catch (e) {
        const data = e.response?.data;
        if (data?.errors) {
            if (data.errors.name) errors.name = data.errors.name[0];
            if (data.errors.email) errors.email = data.errors.email[0];
            if (data.errors.password) errors.password = data.errors.password[0];
        }
        saveError.value = data?.message || t('admin.users.save_error');
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    if (isEdit) {
        loadUser();
    } else {
        loading.value = false;
    }
});
</script>

