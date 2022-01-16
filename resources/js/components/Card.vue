<template>
    <div v-on:click="handleCardClick" class="card p-1 border rounded bg-light justify-content-between" v-bind:data-itemId="cardData.id" v-bind:data-eventToFire="cardData.eventToFire">
        <img v-for="image in cardData.images" :key="image.id" :src="image.path+image.filename" :alt="cardData.imgalt">
        <h4 class="text-dark">{{cardData.title}}</h4>
        <h5><b>cost: </b>{{cardData.cost}}</h5>
        <div v-if="cardData.actionIcons" class="flex-column m-3">
            <icon v-for="actionicon in cardData.actionIcons" :key="actionicon.event" 
                v-bind:class="actionicon.class" 
                v-bind:iconevent="actionicon.event" 
                v-bind:data-itemId="cardData.id">
            </icon>
        </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
import icon from '../components/Icon';
export default {

    components: {
        icon
    },

    props: [
        'cardData'
    ],

    methods: {
        handleCardClick(e) {
            let eventToFire = e.target.closest('.card').getAttribute('data-eventToFire');
            eventBus.$emit(eventToFire, this.cardData);
        }
    }
}
</script>