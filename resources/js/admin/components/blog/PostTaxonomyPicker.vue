<template>
    <div v-if="loading" class="rounded-lg border border-[#c3c4c7] bg-white p-4 text-center text-xs text-slate-500 shadow-sm">
        {{ loadingLabel }}
    </div>

    <template v-else>
        <div class="flex flex-col gap-4">
            <div
                v-for="tax in taxonomies"
                :key="tax.id"
                class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm"
            >
            <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-800">{{ tax.name }}</h3>
            </div>
            <div class="max-h-52 overflow-y-auto p-3">
                <p v-if="!tax.terms?.length" class="text-xs text-slate-500">
                    {{ emptyLabel }}
                </p>
                <ul v-else class="space-y-1.5">
                    <li v-for="term in tax.terms" :key="term.id">
                        <label
                            class="flex cursor-pointer items-start gap-2 rounded px-1 py-0.5 text-sm text-slate-700 hover:bg-slate-50"
                            :class="term.parent_id ? 'pl-4' : ''"
                        >
                            <input
                                type="checkbox"
                                class="mt-0.5 rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1]"
                                :value="term.id"
                                :checked="isSelected(term.id)"
                                @change="toggle(term.id, $event.target.checked)"
                            />
                            <span>
                                <span v-if="term.parent?.name" class="text-slate-400">{{ term.parent.name }} / </span>
                                {{ term.name }}
                            </span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        </div>
    </template>
</template>

<script setup>
const props = defineProps({
    taxonomies: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    loadingLabel: {
        type: String,
        default: 'Loading…',
    },
    emptyLabel: {
        type: String,
        default: 'No terms yet.',
    },
});

const emit = defineEmits(['update:modelValue']);

function isSelected(id) {
    return props.modelValue.includes(id);
}

function toggle(id, checked) {
    const next = new Set(props.modelValue);
    if (checked) {
        next.add(id);
    } else {
        next.delete(id);
    }
    emit('update:modelValue', [...next]);
}
</script>
