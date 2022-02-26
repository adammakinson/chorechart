<template>
    <div>
        <appmenu></appmenu>
        <h1>Manage users</h1>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div id="errorMessage">
                    <p v-for="errorMessage in errorMessages" :key="errorMessage.message">{{errorMessage.message}}</p>
                </div>
            </div>
            <div class="row justify-content-center mb-4">
                <div class="card" style="width:40em;">
                    <div class="card-header">
                        <h1>User information</h1>
                    </div>
                    <div class="card-body">
                        <form id="editUserForm">
                            <div class="form-group mb-2">
                                <label for="name">Name:</label>
                                <input id="name" name="name" class="form-control" type="text" v-model="userData.name">
                            </div>
                            <div class="form-group mb-2">
                                <label for="username">Username:</label>
                                <input id="username" name="username" class="form-control" type="text" v-model="userData.username">
                            </div>
                            <div class="form-group mb-2">
                                <label for="email">Email:</label>
                                <input id="email" name="email" class="form-control" type="text" v-model="userData.email">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" v-on:click.prevent="updateUserInfo">Update user</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="card" style="width:40em;">
                    <div class="card-header">
                        <h1>Credentials</h1>
                    </div>
                    <div class="card-body">
                        <form id="changeUserCredentialsForm">
                            <!-- <div class="form-group">
                                <label for="uname">Username:</label>
                                <input id="uname" name="username" class="form-control" type="text" v-bind:value="editingUsernameValue">
                            </div> -->
                            <div class="form-group mb-2">
                                <label for="password">Password:</label>
                                <input id="password" name="password" class="form-control" type="password" value="">
                            </div>
                            <div class="form-group mb-2">
                                <label for="confirm_password">Confirm password:</label>
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

export default {
    components: { 
        Card,
        Appmenu
    },
  
    data() {
        return {
            userData: {},
            errorMessages: []
        }
    },

    created() {
      this.userData = this.$store.getters.getUser;
    },

    methods: {
        updateUserInfo() {
            console.log("updating user information");

            axios({
                method: 'put',
                url: '/api/user-info/' + this.userData.id,
                data: this.userData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                console.log(response.data.message);
            }).catch((error) => {
                console.log(error);
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

            // reset errorMessages to an empty array
            this.errorMessages = [];

            // TODO validate passwords on the fly with an onchange event.
            if (userData['password'].length < 8) {
                this.errorMessages.push({ message: 'Your password needs to be at least eight characters'});
            }
            
            if (userData['confirm_password'].length < 8) {
                this.errorMessages.push({ message: 'Your password confirmation needs to be at least eight characters'});
            }
            
            if (userData['password'] != userData['confirm_password']) {
                this.errorMessages.push({ message: 'Your passwords do not match'});
            }

            if(this.errorMessages.length == 0) {
                validPasswords = true;
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
                    console.log(response.data);

                    document.querySelector('#password').value = '';
                    document.querySelector('#confirm_password').value = '';
                }).catch((error) => {
                    // TODO - log a nice message to the user
                    console.log(error);
                });
            } else {
                console.log('invalid passwords');
            }
        }
    }
}
</script>