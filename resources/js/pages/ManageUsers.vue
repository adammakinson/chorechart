<template>
    <div class="container-fluid">
        <appmenu></appmenu>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr v-bind:id="row.id">
                    <td>{{row.name}}</td>
                    <td>{{row.username}}</td>
                    <td>{{row.email}}</td>
                    <td>
                        <span v-on:click="showChangeCredentialsModal" class="fas fa-key" v-bind:data-userid="row.id"></span>
                        <span v-on:click="showEditUserModal" class="fas fa-edit" v-bind:data-userid="row.id"></span>
                        <span v-on:click="removeUser" class="fas fa-trash" v-bind:data-userid="row.id"></span>
                    </td>
                </tr>
            </template>
        </datatable>
        <modal id="editUserModal">
            <template v-slot:header>
                Edit User
            </template>
            <div class="modal-body">
                <form id="editUserForm">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input id="name" name="name" class="form-control" type="text" v-bind:value="editingUsersNameValue">
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input id="username" name="username" class="form-control" type="text" v-bind:value="editingUsernameValue">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input id="email" name="email" class="form-control" type="text" v-bind:value="editingUserEmailValue">
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="updateUser">Update user</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="sendEventBusMessage">Close</button>
                </footer>
            </template>
        </modal>
        <modal id="updateUserCredentialsModal">
            <template v-slot:header>
                Change user credentials
            </template>
            <div class="modal-body">
                <form id="changeUserCredentialsForm">
                    <div class="form-group">
                        <label for="uname">Username:</label>
                        <input id="uname" name="username" class="form-control" type="text" v-bind:value="editingUsernameValue">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input id="password" name="password" class="form-control" type="password" value="">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm password:</label>
                        <input id="confirm_password" name="confirm_password" class="form-control" type="password" value="">
                    </div>
                </form>
                <div id="errorMessage">
                    <p v-for="errorMessage in errorMessages" :key="errorMessage.message">{{errorMessage.message}}</p>
                </div>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="changeUserCredentials">Update credentials</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="sendEventBusMessage">Close</button>
                </footer>
            </template>
        </modal>
    </div>
</template>

<script>
    import Modal from "../components/Modal";
    import Appmenu from "../components/AppMenu.vue";
    import eventBus from '../eventBus';
    
    export default {
        data() {
            return {
                users: null,
                currentUser: null,
                columns: [],
                rows: [],
                userIsAdmin: false,
                editingUsersId: '',
                editingUsersNameValue: '',
                editingUsernameValue: '',
                editingUserEmailValue: '',
                errorMessages: []
            }
        },

        components: {
            Modal,
            Appmenu
        },

        mounted() {
            if (this.$store.getters.getUserAuthToken && this.$store.getters.userIsAdmin) {
                
                this.getAllUsers();

            } else {

                this.$router.push('login');
            }
        },

        methods: {
            getAllUsers() {
                axios.get('/api/users', {
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then(
                    (response) => {
                        this.users = response.data;

                        this.columns = [
                            {label: 'name', field: 'name'},
                            {label: 'username', field: 'name'},
                            {label: 'email', field: 'email'}
                        ];

                        this.rows = response.data;
                    }
                );
            },

            showEditUserModal(el) {
                let editUserModal = document.getElementById('editUserModal');
                let userId = el.target.dataset.userid;
                let userBeingEdited;

                this.users.forEach(user => {
                    if (user.id == userId) {
                        userBeingEdited = user;
                    }
                });

                this.editingUsersId = userId;
                this.editingUsersNameValue = userBeingEdited.name;
                this.editingUsernameValue = userBeingEdited.username;
                this.editingUserEmailValue = userBeingEdited.email;

                editUserModal.style.display = 'block';
            },

            showChangeCredentialsModal(el) {
                let changeCredentialsModal = document.getElementById('updateUserCredentialsModal');
                let userId = el.target.dataset.userid;
                let userBeingEdited;

                this.users.forEach(user => {
                    if (user.id == userId) {
                        userBeingEdited = user;
                    }
                });

                this.editingUsersId = userId;
                this.editingUsernameValue = userBeingEdited.username;

                changeCredentialsModal.style.display = 'block';
            },

            changeUserCredentials() {
                let formEls = document.querySelectorAll('#updateUserCredentialsModal .form-control');
                let userData = {};
                let validPasswords = false;

                // reset errorMessages to an empty array
                this.errorMessages = [];

                formEls.forEach((el) => {
                    userData[el.name] = el.value;
                });

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
                        url: '/api/update-credentials/' + this.editingUsersId, // TODO rework this. its not restful
                        data: userData,
                        headers: {
                            authorization: this.$store.getters.getUserAuthToken
                        }
                    }).then((response) => {
                        // Close the modal
                        eventBus.$emit('close-modal');
                    }).catch((error) => {
                        // TODO - log a nice message to the user
                        console.log(error);
                    });
                }
            },

            updateUser() {
                let formEls = document.querySelectorAll('#editUserForm .form-control');
                let userData = {};

                formEls.forEach((el) => {
                    userData[el.name] = el.value;
                });

                axios({
                    method: 'put',
                    url: '/api/users/' + this.editingUsersId,
                    data: userData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    this.users = response.data;
                    this.rows = response.data;
                }).catch((error) => {
                    console.log(error);
                });

                // Close the modal
                eventBus.$emit('close-modal');
            },

            removeUser(el) {
                let userId = el.target.dataset.userid;

                axios({
                    method: 'delete',
                    url: '/api/users/' + userId,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    this.users = response.data;
                    this.rows = response.data;
                }).catch((error) => {
                    console.log(error);
                })

                // Close the modal
                eventBus.$emit('close-modal');
            },
            
            sendEventBusMessage() {
                eventBus.$emit('close-modal');
            }
        }
    }
</script>