<template>
    <div>
        <!-- <user-status-bar>
            <h1 class="self-center">Manage Account</h1>
        </user-status-bar> -->
        <div class="sm:grid transition-all duration-500 ease-in-out" :class="[ mainMenuIsOpen ? 'grid-cols-menuexpanded' : 'grid-cols-menucollapsed' ]">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <div class="md:flex p-4">
                    <div class="card p-4">
                        <notification v-if="typeof userInfoNotification === 'object'" v-bind:notice="userInfoNotification"></notification>
                        <form id="editUserForm" :key="editUserFormKey" class="flex flex-col flex-rows-3 gap-y-4">
                            <FormInput v-for="formField in editUserForm" :key="formField.identifier"
                            :identifier="formField.identifier"
                            :type="formField.type"
                            :elementLabel="formField.label"
                            :errors="formField.errors"
                            :value="formField.value"
                            ></FormInput>
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" callback="updateUserInfo">Update user</Button>
                        </form>
                    </div>
                    <div class="card p-4">
                        <notification v-if="typeof userCredsNotification === 'object'" v-bind:notice="userCredsNotification"></notification>
                        <form id="changeUserCredentialsForm" class="flex flex-col flex-rows-3 gap-y-4">
                            <FormInput v-for="formField in updateCredentialsForm" :key="formField.identifier"
                            :identifier="formField.identifier"
                            :type="formField.type"
                            :elementLabel="formField.label"
                            :errors="formField.errors"
                            :value="formField.value"
                            ></FormInput>
                            <div>
                                <Button colorClass="text-white" bgColorClass="bg-blue-600" callback="updateCredentials">Update credentials</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
import Card from '../components/Card.vue';
import Button from '../components/Button.vue';
import Appmenu from '../components/AppMenu.vue';
import FormInput from '../components/FormInput.vue';
import Notification from '../components/Notification.vue';
import UserStatusBar from '../components/UserStatusBar.vue';

export default {
    components: { 
        Card,
        Appmenu,
        Notification,
        UserStatusBar,
        Button,
        FormInput
    },
  
    data() {
        return {
            userData: {},
            userInfoNotification: '',
            userCredsNotification: '',
            errors: {},
            mainMenuIsOpen: false,

            editUserForm: {
                    name: {
                        identifier: 'name',
                        label: 'Name',
                        type: 'text',
                        errors: '',
                        value: ''
                    },
                    username: {
                        identifier: 'username',
                        label: 'Username',
                        type: 'text',
                        errors: '',
                        value: ''
                    },
                    email: {
                        identifier: 'email',
                        label: 'Email',
                        type: 'text',
                        errors: '',
                        value: ''
                    }
                },
                editUserFormKey: 0,
                updateCredentialsForm: {
                    username: {
                        identifier: 'username',
                        label: 'Username',
                        type: 'text',
                        errors: [],
                        value: ''
                    },
                    password: {
                        identifier: 'password',
                        label: 'Password',
                        type: 'password',
                        errors: [],
                        value: ''
                    },
                    confirm_password: {
                        identifier: 'confirm_password',
                        label: 'Confirm password',
                        type: 'password',
                        errors: [],
                        value: ''
                    }
                },
                updateCredentialsFormKey: 0
        }
    },

    created() {
        this.userData = this.$store.getters.getUser;

        this.editUserForm.name.value = this.userData.name;
        this.editUserForm.username.value = this.userData.username;
        this.editUserForm.email.value = this.userData.email;

        eventBus.on('callback', (eventData) => {
    
            // 'this' is the VueComponent object
            if(this[eventData.callback]){
                if(eventData.args){
                    this[eventData.callback](eventData.args);
                } else {
                    this[eventData.callback]();
                }
            }
        });

        eventBus.on("mobileMainMenuIconClicked", () => {
            this.mainMenuIsOpen = !this.mainMenuIsOpen;
        });

        if (this.windowWidth < 640) {
            this.mainMenuIsOpen = false;
        } else {
            this.mainMenuIsOpen = true;
        }

        window.matchMedia("(orientation: portrait)").addEventListener("change", e => {
            const portrait = e.matches;

            if (this.windowWidth < 640) {
                this.mainMenuIsOpen = false;
            } else {
                this.mainMenuIsOpen = true;
            }
        });
    },

    methods: {
        updateUserInfo() {

            axios({
                method: 'put',
                url: '/api/user-info/' + this.userData.id,
                data: this.userData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                this.userInfoNotification = {
                    message: 'User information successfully updated!',
                    type: 'success'
                };
            }).catch((error) => {
                if (error.response) {
                    this.userInfoNotification = {
                        message: error.response.data.message,
                        status: error.response.status
                    };

                    for (const property in error.response.data.errors) {
                        if (property === 'message') {
                            continue;
                        }

                        this.editUserForm[property].errors = error.response.data.errors[property];
                    }
                }
            });
        },

        updateCredentials() {
            let userData = {};
            let validPasswords = false;
            let numErrors = 0;
            let inputPassword = document.querySelector('#password').value;
            let inputPasswordConfirm = document.querySelector('#confirm_password').value;

            userData.password = inputPassword;
            userData.confirm_password = inputPasswordConfirm;
            userData.username = this.userData.username;

            // TODO validate passwords on the fly with an onchange event.
            if (userData['password'].length < 8) {
                if(!Array.isArray(this.updateCredentialsForm.password.errors)) {
                    this.updateCredentialsForm.password.errors = [];
                }

                this.updateCredentialsForm.password.errors.push('You must have at least eight characters');

                numErrors += 1;
            }
            
            if (userData['confirm_password'].length < 8) {
                if(!Array.isArray(this.updateCredentialsForm.confirm_password.errors)) {
                    this.updateCredentialsForm.confirm_password.errors = [];
                }
                
                this.updateCredentialsForm.confirm_password.errors.push('You must have at least eight characters');

                numErrors += 1;
            }
            
            if (userData['password'] != userData['confirm_password']) {
                if(!Array.isArray(this.updateCredentialsForm.password.errors)) {
                    this.updateCredentialsForm.password.errors = [];
                }

                if(!Array.isArray(this.updateCredentialsForm.confirm_password.errors)) {
                    this.updateCredentialsForm.confirm_password.errors = [];
                }
                
                this.updateCredentialsForm.password.errors.push('Your passwords do not match');
                this.updateCredentialsForm.confirm_password.errors.push('Your passwords do not match');

                numErrors += 2;
            }

            if(numErrors == 0) {
                validPasswords = true;
            } else {
                this.userCredsNotification = {
                    message: 'The given data was invalid',
                    type: 'error'
                };
            }

            if(validPasswords) {

                axios({
                    method: 'put',
                    url: '/api/update-credentials/' + this.userData.id,
                    data: userData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    // rename
                    this.userCredsNotification = {
                        message: 'Password updates successfully!',
                        type: 'success'
                    };

                    document.querySelector('#password').value = '';
                    document.querySelector('#confirm_password').value = '';
                }).catch((error) => {
                    if (error.response) {
                        this.userCredsNotification = {
                            message: error.response.data.message,
                            status: error.response.status
                        };

                        for (const property in error.response.data.errors) {
                            if (property === 'message') {
                                continue;
                            }

                            this.updateCredentialsForm[property].errors = error.response.data.errors[property];
                        }
                    }
                });
            }
        }
    }
}
</script>