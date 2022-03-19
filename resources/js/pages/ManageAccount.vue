<template>
    <div class="container-fluid">
        <appmenu></appmenu>
        <h1>Manage users</h1>
        <div class="container-fluid">
            <div class="row justify-content-center mb-4">
                <div style="width:40em;">
                    <h1>User information</h1>
                </div>
            </div>
            <div class="row justify-content-center mb-4">
                <div class="card" style="width:40em;">
                    <div class="card-header">
                        <notification v-if="typeof userInfoNotification === 'object'" v-bind:notice="userInfoNotification"></notification>
                    </div>
                    <div class="card-body">
                        <form id="editUserForm">
                            <div class="form-group mb-2">
                                <label for="name">Name: <span class="text-danger text-opacity-50" v-if="errors.name">{{errors.name[0]}}</span></label>
                                <input id="name" name="name" class="form-control" type="text" v-model="userData.name">
                            </div>
                            <div class="form-group mb-2">
                                <label for="username">Username: <span class="text-danger text-opacity-50" v-if="errors.username">{{errors.username[0]}}</span></label>
                                <input id="username" name="username" class="form-control" type="text" v-model="userData.username">
                            </div>
                            <div class="form-group mb-2">
                                <label for="email">Email: <span class="text-danger text-opacity-50" v-if="errors.email">{{errors.email[0]}}</span></label>
                                <input id="email" name="email" class="form-control" type="text" v-model="userData.email">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" v-on:click.prevent="updateUserInfo">Update user</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-4">
                <div style="width:40em;">
                    <h1>Credentials</h1>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="card" style="width:40em;">
                    <div class="card-header">
                        <notification v-if="typeof userCredsNotification === 'object'" v-bind:notice="userCredsNotification"></notification>
                    </div>
                    <div class="card-body">
                        <form id="changeUserCredentialsForm">
                            <div class="form-group mb-2">
                                <label for="password">Password: <span class="text-danger text-opacity-50" v-if="errors.password">{{errors.password[0]}}</span></label>
                                <input id="password" name="password" class="form-control" type="password" value="">
                            </div>
                            <div class="form-group mb-2">
                                <label for="confirm_password">Confirm password: <span class="text-danger text-opacity-50" v-if="errors.confirm_password">{{errors.confirm_password[0]}}</span></label>
                                <input id="confirm_password" name="confirm_password" class="form-control" type="password" value="">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" v-on:click.prevent="updateCredentials">Update credentials</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Card from '../components/Card.vue';
import Appmenu from '../components/AppMenu.vue';
import Notification from '../components/Notification.vue';

export default {
    components: { 
        Card,
        Appmenu,
        Notification
    },
  
    data() {
        return {
            userData: {},
            userInfoNotification: '',
            userCredsNotification: '',
            errors: {}
        }
    },

    created() {
      this.userData = this.$store.getters.getUser;
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
                // hrm... it really isn't an errors notification, really
                // its a general one. need to fix that naming convention...

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

                    this.errors = error.response.data.errors;
                }
            });
        },

        updateCredentials() {
            let userData = {};
            let validPasswords = false;
            let inputPassword = document.querySelector('#password').value;
            let inputPasswordConfirm = document.querySelector('#confirm_password').value;

            userData.password = inputPassword;
            userData.confirm_password = inputPasswordConfirm;
            userData.username = this.userData.username;

            // TODO validate passwords on the fly with an onchange event.
            if (userData['password'].length < 8) {
                if(!Array.isArray(this.errors.password)) {
                    this.errors.password = [];
                }

                this.errors.password.push('You must have at least eight characters');
            }
            
            if (userData['confirm_password'].length < 8) {
                if(!Array.isArray(this.errors.confirm_password)) {
                    this.errors.confirm_password = [];
                }
                
                this.errors.confirm_password.push('You must have at least eight characters');
            }
            
            if (userData['password'] != userData['confirm_password']) {
                if(!Array.isArray(this.errors.password)) {
                    this.errors.password = [];
                }

                if(!Array.isArray(this.errors.confirm_password)) {
                    this.errors.confirm_password = [];
                }
                
                this.errors.password.push('Your passwords do not match');
                this.errors.confirm_password.push('Your passwords do not match');
            }

            if(this.errors.length == 0) {
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

                        this.errors = error.response.data.errors;
                    }
                });
            }
        }
    }
}
</script>