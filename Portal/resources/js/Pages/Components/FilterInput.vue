<script setup>

const model = defineModel({
    type: null,
    required: true,
});

defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        defaults: 'text',
    },
    label: {
        type: String
    },
})
</script>

<template>
    <div class="min-w-40">
        <label :for="name" class="mb-1 mr-2">{{ label }}:</label>
        <input
            :class="'rounded-md ring-2 ring-stone-700 pl-1 text-stone-700'"
            :name="name" :type="type" v-model="model">
    </div>
</template>
