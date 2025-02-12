<script setup>
import { ref } from 'vue';

const props = defineProps({
    tags: {
        type: Array,
        required: true,
        default: () => [],
    },
    label: {
        type: String,
    }
});

const selectedTags = ref([]);

const emit = defineEmits(['confirmTags']);

const handleTags = (tag) => {
    const index = selectedTags.value.indexOf(tag);
    if (index === -1) {
        selectedTags.value.push(tag);
    } else {
        selectedTags.value.splice(index, 1);
    }
    emit('confirmTags', selectedTags.value);
};
</script>

<template>
    <div class="mt-4">
        <label class="mb-1">{{ label }}</label>
        <div class="flex flex-wrap gap-2">
            <button v-for="tag in tags" :key="tag.id" @click="handleTags(tag)" type="button" :class="{
                'bg-orange-500 dark:bg-orange-700 text-white': selectedTags.includes(tag),
                'bg-white text-black': !selectedTags.includes(tag),
            }" class="px-4 py-2 rounded-md border border-gray-700  shadow-sm">
                {{ tag.name }}
            </button>
        </div>
    </div>
</template>

<style scoped>
button:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}
</style>
