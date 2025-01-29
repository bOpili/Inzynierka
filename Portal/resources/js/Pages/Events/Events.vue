<script setup>

import EventCard from '../Components/EventCard.vue';
import NavButton from '../Components/NavButton.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import HorizontalSeparator from '../Components/HorizontalSeparator.vue';

const props = defineProps({
    events: Object,
})

</script>

<template>

    <Head>
        <title> | Wydarzenia</title>
        <meta head-key="description" name="description" content="Strona z wydarzeniami" />
    </Head>

    <PageFloatContainer>
        <div class="flex flex-row justify-between items-center">
            <p class="text-lg">Strona Wydarzenia</p>
            <NavButton routeName="event.create">Stwórz wydarzenie</NavButton>
        </div>
        <HorizontalSeparator></HorizontalSeparator>
        <div class="flex flex-row flex-wrap grow-0 gap-4 justify-around justify-items-center">
            <div v-if="events.data.length > 0" v-for="event in props.events.data" :key="event.id">
                <a :href="route('event.show', event.id)">
                    <EventCard :event="event" :userTimezone="$page.props.auth.user.timezone"></EventCard>
                </a>
            </div>
            <div v-else class="grid grid-cols-1 text-center">
                <p>Brak wydarzeń</p>
            </div>
        </div>


    </PageFloatContainer>


</template>
