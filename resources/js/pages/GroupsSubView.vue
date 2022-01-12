<template>
        <li class="list-group-item" v-on:click="handleGroupClick">{{listItem.title}}</li>
</template>

<script>
export default {
    props: [
        'listItem'
    ],

    methods: {
        /**
         * Adds or removes the active class from the clicked list group item element
         */
        handleGroupClick(event) {
            let group = event.target.closest('.list-group-item');

            group.classList.toggle('active');
        },
    }
}
</script>