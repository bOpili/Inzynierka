<script setup>

import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import ConfirmButton from '../Components/ConfirmButton.vue';
import NavLink from '../Components/NavLink.vue';
import TimezoneSelectInput from '../Components/TimezoneSelectInput.vue';
import moment from 'moment-timezone';

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    timezone: moment.tz.guess(),
})

const submit = () => {
    console.log(form.timezone)

    form.post(route('register'), {
        onError: () => form.reset("password", "password_confirmation"),
    })
}
</script>

<template>

    <Head>
        <title> | Rejestracja</title>
        <meta head-key="description" name="description" content="Strona rejestracji nowego użytkownika" />
    </Head>
    <PageFloatContainer>
        <div class="flex justify-center my-2 space-x-32">
            <form @submit.prevent="submit" class="grid w-1/3">
                <h1 class="justify-self-center mb-4 text-xl">Stwórz konto</h1>
                <TextInput name="Username" v-model="form.name" :message="form.errors.name" label="Nazwa użytkownika"></TextInput>
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" label="Email"></TextInput>
                <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" label="Hasło">
                </TextInput>
                <TextInput name="Confirm password" type="password" v-model="form.password_confirmation" label="Potwierdź hasło"></TextInput>
                <TimezoneSelectInput name="Timezone" :options="timezones" v-model="form.timezone" :message="form.errors.timezone"
                    label="Select your timezone"></TimezoneSelectInput>
                <div class="mt-4">
                    <h1 class="mb-4">Masz już konto? <NavLink routeName="login">Zaloguj się</NavLink></h1>
                </div>
                <div class="justify-self-center mt-4">
                    <ConfirmButton :disabled="form.processing">Potwierdź</ConfirmButton>
                </div>
            </form>
        </div>
    </PageFloatContainer>

</template>
