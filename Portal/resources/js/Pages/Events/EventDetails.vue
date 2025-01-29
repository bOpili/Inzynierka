<script setup>
import { useForm } from '@inertiajs/vue3';
import ConfirmButton from '../Components/ConfirmButton.vue';
import HorizontalSeparator from '../Components/HorizontalSeparator.vue';
import PageFloatContainer from '../Components/PageFloatContainer.vue';
import moment from 'moment-timezone';
import PopupMessage from '../Components/PopupMessage.vue';
import { ref } from 'vue';
import ChatBox from '../Components/ChatBox.vue';

var friendsListVis = ref(false);

const props = defineProps({
    event: Object,
    users: Array,
    pendingUsers: Array,
    friends: Array,
    userStatus: Number,
    joinMessage: String,
    auth: Object
})

const form = useForm({
    eventId: props.event.id,

})

const acceptLeaveForm = useForm({
    eventId: props.event.id,
    userId: null
})

const inviteForm = useForm({
    eventId: props.event.id,
    friendId: null
})

const kickUserForm = useForm({
    eventId: props.event.id,
    userId: null
})

const formatDate = (date) => {
    return moment.utc(date).tz(props.auth.user.timezone).format('DD.MM.YYYY');
}

const formatHour = (date) => {
    return moment.utc(date).tz(props.auth.user.timezone).format('HH:mm');
}

const submit = () => {
    form.post(route('event.join'), { preserveScroll: true })
}

const kickUser = (userId) => {
    kickUserForm.userId = userId;
    kickUserForm.post(route('event.kick'), {preserveScroll: true})
}

const deleteEvent = () => {
    form.delete(route('event.destroy', props.event), { preserveScroll: true })
}

const leaveEvent = (userId) => {
    acceptLeaveForm.userId = userId;
    acceptLeaveForm.post(route('event.leave'), { preserveScroll: true })
}


const changeFriendsListVisible = () => {
    friendsListVis.value = !friendsListVis.value
}

const accept = (userId) => {
    acceptLeaveForm.userId = userId;
    acceptLeaveForm.post(route('event.accept'), { preserveScroll: true })
}

const handleInvite = (friendId) => {
    inviteForm.friendId = friendId;
    inviteForm.post(route('event.invite'), { preserveScroll: true });
}

const resetMessage = () => {
    location.reload();
};

</script>

<template>

    <Head>
        <title> | {{ event.title }}</title>
        <meta head-key="description" name="description" content="Szczegóły wydarzenia" />
    </Head>

    <PageFloatContainer>
        <div class="grid grid-cols-2 space-y-4">
            <h1 class="self-end text-2xl font-bold">{{ event.title }}</h1>
            <div class="italic">
                <h1 class="justify-self-end">{{ formatDate(event.startDate) }} - {{
                formatDate(event.endDate) }}</h1>
                <h1 class="justify-self-end">{{ formatHour(event.startDate) }} - {{
                formatHour(event.endDate) }}</h1>
            </div>

            <HorizontalSeparator class="col-span-2"></HorizontalSeparator>
            <img v-if="event.image" :src="'/storage/' + event.image" alt="Event Image"
                class="object-cover rounded justify-self-center col-span-2 w-1/3" />
            <HorizontalSeparator class="col-span-2"></HorizontalSeparator>
            <p class="col-span-2">
                <span v-for="tag in event.tags" :key="tag"
                    class="inline-block px-2 py-1 rounded-full mr-2 text-sm text-white bg-orange-500  dark:bg-orange-700 border border-orange-700  dark:border-orange-800">
                    {{ tag.name }}
                </span>
            </p>
            <p class="col-span-2">
                {{ event.description }}
            </p>
            <p class="col-span-2">
                <strong>Gra:</strong> {{ event.game.title }}
            </p>

            <p :class="event.ip ? 'col-span-2' : ''">
                <strong>Sloty:</strong> {{ event.users_count + '/' + event.slots }}
            </p>
            <p v-if="event.ip" class="col-span-2"><strong>Server ip:</strong> {{ event.ip }}</p>
            <p v-if="event.password"><strong>Server password:</strong> {{ event.password }}</p>
            <div v-if="userStatus == 1" class="mt-4 justify-self-end">
                <form @submit.prevent="leaveEvent">
                    <ConfirmButton>Leave</ConfirmButton>
                </form>
            </div>
            <div v-else-if="userStatus == 0" class="mt-4 justify-self-end">
                <p>Host musi zaakceptować twoją prośbę o dołączenie</p>
            </div>
            <div v-else-if="userStatus == 2" class="grid grid-cols-2">
                <form @submit.prevent="deleteEvent">
                    <ConfirmButton>Delete this event</ConfirmButton>
                </form>
            </div>
            <div v-else class="mt-4 justify-self-end">
                <form @submit.prevent="submit">
                    <ConfirmButton>Dołącz</ConfirmButton>
                </form>
            </div>

            <HorizontalSeparator class="col-span-2"></HorizontalSeparator>
            <h1 :class="(userStatus == 2) ? 'col-span-1' : 'col-span-2'">Uczestnicy:</h1>
            <h1 v-if="userStatus == 2">Uczestnicy oczekujący:</h1>
            <table class="table-auto border-separate border-spacing-4">
                <tr v-for="(user, index) in props.users">
                    <td><img class="object-fill ring-1 ring-amber-800 size-11 rounded-full shadow-lg "
                            :src="'/storage/' + user.profilepic" alt="Current user profile picture" /></td>
                    <td>{{ user.name }}</td>
                    <td v-if="index == 0">Host</td>
                    <td v-else>Uczestnik</td>
                    <td v-if="index !=0 && userStatus == 2">
                        <form @submit.prevent="kickUser(user.id)">
                            <ConfirmButton><i class="fa-solid fa-user-minus"></i></ConfirmButton>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td class="justify-items-center" colspan="3">
                        <div v-if="userStatus > 0">
                            <ConfirmButton @click.prevent="changeFriendsListVisible">Invite Friends</ConfirmButton>
                        </div>
                    </td>
                </tr>
            </table>
            <table v-if="userStatus == 2" class="table-auto border-separate border-spacing-4">
                <tr v-for="(user, index) in props.pendingUsers">
                    <td><img class="object-fill ring-1 ring-amber-800 size-11 rounded-full shadow-lg "
                            :src="'/storage/' + user.profilepic" alt="Current user profile picture" /></td>
                    <td>{{ user.name }}</td>
                    <td>
                        <form @submit.prevent="accept(user.pivot.user_id)">
                            <ConfirmButton>Accept</ConfirmButton>
                        </form>
                    </td>
                </tr>
            </table>

            <PopupMessage @closed="resetMessage" v-if="$page.props.flash.message" :message="$page.props.flash.message">
            </PopupMessage>

            <div v-if="friendsListVis" class="popup-backdrop">
                <PageFloatContainer class="w-96 min-w-56 z-0">
                    <div class="popup-header">
                        <h3 class="popup-title">Friends List</h3>
                        <button @click="changeFriendsListVisible" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div>
                        <ul>
                            <p v-show="friends.length == 0">You have no friends :(</p>
                            <li v-for="friend in friends" :key="friend.id" class="user-item">
                                <span><img class="object-fill ring-1 ring-amber-800 size-11 rounded-full shadow-lg "
                                        :src="'/storage/' + friend.profilepic"
                                        alt="Current user profile picture" /></span>
                                <span>{{ friend.name }}</span>
                                <form @submit.prevent="handleInvite(friend.id)">
                                    <ConfirmButton>
                                        Invite
                                    </ConfirmButton>
                                </form>
                            </li>
                        </ul>
                        <p v-show="inviteForm.wasSuccessful" class="">Invite sent</p>
                        <p v-for="error in inviteForm.errors" class="text-red-500">{{ error }}</p>
                    </div>
                </PageFloatContainer>
            </div>
        </div>

    </PageFloatContainer>

    <ChatBox :eventId="props.event.id" v-if="userStatus > 0"></ChatBox>

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

.popup-content {
    background: rgb(249 115 22);
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

.user-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
</style>
