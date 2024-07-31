<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="w-full sm:w-112 self-center">
            <h1 class="ml-4">Login</h1>
            <div class="m-4 p-4 border rounded-sm flex flex-col flex-rows-2 gap-y-4">
                <notification v-if="typeof loginFormNotification === 'object'" v-bind:notice="loginFormNotification"></notification>
                <form id="loginForm" class="flex flex-col flex-rows-3 gap-y-4">
                    <FormInput v-for="formField in loginForm" :key="formField.id"
                        :id="formField.id"
                        :identifier="formField.identifier" 
                        :type="formField.type" 
                        :elementLabel="formField.label" 
                        :errors="formField.errors" 
                        :value="formField.value" 
                        :callback="formField.callback"
                        :form="'loginForm'"
                    ></FormInput>

                    <Button bgColorClass="bg-blue-600" colorClass="text-white" callback="handleLogin">Login</Button>
                </form>
                <p>Dont have an account? <Link href="/register">Sign up now!</Link></p>
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../components/Notification.vue';
import Link from '../components/Link.vue';
import Button from '../components/Button.vue';
import eventBus from '../eventBus.js';
import FormInput from '../components/FormInput.vue';

export default {
    components: {
    Notification,
    Link,
    Button,
    eventBus,
    FormInput
},

    created() {
        // This is used with the button component. We pass the callback to the
        // button component which fires off an event and the callback gets executed
        // here.
        eventBus.on('callback', (eventData) => {
            if(eventData.args){
                this[eventData.callback](eventData.args);
            } else {
                this[eventData.callback]();
            }
        });
    },

    data() {
        return {
            loginFormNotification: '',
            errors: '',
            loginForm: {
                username: {
                    id: "username",
                    identifier: 'username',
                    label: 'Username',
                    type: 'text',
                    errors: [],
                    value: '',
                    callback: 'updateUserNameFieldValue'
                },
                password: {
                    id: 'password',
                    identifier: 'password',
                    label: 'Password',
                    type: 'password',
                    value: '',
                    errors: [],
                    callback: 'updatePasswordFieldValue'
                }
            }
        }
    },

    methods: {
        
        /**
         * Send a login request to the server and if successful, set the current
         * user in the store and redirect to the chores list page; otherwise,
         * display the error from the server.
         */
        handleLogin: function() {
            
            axios.post('/api/login', {
                username: this.loginForm.username.value,
                password: this.loginForm.password.value
            }).then((response) => {
                this.$store.commit('setCurrentUser', response.data.user);
                this.$router.push('chores-list');
            }).catch((error) => {
                if (error.response) {
                    this.loginFormNotification = {
                        message: error.response.data.message,
                        status: error.response.status
                    };

                    // this.errors = error.response.data.errors;
                    console.log(error.response);

                    for (const property in error.response.data.errors) {
                        if (property === 'message') {
                            continue;
                        }

                        this.loginForm[property].errors = error.response.data.errors[property];
                    }
                }
            });
        },


        /**
         * update the username fields value. This is called onChange of the
         * username field.
         * 
         * @param elValue - the value of the username field
         */
        updateUserNameFieldValue(elValue) {
            this.userNameField.value = elValue;
        },


        /**
         * update the password fields value. This is called onChange of the
         * password field.
         * 
         * @param elValue - the value of the password field
         */
        updatePasswordFieldValue(elValue) {
            this.passwordField.value = elValue;
        },

        resetFormsAndClearNotification(args) {
            this.resetFormErrors(args);
            this.clearNotification();
        },

        /**
         * 
         * @param fields array 
         */
        resetFormErrors(fields) {
            fields.forEach(field => {
                let fieldName = field.name;
                let formName = field.form;
                this[formName][fieldName].errors = [];
                this[formName][fieldName].value = field.value;
            });
        },

        clearNotification() {
            this.loginFormNotification = undefined;
        }
    },

    mounted() {
        axios.get('/sanctum/csrf-cookie');

        if (this.$store.getters.getUserAuthToken) {
            this.$router.push('chores-list');
        }
    }
}
</script>