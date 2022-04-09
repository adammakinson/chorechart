<template>
    <span v-on:click="handleIconClick"></span>
</template>
    
<script>
import eventBus from '../eventBus';

export default {
    
    methods: {
        handleIconClick(e) {
            e.stopPropagation();
            console.log(this);
            eventBus.$emit(this.$attrs.iconevent, this);
        }
    }
}
</script>

<style scoped>
    span {
        display: inline-block;
        padding: 5px;
    }
</style>