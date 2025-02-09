<script setup>

import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import ConfirmButton from '../Components/ConfirmButton.vue';

const props = defineProps ({
    token: String,
    email: String,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: null,
    password_confirmation: null,
})


const submit = () => {
    form.post(route('password.update'), {
        onError: () => form.reset("password", "password_confirmation"),
    })
}
</script>

<template>

    <Head>
        <title> | Enter new passowrd</title>
        <meta head-key="description" name="description" content="Strona rejestracji nowego użytkownika" />
    </Head>
    <PageFloatContainer>
        <div class="flex justify-center my-2 space-x-32">
            <form @submit.prevent="submit" class="grid w-1/3">
                <h1 class="justify-self-center mb-4 text-xl">Enter new password</h1>
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" label="Email"></TextInput>
                <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" label="Password">
                </TextInput>
                <TextInput name="Confirm password" type="password" v-model="form.password_confirmation" label="Confirm password"></TextInput>
                <div class="justify-self-center mt-4">
                    <ConfirmButton :disabled="form.processing">Confirm</ConfirmButton>
                </div>
            </form>
        </div>
    </PageFloatContainer>

</template>
