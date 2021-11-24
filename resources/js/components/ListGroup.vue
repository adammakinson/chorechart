<template>
    <ul id="chores-list-group" class="list-group mt-4">
            <slot></slot>
    </ul>
</template>

<script>
import eventBus from "../eventBus.js";

export default { 
    created() {

    },

    data() {
        return {

        }
    }
}
</script>