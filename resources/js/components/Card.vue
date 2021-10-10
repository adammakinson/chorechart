<template>
    <div v-on:click="handleCardClick" class="card p-1 border rounded bg-light">
        <img v-for="image in cardData.images" :key="image.id" :src="image.path+image.filename+image.file_extension" :alt="cardData.imgalt">
        <h4 class="text-dark">{{cardData.title}}</h4>
        <h5><b>cost: </b>{{cardData.cost}}</h5>
    </div>
</template>

<script>
import eventBus from '../eventBus';
export default {
    data() {
        return {
            
        };
    },

    components: {

    },

    props: [
        'cardData'
    ],

    methods: {
        handleCardClick() {
            eventBus.$emit('reward-card-click', this.cardData);
        }
    }
}
</script>