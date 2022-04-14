<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="w-112 self-center">
            <h1 class="ml-4">Login</h1>
            <div class="mx-4 mt-4 px-4 pb-4 border rounded-sm">
                <notification v-if="typeof loginFormNotification === 'object'" v-bind:notice="loginFormNotification"></notification>
                <form id="loginForm">
                    <label for="username" class="block mt-4">Username: <span v-if="errors.username" class="text-red-600">{{errors.username[0]}}</span></label>
                    <input type="text" name="username" v-model="username" id="username" class="block border h-8 w-full">

                    <label for="password" class="block mt-4">Password: <span v-if="errors.password" class="text-red-600">{{errors.password[0]}}</span></label>
                    <input type="password" name="password" v-model="password" id="password" class="block border h-8 w-full">

                    <button class="border rounded-md px-4 py-2 my-4" v-on:click.prevent="handleLogin" type="submit">Login</button>
                </form>
                <p>Dont have an account? <Link href="/register">Sign up now!</Link></p>
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../components/Notification.vue';
import Link from '../components/Link.vue';

export default {
    components: {
        Notification,
        Link
    },

    data() {
        return {
            username: '',
            password: '',
            loginFormNotification: '',
            errors: ''
        }
    },

    methods: {
        handleLogin: function() {
            
            // Send the POST request
            axios.post('/api/login', {
                username: this.username,
                password: this.password
            }).then((response) => {
                this.$store.commit('setCurrentUser', response.data.user);
                this.$router.push('chores-list');
            }).catch((error) => {
                if (error.response) {
                    this.loginFormNotification = {
                        message: error.response.data.message,
                        status: error.response.status
                    };

                    this.errors = error.response.data.errors;
                }
            });
        }
    },

    mounted() {
        axios.get('/sanctum/csrf-cookie');

        if(this.$store.getters.getUserAuthToken) {
            this.$router.push('chores-list'); // This may change to a different view...
        }
    }
}
</script>