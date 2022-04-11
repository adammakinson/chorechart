<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="max-w-4xl self-center">
            <h1 class="ml-4">Login</h1>
            <div class="mx-4 mt-4 p-4 border rounded-sm">
                <notification v-if="typeof loginFormNotification === 'object'" v-bind:notice="loginFormNotification"></notification>
                <form id="loginForm">
                    <label for="username">Username: <span class="block mt-4" v-if="errors.username">{{errors.username[0]}}</span></label>
                    <input type="text" name="username" v-model="username" id="username" class="block border h-8">

                    <label for="password">Password: <span class="block mt-4" v-if="errors.password">{{errors.password[0]}}</span></label>
                    <input type="password" name="password" v-model="password" id="password" class="block border h-8">

                    <button class="border rounded-md px-4 py-2 my-4" v-on:click.prevent="handleLogin" type="submit">Login</button>
                </form>
                <p>Dont have an account? <a href="/register" class="underline text-blue-400">Sign up for one now.</a></p>
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../components/Notification.vue';

export default {
    components: {
        Notification
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
                console.log(response);
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