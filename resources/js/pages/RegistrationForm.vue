<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="w-112 self-center">
            <h1 class="ml-4">Register</h1>
            <div class="mx-4 mt-4 px-4 pb-4 border rounded-sm">
                <notification v-if="typeof registerFormNotification === 'object'" v-bind:notice="registerFormNotification"></notification>
                <form id="loginForm">
                    <label for="name" class="block mt-4">Name: <span class="text-red-600" v-if="errors.name">{{errors.name[0]}}</span></label>
                    <input type="text" name="name" v-model="name" id="name" class="block border h-8 w-full">
                    
                    <label for="username" class="block mt-4">Username: <span class="text-red-600" v-if="errors.username">{{errors.username[0]}}</span></label>
                    <input type="text" name="username" v-model="username" id="username" class="block border h-8 w-full" required>

                    <label for="email" class="block mt-4">Email: <span class="text-red-600" v-if="errors.email">{{errors.email[0]}}</span></label>
                    <input type="text" name="email" v-model="email" id="email" class="block border h-8 w-full">
                    
                    <label for="password" class="block mt-4">Password: <span class="text-red-600" v-if="errors.password">{{errors.password[0]}}</span></label>
                    <input type="password" name="password" v-model="password" id="password" class="block border h-8 w-full" required>

                    <label for="passwordconfirm" class="block mt-4">Confirm password: <span class="text-red-600" v-if="errors.confirm_password">{{errors.confirm_password[0]}}</span></label>
                    <input type="password" name="passwordconfirm" v-model="passwordconfirm" id="passwordconfirm" class="block border h-8 w-full" required>

                    <div class="flex justify-between">
                        <Button colorClass="text-white" bgColorClass="bg-blue-600" callback="handleRegistration">Register</Button>
                        <p class="self-center">Already have an account? <Link href="/login">Log in</Link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../components/Notification.vue';
import Link from '../components/Link.vue';
import Button from '../components/Button.vue';
import eventBus from '../eventBus.js';

export default {
    components: {
        Notification,
        Link,
        Button,
        eventBus
    },

    created() {
        eventBus.$on('callback', (callback, args) => {
            var fn = window[callback];
    
            // 'this' is the VueComponent object
            if(args){
                this[callback](args);
            } else {
                this[callback]();
            }
        });
    },

    data() {
        return {
            name: '',
            username: '',
            email: '',
            password: '',
            passwordconfirm: '',
            registerFormNotification: '',
            errors: ''
        };
    },

    methods: {
        handleRegistration() {
            axios.post('/api/register', {
                name: this.name,
                username: this.username,
                email: this.email,
                password: this.password,
                confirm_password: this.passwordconfirm
            }).then((response) => {
                this.registerFormNotification = {
                    message: 'Success! Redirecting to Login screen...',
                    type: 'success'
                };

                setTimeout(() => {
                    this.registerFormNotification = '';
                    this.$router.push('login');
                }, 1000);
            }).catch((error) => {
                if (error.response) {
                    this.registerFormNotification = {
                        message: error.response.data.message,
                        status: error.response.status
                    };

                    this.errors = error.response.data.errors;
                }
            });
        }
    }
}
</script>