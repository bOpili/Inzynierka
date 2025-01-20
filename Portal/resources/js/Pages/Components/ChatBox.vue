<template>
    <div class="chatbox">
        <div class="messages">
            <div v-for="message in messages" :key="message.id" class="message">
                <strong>{{ message.sender.name }}</strong>: {{ message.message }}
            </div>
        </div>
        <input v-model="newMessage" type="text" placeholder="Type a message" @keyup.enter="sendMessage" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';


const props = defineProps({
    eventId: Number
})

const messages = ref([]);
const newMessage = ref('');
const eventId = props.eventId;
console.log(`event-chat.${eventId}`)

const fetchMessages = async () => {
    try{
        const response = await axios.get(`/event/${eventId}/messages`);
        messages.value = response.data;
    }catch (e) {
        console.log(e);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    await axios.post(`/event/${eventId}/messages`, {
        message: newMessage.value,
    });
    newMessage.value = '';
};

onMounted(() => {
    fetchMessages();

    window.Echo.join(`event-chat.${eventId}`)
        .listen('MessageSent', (e) => {
            console.log(e)
            messages.value.push(e);
        });
});
</script>

<style>
.chatbox {
    border: 1px solid #ccc;
    padding: 10px;
    width: 300px;
    height: 400px;
    overflow-y: scroll;
    color: black;
}

.messages {
    height: 90%;
    overflow-y: auto;
}

.message {
    margin-bottom: 10px;
}
</style>
