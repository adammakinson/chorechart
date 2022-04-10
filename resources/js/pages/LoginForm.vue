<template>
    <div>
        <div>
            <div>
                <h1>Chorechart Login</h1>
                <div>
                    <div>
                        <notification v-if="typeof loginFormNotification === 'object'" v-bind:notice="loginFormNotification"></notification>
                    </div>
                    <div>
                        <form id="loginForm">
                            <div>
                                <label for="username">Username: <span class="text-danger text-opacity-50" v-if="errors.username">{{errors.username[0]}}</span></label>
                                <input type="text" name="username" v-model="username" id="username" class="form-control">
                            </div>
                            <div>
                                <label for="password">Password: <span class="text-danger text-opacity-50" v-if="errors.password">{{errors.password[0]}}</span></label>
                                <input type="password" name="password" v-model="password" id="password" class="form-control">
                            </div>
                            <!-- <div>
                                <input type="checkbox" id="remember_me" name="remember">
                                <label for="remember_me">Remember me</label>
                            </div> -->
                            <div>
                                <button class="btn btn-primary" v-on:click.prevent="handleLogin" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <p>Dont have an account? <a href="/register">Sign up for one now.</a></p>
                    </div>
                </div>
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