<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="w-112 self-center">
            <h1 class="ml-4">Register</h1>
            <div class="m-4 p-4 border rounded-sm">
                <notification v-if="typeof registerFormNotification === 'object'" v-bind:notice="registerFormNotification"></notification>
                <form id="loginForm" class="flex flex-col flex-rows-6 gap-y-4">
                    <FormInput 
                        :identifier="nameField.identifier" 
                        :type="nameField.type" 
                        :elementLabel="nameField.label" 
                        :errors="errors.name" 
                        :value="nameField.value" 
                        :callback="nameField.callback"
                    ></FormInput>
                    <FormInput 
                        :identifier="userNameField.identifier" 
                        :type="userNameField.type" 
                        :elementLabel="userNameField.label" 
                        :errors="errors.username" 
                        :value="userNameField.value" 
                        :callback="userNameField.callback"
                    ></FormInput>
                    <FormInput 
                        :identifier="emailField.identifier" 
                        :type="emailField.type" 
                        :elementLabel="emailField.label" 
                        :errors="errors.email" 
                        :value="emailField.value" 
                        :callback="emailField.callback"
                    ></FormInput>
                    <FormInput 
                        :identifier="passwordField.identifier" 
                        :type="passwordField.type" 
                        :elementLabel="passwordField.label" 
                        :errors="errors.password" 
                        :value="passwordField.value" 
                        :callback="passwordField.callback"
                    ></FormInput>
                    <FormInput 
                        :identifier="confirmPasswordField.identifier" 
                        :type="confirmPasswordField.type" 
                        :elementLabel="confirmPasswordField.label" 
                        :errors="errors.confirm_password"
                        :value="confirmPasswordField.value" 
                        :callback="confirmPasswordField.callback"
                    ></FormInput>

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
            registerFormNotification: '',
            errors: '',

            nameField: {
                identifier: 'name',
                label: 'Name',
                type: 'text',
                value: '',
                callback: 'updateNameFieldValue'
            },
            userNameField: {
                identifier: 'username',
                label: 'Username',
                type: 'text',
                value: '',
                callback: 'updateUserNameFieldValue'
            },
            emailField: {
                identifier: 'email',
                label: 'Email',
                type: 'text',
                value: '',
                callback: 'updateEmailFieldValue'
            },
            passwordField: {
                identifier: 'password',
                label: 'Password',
                type: 'password',
                value: '',
                callback: 'updatePasswordFieldValue'
            },
            confirmPasswordField: {
                identifier: 'passwordconfirm',
                label: 'Confirm Password',
                type: 'password',
                value: '',
                callback: 'updateConfirmPasswordFieldValue'
            },
        };
    },

    methods: {
        handleRegistration() {
            axios.post('/api/register', {
                name: this.nameField.value,
                username: this.userNameField.value,
                email: this.emailField.value,
                password: this.passwordField.value,
                confirm_password: this.confirmPasswordField.value
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
        },

        updateNameFieldValue(elValue) {
            this.nameField.value = elValue;
        },

        updateUserNameFieldValue(elValue) {
            this.userNameField.value = elValue;
        },
        
        updateEmailFieldValue(elValue) {
            this.emailField.value = elValue;
        },

        updatePasswordFieldValue(elValue) {
            this.passwordField.value = elValue;
        },

        updateConfirmPasswordFieldValue(elValue) {
            this.confirmPasswordField.value = elValue;
        }
    }
}
</script>