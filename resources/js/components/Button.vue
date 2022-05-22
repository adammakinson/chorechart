<template>
    <button class="border rounded-md px-4 py-2"
            :class="[colorClass, bgColorClass, widthClass]"
            v-on:click.stop.prevent="handleClick" 
            type="submit">
            <slot></slot>
    </button>
</template>

<script>
/**
 *  The Button component takes a string callback property,
 *  which refers back to a function of the same name and
 *  also 'args', arguments passed into that callback function.
 *  TODO: refine args a little bit more to handle various types
 * 
 *  It also takes 'colorClass' and 'bgColorClass' attributes
 */
import eventBus from '../eventBus.js';

export default {
    components: {
        eventBus
    },

    props: [
        'args',
        'callback',
        'colorClass',
        'bgColorClass',
        'widthClass'
    ],

    methods: {
        handleClick() {
            eventBus.$emit('callback', this.callback, this.args);
        }
    }
}
</script>