<script setup>
import { ref } from 'vue';

const props = defineProps({
    games: {
        type: Array,
        required: true,
        default: () => [],
    },
    label: {
        type: String,
    }
});

const selectedgames = ref([]);

const emit = defineEmits(['confirmGames']);

const handlegames = (game) => {
    const index = selectedgames.value.indexOf(game);
    if (index === -1) {
        selectedgames.value.push(game);
    } else {
        selectedgames.value.splice(index, 1);
    }
    emit('confirmGames', selectedgames.value);
};
</script>

<template>
    <div class="mt-4">
        <label class="mb-1">{{ label }}</label>
        <div class="flex flex-wrap gap-2">
            <button v-for="game in games" :key="game.id" @click="handlegames(game)" type="button" :class="{
                'bg-orange-500 dark:bg-orange-700 text-white': selectedgames.includes(game),
                'bg-white text-black': !selectedgames.includes(game),
            }" class="px-4 py-2 rounded-md border border-gray-700  shadow-sm">
                {{ game.title }}
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
