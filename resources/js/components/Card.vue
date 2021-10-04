<template>
    <div class="card p-1 border rounded bg-light">
        <img :src="cardData.image" :alt="cardData.imgalt">
        <h4 class="text-dark">{{cardData.title}}</h4>
        <h5><b>cost: </b>{{cardData.cost}}</h5>
        <p class="text-dark">{{cardData.body}}</p>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    },

    components: {},

    props: [
        'cardData'
    ],

    methods: {}
}
</script>