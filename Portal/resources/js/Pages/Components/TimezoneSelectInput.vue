<script setup>

import SessionMessage from './SessionMessage.vue';

const model = defineModel({
    option: "",
    required: true,
});

defineProps({
    name: {
        type: String,
        required: true,
    },
    message: {
        type: String,
    },
    label: {
        type: String
    },
})

const options = Intl.supportedValuesOf('timeZone');

</script>

<template>
    <div class="flex flex-col mt-4 min-w-40">
        <label :for="name" class="mb-1">{{ label }}</label>

        <select
            :class="(message) ? 'rounded-md ring-2 ring-red-500 pl-1 text-stone-700' : 'rounded-md ring-2 ring-stone-700 pl-1 text-stone-700'"
            :name="name" v-model="model">
            <option v-for="option in options" :value="option">{{ option }}</option>
        </select>

        <SessionMessage :status="message"></SessionMessage>
    </div>
</template>
