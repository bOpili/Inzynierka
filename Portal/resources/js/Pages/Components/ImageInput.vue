<script setup>

import { ref } from 'vue';

const emit = defineEmits(['image'])

const preview = ref(null)

const imageSelected = (e) => {
    preview.value = URL.createObjectURL(e.target.files[0]);
    if (e.target.files[0].size > 3145728) {
        alert('Wybrane zdjęcie przekracza maksymalny dopuszczalny romiar 3 Mb!');
    }
    emit('image',e.target.files[0]);
}

</script>


<template>

    <div class="flex h-96 flex-col mt-4 min-w-40 ">
        <span>Picture (max size 3 Mb)</span>
        <label for="image" class="overflow-hidden ">
            <img class="object-contain object-center w-full h-full" :src="preview ?? '/storage/Events/DefaultEvent2.webp'">
        </label>
        <input @input="imageSelected" type="file" id="image" name="image" accept="image/*" hidden>
    </div>


</template>
