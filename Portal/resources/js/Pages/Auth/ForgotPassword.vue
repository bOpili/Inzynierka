<script setup>

import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import ConfirmButton from '../Components/ConfirmButton.vue';
import SessionMessage from '../Components/SessionMessage.vue';

const form = useForm({
    email: null,
})

const props = defineProps({
    status: String,
})

const submit = () => {
    form.post(route("password.email"))
}
</script>

<template>

<Head>
    <title> | Zresetuj hasło</title>
</Head>

<PageFloatContainer>
        <div class="flex justify-center my-2">
            <form @submit.prevent="submit" class="grid flex-col w-1/3">
                <h1 class="justify-self-center mb-4 text-xl text-center">Enter email Your email address, so we can send you password reset link</h1>
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" label="Email"></TextInput>
                <SessionMessage :status="props.status"></SessionMessage>
                <div class="justify-self-center mt-4">
                    <ConfirmButton :disabled="form.processing">Send</ConfirmButton>
                </div>
            </form>
        </div>
    </PageFloatContainer>

</template>
