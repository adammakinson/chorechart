<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <h1>Manage users</h1>
        <div class="container">
            <div class="row justify-content-center">
                <div id="errorMessage">
                    <p v-for="errorMessage in errorMessages" :key="errorMessage.message">{{errorMessage.message}}</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="card" style="width:40em;">
                    <div class="card-header">
                        <h1>User information</h1>
                    </div>
                    <div class="card-body">
                        <form id="editUserForm">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input id="name" name="name" class="form-control" type="text" v-model="this.userData.name">
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input id="username" name="username" class="form-control" type="text" v-model="this.userData.username">
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input id="email" name="email" class="form-control" type="text" v-model="this.userData.email">
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" v-on:click.prevent="updateUser">Update user</button>
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
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input id="password" name="password" class="form-control" type="password" value="">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm password:</label>
                                <input id="confirm_password" name="confirm_password" class="form-control" type="password" value="">
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" v-on:click.prevent="changeUserCredentials">Update credentials</button>
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
import UserStatusBar from '../components/UserStatusBar.vue';

export default {
  components: { Card, Appmenu, UserStatusBar },
  
  data() {
      return {
          userData: {},
          errorMessages = []
      }
  },

  created() {
      this.userData = this.$store.getters.getUser;
  },

  methods: {
      updateUserInfo() {
          console.log("updating user information");
      },

      updateCredentials() {
          console.log("updating credentials");
      }
  }
}
</script>