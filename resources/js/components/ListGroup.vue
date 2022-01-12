<template>
    <ul v-bind:id="listId" class="list-group mt-4">
            <slot></slot>
    </ul>
</template>

<script>
import eventBus from "../eventBus.js";

export default {
    props: [
        'listId'
    ],

    created() {

    },

    data() {
        return {

        }
    }
}
</script>