<script setup>
import { computed, ref } from 'vue';

import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import ConfirmButton from '../Components/ConfirmButton.vue';
import TextareaInput from '../Components/TextareaInput.vue';
import NumberInput from '../Components/NumberInput.vue';
import ImageInput from '../Components/ImageInput.vue';
import SelectInput from '../Components/SelectInput.vue';
import TagSelector from '../Components/TagSelector.vue';
import EventDateSelector from '../Components/EventDateSelector.vue';
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
    form.startDate = (new moment(form.startDate).utc()).format('YYYY-MM-DD HH:mm:ss');
    form.endDate = (new moment(form.endDate).utc()).format('YYYY-MM-DD HH:mm:ss');
    form.post(route('event.store'))
}

const handleTagSubmit = (selectedTags) => {
    form.tags = selectedTags;
}

const handleSelectedPeriod = (period) => {
    form.startDate = period.start;
    form.endDate = period.end;
};


defineProps({
    games: {
        type: Array
    },
    tags: {
        type: Array
    },
})

</script>

<template>

    <Head>
        <title> | Stwórz wydarzenie</title>
        <meta head-key="description" name="description" content="Strona rejestracji nowego użytkownika" />
    </Head>
    <PageFloatContainer>
        <div class="flex justify-center my-2 space-x-32 mx-14">
            <form @submit.prevent="submit" class="grid grid-cols-3 w-full gap-3">
                <h1 class="col-span-3 justify-self-center mb-4 text-xl">Stwórz wydarzenie</h1>
                <div v-for="error in $page.props.errors">
                    <p class="text-red-500">{{ error }}</p>
                </div>
                <TextInput class="col-span-3" name="Title" v-model="form.title" :message="form.errors.title"
                    label="Nazwa wydarzenia"></TextInput>
                <TextareaInput class="col-span-2" name="Opis" v-model="form.description"
                    :message="form.errors.description" label="Opis"></TextareaInput>
                <ImageInput @image="(e) => form.image = e"></ImageInput>
                <TagSelector label="Wybierz tagi" :tags="tags" @confirmTags="handleTagSubmit"></TagSelector>
                <SelectInput name="Game" :options="games" v-model="form.game_id" :message="form.errors.game_id"
                    label="W jakiej grze odbywa się wydarzenie"></SelectInput>
                <NumberInput name="Slots" v-model="form.slots" :message="form.errors.slots" label="Liczba miejsc"></NumberInput>
                <TextInput name="ServerIP" v-model="form.ip" :message="form.errors.ip" label="Server IP adress"></TextInput>
                <TextInput name="Password" v-model="form.password" :message="form.errors.password" label="Server password"></TextInput>
                <EventDateSelector class="col-span-3 p-2" @selectedEventTimeframe="handleSelectedPeriod"></EventDateSelector>
                <div class="justify-self-center mt-4 col-span-3">
                    <ConfirmButton :disabled="form.processing">Potwierdź</ConfirmButton>
                </div>
            </form>
        </div>
    </PageFloatContainer>
</template>
