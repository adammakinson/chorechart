<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="w-full sm:w-112 self-center">
            <h1 class="ml-4">Register</h1>
            <div class="flex flex-col flex-rows-2 gap-y-4 m-4 p-4 border rounded-sm">
                <notification v-if="typeof registerFormNotification === 'object'" v-bind:notice="registerFormNotification"></notification>
                <form id="loginForm" class="flex flex-col flex-rows-6 gap-y-4">

                    <FormInput v-for="formField in registerFormData" :key="formField.identifier"
                        :identifier="formField.identifier"
                        :type="formField.type"
                        :elementLabel="formField.label"
                        :errors="formField.errors"
                        :value="formField.value"
                        :callback="formField.callback"
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
    
            // 'this' is the VueComponent object
            if(this[callback]){
                if(args){
                    this[callback](args);
                } else {
                    this[callback]();
                }
            }
        });
    },

    data() {
        return {
            registerFormNotification: '',
            registerFormData: {
                name: {
                    identifier: 'name',
                    label: 'Name',
                    type: 'text',
                    errors: '',
                    value: '',
                    callback: 'updateNameFieldValue'
                },
                username: {
                    identifier: 'username',
                    label: 'Username',
                    type: 'text',
                    errors: '',
                    value: '',
                    callback: 'updateUserNameFieldValue'
                },
                email: {
                    identifier: 'email',
                    label: 'Email',
                    type: 'text',
                    errors: '',
                    value: '',
                    callback: 'updateEmailFieldValue'
                }, 
                password: {
                    identifier: 'password',
                    label: 'Password',
                    type: 'password',
                    errors: '',
                    value: '',
                    callback: 'updatePasswordFieldValue'
                },
                confirm_password: {
                    identifier: 'passwordconfirm',
                    label: 'Confirm Password',
                    type: 'password',
                    errors: '',
                    value: '',
                    callback: 'updateConfirmPasswordFieldValue'
                }
            }
        };
    },

    methods: {
        handleRegistration() {
            axios.post('/api/register', {
                name: this.registerFormData.name.value,
                username: this.registerFormData.username.value,
                email: this.registerFormData.email.value,
                password: this.registerFormData.password.value,
                confirm_password: this.registerFormData.confirm_password.value
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

                    for (const property in error.response.data.errors) {
                        if (property === 'message') {
                            continue;
                        }

                        this.registerFormData[property].errors = error.response.data.errors[property];
                    }
                }
            });
        },

        updateNameFieldValue(elValue) {
            this.registerFormData.name.value = elValue;
        },

        updateUserNameFieldValue(elValue) {
            this.registerFormData.username.value = elValue;
        },
        
        updateEmailFieldValue(elValue) {
            this.registerFormData.email.value = elValue;
        },

        updatePasswordFieldValue(elValue) {
            this.registerFormData.password.value = elValue;
        },

        updateConfirmPasswordFieldValue(elValue) {
            this.registerFormData.confirm_password.value = elValue;
        }
    }
}
</script>