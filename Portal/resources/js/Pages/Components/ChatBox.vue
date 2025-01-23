<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';


const props = defineProps({
    eventId: Number
})

const isChatOpen = ref(false);
const messages = ref([]);
const newMessage = ref('');
const eventId = props.eventId;
const chatContainer = ref(null);

const toggleChat = () => {
    isChatOpen.value = !isChatOpen.value;
    if (isChatOpen.value) {
        setTimeout(scrollToBottom, 0);
  }
};

const fetchMessages = async () => {
    try {
        const response = await axios.get(route('event.messages.fetch', eventId));
        messages.value = response.data;
    } catch (e) {
        console.log(e);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    await axios.post(route('event.messages.send', eventId), {
        message: newMessage.value,
    });
    newMessage.value = '';
    scrollToBottom();
};

const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTo({
      top: chatContainer.value.scrollHeight,
      behavior: 'smooth',
    });
  }
};

onMounted(() => {
    fetchMessages();
    scrollToBottom();
    window.Echo.join(`event-chat.${eventId}`)
        .listen('MessageSent', (e) => {
            console.log(e)
            messages.value.push(e);
        });
});


</script>

<template>
    <div class="fixed top-1/4 right-0 z-50 w-1/4">
        <!-- Chat Bubble -->
        <div id="chat-bubble" class="flex justify-end">
            <i @click="toggleChat" class="fa-solid fa-message m-2 ring-1 text-center place-content-center text-white bg-orange-500 dark:bg-orange-700 ring-amber-800 p-2 size-11 rounded-full shadow-lg hover:bg-orange-600 dark:hover:bg-orange-800 hover:ring-amber-400 dark:hover:ring-amber-600"></i>
        </div>

        <!-- Chat Box -->
        <div id="chat-box" class="backdrop-blur-lg " v-if="isChatOpen">
            <div class="flex-1 px-4 py-2 md:h-[20rem] h-[40rem] overflow-auto scrollbar-hide overscroll-contain" ref="chatContainer">
                <div v-for="message in messages" :key="message.id" class="mb-2 text-md">
                    <div v-if="$page.props.auth.user.id == message.sender.id" class="flex justify-end pt-2 items-center">
                        <p>{{ message.message }}</p>
                        <img class="object-fill ring-1 ring-amber-800 size-9 rounded-full shadow-lg ml-2"
                        :src="'/storage/' + message.sender.profilepic" alt="Current user profile picture" />
                    </div>
                    <div v-else class="flex pt-2 items-center" >
                        <img class="object-fill ring-1 ring-amber-800 size-9 rounded-full shadow-lg mr-2"
                        :src="'/storage/' + message.sender.profilepic" alt="Current user profile picture" />
                        <p>{{ message.message }}</p>
                    </div>
                </div>
            </div>
            <div class="chat-input">
                <input v-model="newMessage" type="text" placeholder="Type a message" @keyup.enter="sendMessage" class="rounded-lg text-black w-full"/>
            </div>
        </div>
    </div>
    <!-- <div class="chatbox">
        <div class="messages">
            <div v-for="message in messages" :key="message.id" class="message">
                <strong>{{ message.sender.name }}</strong>: {{ message.message }}
            </div>
        </div>
        <input v-model="newMessage" type="text" placeholder="Type a message" @keyup.enter="sendMessage" />
    </div> -->
</template>

<style>
/* Close button */
.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}
</style>
