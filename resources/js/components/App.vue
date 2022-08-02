<template>
    <main class="w-screen max-w-full h-screen max-h-full">
        <router-view></router-view>
    </main>
</template>
<script>
import axios from 'axios';

export default {
    created() {
        window.addEventListener('resize', () => {
            this.$store.commit('setWindowWidth', window.innerWidth);
        });

        // I'm thinking this could be the place where we check for the existence
        // of the database schema we need for the app. The idea is a health check
        // for the application; do we have the things we need in order for the app
        // to run?

        // I need a controller for sure. Not sure about models. Don't need any
        // new migrations for this piece.

        // I'm thinking the class could be the centralized piece, accessible by
        // both console and http routes.
        this.checkAppHealth();
    },

    methods: {
        checkAppHealth() {
            axios.get('/api/healthcheck')
            .then((response) => {
                console.log(response);
            });
        }
    }
}
</script>