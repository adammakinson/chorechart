<template>
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
        handleCardClick() {
            eventBus.$emit('reward-card-click', this.cardData);
        },

        editCardClick(e) {
            e.stopPropagation();

            console.log("editing ");
            console.log(e);
        },

        deleteCardClick(e) {
            e.stopPropagation();

            console.log("deleting");
            console.log(e);
        }
    }
}
</script>