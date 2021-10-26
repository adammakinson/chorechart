<template>
    <span v-on:click="handleIconClick"></span>
</template>
    
<script>
import eventBus from '../eventBus';

export default {
    /**
     * The thought is that the icon "component" will be generic and accept
     * the class that makes the icon from props as well as the event the
     * icon should emit when clicked.
     */

    methods: {
        handleIconClick(e) {
            // eventBus.$emit('reward-card-click', this.cardData);
            e.stopPropagation();
            eventBus.$emit(this.$attrs.iconevent, this);
            // console.log(this);
        }
    }
}
</script>