<template>
        <li class="list-group-item user-item" v-bind:data-userid="listItem.id" v-on:click="handleIndividualClick">{{listItem.name}}</li>
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
        handleIndividualClick(event) {
            let individual = event.target.closest('.list-group-item');

            individual.classList.toggle('active');
        },
    }
}
</script>