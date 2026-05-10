<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ t('settings.smtp_title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ t('settings.smtp_description') }}</p>
        </div>

        <form class="space-y-6" @submit.prevent="save">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">{{ t('settings.smtp') }}</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="mail_mailer" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.mail_mailer') }}
                        </label>
                        <select
                            id="mail_mailer"
                            v-model="form.mail_mailer"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="smtp">smtp</option>
                            <option value="log">log</option>
                            <option value="sendmail">sendmail</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">{{ t('settings.smtp_hint') }}</p>
                    </div>

                    <div>
                        <label for="mail_scheme" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.mail_scheme') }}
                        </label>
                        <select
                            id="mail_scheme"
                            v-model="form.mail_scheme"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">{{ t('settings.mail_scheme_auto') }}</option>
                            <option value="smtp">smtp</option>
                            <option value="smtps">smtps</option>
                        </select>
                    </div>

                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.mail_host') }}
                        </label>
                        <input
                            id="mail_host"
                            v-model="form.mail_host"
                            type="text"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="smtp.gmail.com"
                        />
                    </div>

                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.mail_port') }}
                        </label>
                        <input
                            id="mail_port"
                            v-model="form.mail_port"
                            type="text"
                            inputmode="numeric"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="587"
                        />
                    </div>

                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.mail_username') }}
                        </label>
                        <input
                            id="mail_username"
                            v-model="form.mail_username"
                            type="text"
                            autocomplete="username"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-slate-700">
                            {{ t('settings.mail_password') }}
                        </label>
                        <input
                            id="mail_password"
                            v-model="form.mail_password"
                            type="password"
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div class="sm:col-span-2 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="mail_from_address" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.mail_from_address') }}
                            </label>
                            <input
                                id="mail_from_address"
                                v-model="form.mail_from_address"
                                type="email"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="hello@example.com"
                            />
                        </div>
                        <div>
                            <label for="mail_from_name" class="block text-sm font-medium text-slate-700">
                                {{ t('settings.mail_from_name') }}
                            </label>
                            <input
                                id="mail_from_name"
                                v-model="form.mail_from_name"
                                type="text"
                                class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                :placeholder="t('settings.mail_from_name_placeholder')"
                            />
                        </div>
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
import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';

const { t } = useI18n();

const form = reactive({
    mail_mailer: 'log',
    mail_host: '',
    mail_port: '',
    mail_username: '',
    mail_password: '',
    mail_scheme: '',
    mail_from_address: '',
    mail_from_name: '',
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
        const { data } = await axios.get('/admin/settings/api/smtp');
        Object.assign(form, data ?? {});
    } catch (e) {
        console.error('Load SMTP settings failed', e);
    }
}

async function save() {
    setCsrf();
    saving.value = true;
    saved.value = false;
    saveError.value = '';
    try {
        await axios.post('/admin/settings/api/smtp', form);
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

