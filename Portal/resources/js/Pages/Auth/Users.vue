<script setup>

import { useForm } from '@inertiajs/vue3';
import ConfirmButton from '../Components/ConfirmButton.vue';
import FilterInput from '../Components/FilterInput.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import HorizontalSeparator from '../Components/HorizontalSeparator.vue';
import PopupMessage from '../Components/PopupMessage.vue';

const form = useForm({
    Name: ""
})

const requestSend = useForm({
    id: null
})

const friendRemove = useForm({
    id: null
})

const inviteForm = useForm({
    id: null
})

const props = defineProps({
    users: Array,
    requests: Array,
    friends: Array,
    invites: Array,
})

const submit = () => {
    form.post(route('users.findUser'))
}

const sendRequest = (id) => {
    requestSend.post(route('friend.sendRequest', id));
}
const acceptRequest = (id) => {
    requestSend.post(route('friend.accept', id));
}
const rejectRequest = (id) => {
    requestSend.post(route('friend.reject', id));
}

const acceptInvite = (id) => {
    inviteForm.id = id;
    inviteForm.post(route('event.invite.accept'));
}
const rejectInvite = (id) => {
    inviteForm.id = id;
    inviteForm.post(route('event.invite.reject'));
}

const removeFriend = (id) => {
    inviteForm.id = id;
    friendRemove.post(route('friend.remove', id));
}



const resetMessage = () => {
    location.reload();
};

</script>

<template>

    <Head>
        <title> | Lista użytkowników</title>
    </Head>

    <PageFloatContainer>
        <div class="flex flex-row justify-between items-center">
            <p class="text-lg">Friends list</p>
            <form @submit.prevent="submit" class="flex flex-row space-x-2">
                <FilterInput label="Find user by name" type="text" v-model="form.Name" name="Username">
                </FilterInput>
                <ConfirmButton>Search</ConfirmButton>
            </form>
        </div>
        <HorizontalSeparator v-if="props.requests"></HorizontalSeparator>
        <table class="table-auto border-separate border-spacing-4">
            <tr v-for="request in props.requests">
                <td><img class="object-fill ring-1 ring-amber-800 size-11 rounded-full shadow-lg "
                        :src="'storage/' + request.sender.profilepic" alt="Request sender profile picture" /></td>
                <td>{{ request.sender.name }} wants to be yout friend</td>
                <td>
                    <ConfirmButton @click="acceptRequest(request.id)"><i class="fa-solid fa-check"></i></ConfirmButton>
                </td>
                <td>
                    <ConfirmButton @click="rejectRequest(request.id)"><i class="fa-solid fa-xmark"></i></ConfirmButton>
                </td>
            </tr>
            <tr v-for="invite in props.invites">
                <td><img class="object-fill ring-1 ring-amber-800 size-11 rounded-full shadow-lg "
                        :src="'storage/' + invite.sender.profilepic" alt="Invite sender profile picture" /></td>
                <td>{{ invite.sender.name }} invited you to participate in <b><i>{{ invite.event.title }}</i></b></td>
                <td>
                    <ConfirmButton @click="acceptInvite(invite.id)"><i class="fa-solid fa-check"></i></ConfirmButton>
                </td>
                <td>
                    <ConfirmButton @click="rejectInvite(invite.id)"><i class="fa-solid fa-xmark"></i></ConfirmButton>
                </td>
            </tr>
        </table>
        <HorizontalSeparator></HorizontalSeparator>
        <table class="table-auto border-separate border-spacing-4">
            <tr v-for="user in props.users">
                <td><img class="object-fill ring-1 ring-amber-800 size-11 rounded-full shadow-lg "
                        :src="'storage/' + user.profilepic" alt="Current user profile picture" /></td>
                <td>{{ user.name }}</td>
                <td v-if="!props.friends.some(e => e.id === user.id) && user.id != $page.props.auth.user.id"><button
                        @click.prevent="sendRequest(user.id)"
                        class="text-white bg-orange-500 hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-800 py-2 px-4 border border-orange-700  dark:border-orange-800 rounded">
                        <i class="fa-solid fa-user-plus"></i>
                    </button></td>
                <td v-else-if="props.friends.some(e => e.id === user.id) && user.id != $page.props.auth.user.id"><button
                        @click.prevent="removeFriend(user.id)"
                        class="text-white bg-orange-500 hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-800 py-2 px-4 border border-orange-700  dark:border-orange-800 rounded">
                        <i class="fa-solid fa-user-xmark"></i>
                    </button></td>
                <td v-else-if="user.id == $page.props.auth.user.id">It's you</td>
            </tr>
        </table>
    </PageFloatContainer>
    <PopupMessage @closed="resetMessage" v-if="$page.props.flash.message" :message="$page.props.flash.message">
    </PopupMessage>

</template>
