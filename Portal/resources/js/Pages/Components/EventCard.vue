<script setup>
import moment from "moment-timezone";
import { defineProps } from "vue";


const props = defineProps({
    event: Object,
    userTimezone: String,
});

console.log(props.userTimezone)

const formatDate = (date) => {
    return moment.utc(date).tz(props.userTimezone).format('DD.MM.YYYY HH:mm');
}


</script>

<style scoped>
.event-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.tags span {
    display: inline-block;
}
</style>


<template>
    <div class="event-card border-2 rounded-lg shadow-md p-4 border-orange-700  dark:border-orange-800 bg-zinc-100 dark:bg-stone-500 w-80 h-full">
        <div v-if="event.image" class="event-image mb-4">
            <img :src="'storage/' + event.image" alt="Event Image"
                class="w-full h-48 object-cover rounded-lg border border-black" />
        </div>
        <div class="grid grid-cols-1 event-details">
            <h2 class="text-2xl font-semibold mb-2">{{ event.title }}</h2>
            <h3 class="font-semibold mb-2">{{ event.data }}</h3>
            <div class="tags flex flex-wrap gap-2 mb-4">
                <span v-for="tag in event.tags" :key="tag"
                    class="text-sm text-white bg-orange-500  dark:bg-orange-700 border border-orange-700  dark:border-orange-800 px-2 py-1 rounded ">
                    {{ tag.name }}
                </span>
            </div>
            <div class="grid justify-between items-center ">
                <span><strong>Data:</strong> {{ formatDate(event.startDate)}}</span>
                <span><strong>Gra:</strong> {{ event.game.title }}</span>
                <span><strong>Sloty:</strong> {{ event.users_count + '/' + event.slots }}</span>
            </div>
        </div>
    </div>
</template>


