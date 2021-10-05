<template>
    <div class="cardgrid">
        <card v-for="cardData in cardCollectionData" :key="cardData.id" :cardData="cardData"></card>
    </div>
</template>

<script>
import Card from './Card.vue';
import Modal from './Modal.vue';
import eventBus from '../eventBus';

export default {
    data() {
        return {};
    },

    props: [
        'cardCollectionData'
    ],

    components: {
        Card,
        Modal
    },

    methods: {
    },

    created() {
        eventBus.$on("reward-card-click", (cardData) => {
            console.log(cardData);
        });
    }
}
</script>