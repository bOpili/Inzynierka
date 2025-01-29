<template>
    <div v-if="isVisible" class="popup-overlay">
        <PageFloatContainer class="popup-content">
            <p>{{ message }}</p>
            <ConfirmButton class="mt-2" @click="closePopup">Ok</ConfirmButton>
        </PageFloatContainer>
    </div>
</template>

<script setup>
import { ref, defineProps } from "vue";
import ConfirmButton from "./ConfirmButton.vue";
import PageFloatContainer from "./PageFloatContainer.vue";

defineProps({
    message: {
        type: String,
        required: true,
    },
});
const emit = defineEmits(['closed'])

const isVisible = ref(true);

const closePopup = () => {
    isVisible.value = false;
    emit('closed')
};

</script>

<style scoped>
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup-content {
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.close-button {
    margin-top: 15px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
</style>
