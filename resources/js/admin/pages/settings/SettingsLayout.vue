<template>
    <div class="flex min-h-0 flex-1 gap-6">
        <!-- Подпанель меню настроек -->
        <aside class="w-56 shrink-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                {{ t('settings.title') }}
            </p>
            <nav class="mt-2 space-y-0.5">
                <a
                    href="/admin/settings/organization"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                        isOrganization
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                >
                    <BuildingOffice2Icon class="h-5 w-5 shrink-0" />
                    <span>{{ t('settings.organization') }}</span>
                </a>
                <a
                    href="/admin/settings/global"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                        isGlobal
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                >
                    <Cog6ToothIcon class="h-5 w-5 shrink-0" />
                    <span>{{ t('settings.global') }}</span>
                </a>
                <a
                    href="/admin/settings/languages"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                        isLanguages
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                >
                    <LanguageIcon class="h-5 w-5 shrink-0" />
                    <span>{{ t('settings.languages') }}</span>
                </a>
                <a
                    href="/admin/settings/taxonomies"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                        isTaxonomiesSection
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                >
                    <RectangleStackIcon class="h-5 w-5 shrink-0" />
                    <span>{{ t('settings.taxonomies') }}</span>
                </a>
            </nav>
        </aside>

        <!-- Контент страницы настроек -->
            <div class="min-w-0 flex-1">
            <SettingsOrganization v-if="isOrganization" />
            <SettingsGlobal v-else-if="isGlobal" />
            <SettingsLanguages v-else-if="isLanguagesList" />
            <SettingsLanguageForm v-else-if="isLanguagesCreate || isLanguagesEdit" />
            <TaxonomyForm v-else-if="isTaxonomiesCreate || isTaxonomiesEdit" />
            <Taxonomies v-else-if="isTaxonomiesList" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from '../../composables/useI18n';
import { BuildingOffice2Icon, Cog6ToothIcon, LanguageIcon, RectangleStackIcon } from '@heroicons/vue/24/outline';
import SettingsOrganization from './SettingsOrganization.vue';
import SettingsGlobal from './SettingsGlobal.vue';
import SettingsLanguages from './SettingsLanguages.vue';
import SettingsLanguageForm from './SettingsLanguageForm.vue';
import Taxonomies from './Taxonomies.vue';
import TaxonomyForm from './TaxonomyForm.vue';

const { t } = useI18n();

const isOrganization = computed(() => {
    if (typeof window === 'undefined') return true;
    return (
        window.location.pathname === '/admin/settings/organization' ||
        window.location.pathname === '/admin/settings'
    );
});

const isGlobal = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/settings/global';
});

const isLanguagesList = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/settings/languages';
});

const isLanguagesCreate = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/settings/languages/create';
});

const isLanguagesEdit = computed(() => {
    if (typeof window === 'undefined') return false;
    return /^\/admin\/settings\/languages\/\d+\/edit$/.test(window.location.pathname);
});

const isLanguages = computed(() => isLanguagesList.value || isLanguagesCreate.value || isLanguagesEdit.value);

const isTaxonomiesList = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/settings/taxonomies';
});

const isTaxonomiesCreate = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/settings/taxonomies/create';
});

const isTaxonomiesEdit = computed(() => {
    if (typeof window === 'undefined') return false;
    return /^\/admin\/settings\/taxonomies\/.+\/edit$/.test(window.location.pathname);
});

const isTaxonomiesSection = computed(
    () => isTaxonomiesList.value || isTaxonomiesCreate.value || isTaxonomiesEdit.value,
);
</script>

