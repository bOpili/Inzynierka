<script setup>

import EventCard from '../Components/EventCard.vue';
import NavButton from '../Components/NavButton.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import HorizontalSeparator from '../Components/HorizontalSeparator.vue';
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import ConfirmButton from '../Components/ConfirmButton.vue';
import TagSelector from '../Components/TagSelector.vue';
import GameSelector from '../Components/GameSelector.vue';
const props = defineProps({
    events: Object,
    searchValue: String,
    tags: Object,
    games: Object,
    filteredTags: Object,
    filteredGames: Object,
})

const search = ref(props.searchValue);
var filtersVis = ref(false);

watch(search, debounce(
    (query) => router.get("/events", { search: query, games: props.filteredGames, tags: props.filteredTags }, { preserveState: true })
), 1000);

const formatLabel = (label) => {
    if (label.includes("Previous")) {
        return `<i class="fa-solid fa-caret-left"></i>`
    } else if (label.includes("Next")) {
        return `<i class="fa-solid fa-caret-right"></i>`;
    } else {
        return label;
    }
};

const changeFiltersVis = () => {
    filtersVis.value = !filtersVis.value
}
const form = useForm({
    tags: null,
    games: null,
    search: null,
})

const filter = () => {
    form.tags ? form.tags = form.tags.map((tag) => tag.id) : form.tags;
    form.games ? form.games = form.games.map((game) => game.id) : form.games;
    form.search = search;
    form.get(route('events'))
}

const handleTagSubmit = (selectedTags) => {
    form.tags = selectedTags;
}

const handleGameSubmit = (selectedGames) => {
    form.games = selectedGames;
};

const resetFilters = () => {
    form.tags = null;
    form.games = null;
    router.get("/events");
}

</script>

<template>

    <Head>
        <title> | Events</title>
        <meta head-key="description" name="description" content="Strona z wydarzeniami" />
    </Head>

    <PageFloatContainer>
        <div class="flex flex-row justify-between items-center">
            <p class="text-lg">Events page</p>
            <div class="space-x-2 flex items-center">
                <ConfirmButton v-if="!props.filteredGames && !props.filteredTags" @click.prevent="changeFiltersVis"><i
                        class="fa-solid fa-filter"></i></ConfirmButton>
                <ConfirmButton v-else @click.prevent="resetFilters"><i class="fa-solid fa-filter-circle-xmark"></i>
                </ConfirmButton>
                <input type="search" name="" id="" placeholder="Search"
                    class="rounded-md ring-2 ring-stone-700 pl-1 text-stone-700" v-model="search">
                <NavButton routeName="event.create">Create event</NavButton>
            </div>
        </div>
        <HorizontalSeparator></HorizontalSeparator>
        <div class="flex flex-row flex-wrap grow-0 gap-4 justify-around justify-items-center">
            <div v-if="events.data.length > 0" v-for="event in props.events.data" :key="event.id">
                <a :href="route('event.show', event.id)">
                    <EventCard :event="event" :userTimezone="$page.props.auth.user.timezone"></EventCard>
                </a>
            </div>
            <div v-else class="grid grid-cols-1 text-center">
                <p>No events found</p>
            </div>
        </div>
        <div v-if="events.data.length > 0" class="flex justify-items-center justify-center mt-4">
            <Link v-for="link in events.links" :key="link.label" :href="link.url ? link.url : ''" class="p-3"
                :class="{ 'dark:text-slate-800 text-slate-400': !link.url, 'text-orange-500 font-medium': link.active }">
            <span v-if="!link.url"></span>
            <span v-else v-html="formatLabel(link.label)"></span>
            </Link>
        </div>

        <div v-if="filtersVis" class="popup-backdrop">
            <PageFloatContainer class="w-96 min-w-56 z-0">
                <div class="popup-header">
                    <h3 class="popup-title">Filter events</h3>
                    <button @click="changeFiltersVis" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form @submit.prevent="filter">
                    <TagSelector label="Tags:" :tags="tags" @confirmTags="handleTagSubmit"></TagSelector>
                    <GameSelector label="Games:" :games="games" @confirmGames="handleGameSubmit"></GameSelector>
                    <div class="justify-self-center mt-4 col-span-3">
                        <ConfirmButton :disabled="form.processing">Confirm</ConfirmButton>
                    </div>
                </form>
            </PageFloatContainer>
        </div>

    </PageFloatContainer>


</template>

<style scoped>
.popup-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 500;
}

.popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}
</style>
