<script setup>

import { Link } from '@inertiajs/vue3';
import { route } from '../../../vendor/tightenco/ziggy/src/js';
import { switchTheme } from '../theme';
import NavButton from '../Pages/Components/NavButton.vue';
import NavIcon from '../Pages/Components/NavIcon.vue';
import { ref, watchEffect } from 'vue';
import moment from 'moment-timezone';

var theme = ref("dark");

const props = defineProps({
    notificationNumber: Number,
    userId: Number,
})

let non = ref(props.notificationNumber);

const leaveChannel = () => {
    if (props.userId != -1) {
        Echo.leave('private-user.' + (props.userId));
    }
}

watchEffect(() => {
    non.value = props.notificationNumber;
    if (props.userId != -1) {
        Echo.private('user.' + (props.userId)).listen('NotificationNumChange', (event) => {
            non.value = event.non;
        })
    }
})

const handleThemeSwitch = () => {
    if (theme.value == 'light') {
        theme.value = 'dark';
    } else {
        theme.value = 'light';
    }
    switchTheme();
}


</script>

<template>

    <Head>
        <meta head-key="description" name="description" content="Layout of the page" />
    </Head>
    <div>
        <header class="bg-orange-500 dark:bg-orange-700  text-xl text-white w-full">
            <nav class="flex flex-wrap items-center justify-between p-2 space-x-5">
                <div class="flex flex-wrap space-x-6">
                    <NavButton routeName="home" pageComp="Home">Main page</NavButton>
                    <button @click="handleThemeSwitch"
                        class="m-2 text-center ring-1 ring-amber-800 w-11 p-2 rounded-full shadow-lg hover:bg-orange-800 hover:ring-amber-600">
                        <i v-if="theme == 'light'" class="fa-solid fa-moon"></i>
                        <i v-else class="fa-solid fa-sun"></i>
                    </button>
                </div>
                <div class="flex flex-wrap space-x-6">
                    <NavButton routeName="events" text="Wydarzenia" pageComp="Events">Events</NavButton>
                </div>
                <div v-if="$page.props.auth.user" class="flex flex-wrap justify-between">
                    <Link :href="route('dashboard')" as="button" preserve-scroll class="content-center">
                    <img class=" m-2 object-fill ring-1 ring-amber-800 size-11 rounded-full hover:bg-orange-800 shadow-lg hover:ring-amber-600"
                        :src="'/storage/' + $page.props.auth.user.profilepic" alt="Current user profile picture" />
                    </Link>
                    <NavIcon routeName="users">
                        <i class="fa-solid fa-users"></i>
                        <span v-show="non != 0" class="notificationsNumber">{{
                            non }}</span>
                    </NavIcon>
                    <NavButton @click="leaveChannel" routeName="logout" method="post">Log out</NavButton>
                </div>
                <div v-else class="flex flex-wrap">
                    <NavButton routeName="login" text="Login" pageComp="Login">Login</NavButton>
                </div>
            </nav>
        </header>
        <main class="p-4">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.notificationsNumber {
    position: relative;
    top: 10px;
    right: 5px;
    height: 1.30rem;
    width: 1.30rem;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    background-color: rgb(255, 89, 0);
    font-size: smaller;
    font-weight: bold;
    text-align: center;
    line-height: 1;
    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.2);
}
</style>
