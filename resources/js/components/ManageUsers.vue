<template>
    <div>
        <user-status-bar></user-status-bar>
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
                        <input id="name" name="name" class="form-control" type="text" value="">
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input id="username" name="username" class="form-control" type="text" value="">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input id="email" name="email" class="form-control" type="text" value="">
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
                <form id="editUserForm">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input id="username" name="username" class="form-control" type="text" value="">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input id="password" name="password" class="form-control" type="text" value="">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm password:</label>
                        <input id="confirm_password" name="confirm_password" class="form-control" type="text" value="">
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
    </div>
</template>

<script>
    import Modal from "./Modal";
    import UserStatusBar from "./UserStatusBar.vue";
    import eventBus from '../eventBus';
    
    export default {
        data() {
            return {
                users: null,
                currentUser: null,
                columns: [],
                rows: [],
                userIsAdmin: false
            }
        },

        components: {
            Modal,
            UserStatusBar
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

            showEditUserModal() {
                let editUserModal = document.getElementById('editUserModal');

                editUserModal.style.display = 'block';
            },

            showChangeCredentialsModal() {
                let changeCredentialsModal = document.getElementById('updateUserCredentialsModal');

                changeCredentialsModal.style.display = 'block';
            },

            changeUserCredentials() {
                console.log('Changing the user credentials!');
            },

            updateUser() {
                console.log('Updating the user!');

                // Close the modal
                eventBus.$emit('close-modal');
            },

            removeUser() {
                console.log("removing the user!");

                // Close the modal
                eventBus.$emit('close-modal');
            },
            
            sendEventBusMessage() {
                eventBus.$emit('close-modal');
            }
        }
    }
</script>