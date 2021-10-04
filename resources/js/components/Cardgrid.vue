<template>
    <div class="cardgrid">
        <card v-for="cardData in cardCollectionData" :key="cardData.id" :cardData="cardData"></card>
    </div>
</template>

<script>
import Card from './Card.vue';

export default {
    data() {
        return {};
    },

    props: [
        'cardCollectionData'
    ],

    components: {
        Card
    },

    methods: {}
}
</script>