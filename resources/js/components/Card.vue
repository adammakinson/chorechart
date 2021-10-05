<template>
    <div v-on:click="handleCardClick" class="card p-1 border rounded bg-light">
        <img :src="cardData.image" :alt="cardData.imgalt">
        <h4 class="text-dark">{{cardData.title}}</h4>
        <h5><b>cost: </b>{{cardData.cost}}</h5>
        <p class="text-dark">{{cardData.body}}</p>
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
            // console.log(this);

            eventBus.$emit('reward-card-click', this.cardData);
        }
    }
}
</script>