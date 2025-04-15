<script setup>

import { router, useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import ConfirmButton from '../Components/ConfirmButton.vue';
import TextareaInput from '../Components/TextareaInput.vue';
import NumberInput from '../Components/NumberInput.vue';
import ImageInput from '../Components/ImageInput.vue';
import SelectInput from '../Components/SelectInput.vue';
import TagSelector from '../Components/TagSelector.vue';
import EventDateSelector from '../Components/EventDateSelector.vue';
import { onBeforeUnmount, onMounted, onUnmounted, ref } from 'vue';
import moment from 'moment-timezone';

const form = useForm({
    title: null,
    description: null,
    tags: null,
    slots: null,
    game_id: null,
    image: null,
    startDate: null,
    endDate: null,
    ip: null,
    password: null,
})

const submit = () => {
    form.startDate = moment.tz(form.startDate, props.timezone).utc().format('YYYY-MM-DD HH:mm:ss');
    form.endDate = moment.tz(form.endDate, props.timezone).utc().format('YYYY-MM-DD HH:mm:ss');
    form.post(route('event.store'))
}

const handleTagSubmit = (selectedTags) => {
    form.tags = selectedTags;
}

const handleSelectedPeriod = (period) => {
    form.startDate = period.start;
    form.endDate = period.end;
};


const props = defineProps({
    games: {
        type: Array
    },
    tags: {
        type: Array
    },
    timezone: {
        type: String
    }
})


</script>

<template>

    <Head>
        <title> | Create event</title>
        <meta head-key="description" name="description" content="Strona rejestracji nowego użytkownika" />
    </Head>
    <PageFloatContainer>
        <div class="flex justify-center my-2 space-x-32 mx-14">
            <form @submit.prevent="submit" class="grid grid-cols-3 w-full gap-3">
                <h1 class="col-span-3 justify-self-center mb-4 text-xl">Create event</h1>
                <div v-for="error in $page.props.errors">
                    <p class="text-red-500">{{ error }}</p>
                </div>
                <TextInput class="col-span-3" name="Title" v-model="form.title" :message="form.errors.title"
                    label="Title"></TextInput>
                <TextareaInput class="col-span-2" name="Description" v-model="form.description"
                    :message="form.errors.description" label="Description"></TextareaInput>
                <ImageInput @image="(e) => form.image = e"></ImageInput>
                <TagSelector label="Select tags" :tags="tags" @confirmTags="handleTagSubmit"></TagSelector>
                <SelectInput name="Game" :options="games" v-model="form.game_id" :message="form.errors.game_id"
                    label="Select game where event takes place"></SelectInput>
                <NumberInput name="Slots" v-model="form.slots" :message="form.errors.slots" label="Slots"></NumberInput>
                <TextInput name="ServerIP" v-model="form.ip" :message="form.errors.ip" label="Server IP adress">
                </TextInput>
                <TextInput name="Password" v-model="form.password" :message="form.errors.password"
                    label="Server password"></TextInput>
                <EventDateSelector class="col-span-3 p-2" @selectedEventTimeframe="handleSelectedPeriod">
                </EventDateSelector>
                <div class="justify-self-center mt-4 col-span-3">
                    <ConfirmButton :disabled="form.processing">Create</ConfirmButton>
                </div>
            </form>
        </div>
    </PageFloatContainer>
</template>
