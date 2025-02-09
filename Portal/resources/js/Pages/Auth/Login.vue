<script setup>

import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import ConfirmButton from '../Components/ConfirmButton.vue';
import CheckBox from '../Components/CheckBox.vue';
import NavLink from '../Components/NavLink.vue';
import SessionMessage from '../Components/SessionMessage.vue';

const form = useForm({
    email: null,
    password: null,
    remember: null,
})

const props = defineProps ({
    status: String
})

const submit = () => {
    form.post(route('login'), {
        onError: () => form.reset("password", "remember"),
    })
}
</script>

<template>

    <Head>
        <title> | Login</title>
        <meta head-key="description" name="description" content="User login page" />
    </Head>
    <PageFloatContainer>
        <div class="flex justify-center my-2">
            <form @submit.prevent="submit" class="grid flex-col w-1/3">
                <h1 class="justify-self-center mb-4 text-xl">Login</h1>
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" label="Email"></TextInput>
                <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" label="Password"> </TextInput>
                <SessionMessage :status="props.status"></SessionMessage>
                <div class="flex items-center justify-between mt-4">
                    <p>Need account? <NavLink routeName="register">Register here</NavLink> </p>
                    <NavLink routeName="password.request">Forgot password?</NavLink>
                </div>
                <div class="justify-self-center mt-4">
                    <CheckBox id="remember" v-model="form.remember">Remember me</CheckBox>
                </div>
                <div class="justify-self-center mt-4">
                    <ConfirmButton :disabled="form.processing">Login</ConfirmButton>
                </div>
            </form>
        </div>
    </PageFloatContainer>
</template>
