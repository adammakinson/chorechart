<template>
    <main class="w-screen max-w-full h-screen max-h-full">
        <router-view></router-view>
    </main>
</template>
<script>
/**
 * Note, in the template above, the <router-view> component handles routing
 * requests to the different pages of the app.
 */


/**
 * Import axios to handle REST calls
 */
import axios from 'axios';

export default {
    created() {

        this.checkAppHealth();
    },

    methods: {

        /**
         * This makes a call to the server side to check if the database config
         * is set up, the initial user is defined, the migrations and seeders
         * have been run.
         */
        checkAppHealth() {
            axios.get('/api/healthcheck')
            .then((response) => {
                console.log(response);
            });
        }
    }
}
</script>