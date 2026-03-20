<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <label class="mb-2 block text-sm font-semibold text-slate-800">{{ label }}</label>
        <p v-if="hint" class="mb-3 text-xs text-slate-500">{{ hint }}</p>

        <div
            v-if="suggestedIconKey && model !== suggestedIconKey"
            class="mb-3 flex flex-wrap items-center gap-2 rounded-lg border border-amber-200/80 bg-amber-50/60 px-3 py-2 text-xs text-amber-950"
        >
            <span class="text-amber-900/90">{{ t('admin.taxonomies.form_icon_suggested') }}</span>
            <span class="inline-flex items-center gap-1.5 rounded-md bg-white/90 px-2 py-1 font-medium text-slate-800 ring-1 ring-amber-200/60">
                <component :is="getTaxonomyIconComponent(suggestedIconKey)" class="h-4 w-4 text-slate-600" />
                {{ formatOptionLabel(suggestedIconKey) }}
            </span>
            <button
                type="button"
                class="font-semibold text-blue-700 hover:text-blue-900 hover:underline"
                @click="applySuggested"
            >
                {{ t('admin.taxonomies.form_icon_apply') }}
            </button>
        </div>

        <div ref="rootRef" class="relative">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-700"
                    aria-hidden="true"
                >
                    <component :is="previewIcon" class="h-6 w-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <button
                        type="button"
                        class="flex w-full items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-left text-sm shadow-sm transition-colors hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/25"
                        :aria-expanded="listOpen"
                        aria-haspopup="listbox"
                        :aria-label="label"
                        @click.stop="toggleList"
                    >
                        <component :is="previewIcon" class="h-5 w-5 shrink-0 text-slate-600" />
                        <span class="min-w-0 flex-1 truncate font-medium text-slate-900">{{ currentLabel }}</span>
                        <ChevronUpDownIcon class="h-5 w-5 shrink-0 text-slate-400" />
                    </button>

                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="translate-y-0.5 opacity-0"
                        enter-to-class="translate-y-0 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="translate-y-0 opacity-100"
                        leave-to-class="translate-y-0.5 opacity-0"
                    >
                        <ul
                            v-show="listOpen"
                            class="absolute z-[70] mt-1 max-h-64 w-full min-w-[18rem] overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-xl ring-1 ring-slate-900/5"
                            role="listbox"
                        >
                            <li role="presentation">
                                <button
                                    type="button"
                                    role="option"
                                    :aria-selected="!model"
                                    class="flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                                    :class="!model ? 'bg-slate-100 font-medium' : ''"
                                    @click="selectValue(null)"
                                >
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-400">—</span>
                                    {{ emptyLabel }}
                                </button>
                            </li>
                            <li v-for="opt in taxonomyIconOptions" :key="opt.key" role="presentation">
                                <button
                                    type="button"
                                    role="option"
                                    :aria-selected="model === opt.key"
                                    class="flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm hover:bg-slate-50"
                                    :class="model === opt.key ? 'bg-blue-50 font-medium text-blue-900' : 'text-slate-800'"
                                    @click="selectValue(opt.key)"
                                >
                                    <component :is="opt.component" class="h-5 w-5 shrink-0 text-slate-600" />
                                    <span class="min-w-0 flex-1 truncate">{{ formatOptionLabel(opt.key) }}</span>
                                    <span
                                        v-if="suggestedIconKey === opt.key"
                                        class="shrink-0 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-900"
                                    >
                                        ★
                                    </span>
                                </button>
                            </li>
                        </ul>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { ChevronUpDownIcon } from '@heroicons/vue/24/outline';
import { useI18n } from '../../composables/useI18n';
import { taxonomyIconOptions, getTaxonomyIconComponent } from '../../data/taxonomyIcons';

const { t } = useI18n();

const props = defineProps({
    label: { type: String, required: true },
    hint: { type: String, default: '' },
    emptyLabel: { type: String, default: '—' },
    /** Slug-based suggestion from `taxonomySuggestedIconBySlug` */
    suggestedIconKey: { type: String, default: null },
});

const model = defineModel({ type: String, default: null });

const rootRef = ref(null);
const listOpen = ref(false);

const previewIcon = computed(() => getTaxonomyIconComponent(model.value));

const currentLabel = computed(() =>
    model.value ? formatOptionLabel(model.value) : props.emptyLabel,
);

function formatOptionLabel(key) {
    return key
        .split('-')
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ');
}

function toggleList() {
    listOpen.value = !listOpen.value;
}

function selectValue(key) {
    model.value = key;
    listOpen.value = false;
}

function applySuggested() {
    if (props.suggestedIconKey) {
        model.value = props.suggestedIconKey;
    }
}

function onDocumentPointerDown(e) {
    if (!rootRef.value?.contains(e.target)) {
        listOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('pointerdown', onDocumentPointerDown, true);
});

onUnmounted(() => {
    document.removeEventListener('pointerdown', onDocumentPointerDown, true);
});
</script>
