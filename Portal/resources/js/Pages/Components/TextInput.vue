<script setup>

import SessionMessage from './SessionMessage.vue';
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
    message: {
        type: String,
    },
    label: {
        type: String
    },
})
</script>

<template>
    <div class="flex flex-col mt-4 min-w-40">
        <label :for="name" class="mb-1">{{ label }}</label>
        <input
            :class="(message) ? 'rounded-md ring-2 ring-red-500 pl-1 text-stone-700' : 'rounded-md ring-2 ring-stone-700 pl-1 text-stone-700'"
            :name="name" :type="type" v-model="model">
        <SessionMessage :status="message"></SessionMessage>
    </div>
</template>
