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
                <form id="createChoreForm">
                    <div class="form-group">
                        <label for="chore">Chore:</label>
                        <input id="chore" name="chore" class="form-control" type="text" value="">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" name="pointvalue" class="form-control" type="number" value="">
                    </div>
                    <div class="form-group">
                        <label for="assignedto">Assigned to:</label>
                        <!-- <input id="assignedto" name="assignedto" class="form-control" type="number" v-bind:value="pointFieldValue"> -->
                        <select class="form-select form-control" name="assignedto" aria-label="Assigned to user">
                            <option value="" selected>Unassigned</option>
                            <option v-for="user in allUsers" :key="user.id" v-bind:value="user.id">{{user.name}}</option>
                        </select>
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="createChore">Create chore</button>
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
            showEditUserModal() {
                console.log("Showing the user edit modal!");
            },

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

            removeUser() {
                console.log("removing the user!");
            },

            // TODO recreate this as a vuex getter
            userHasAdminPrivelege() {
                let userRoles = this.$store.getters.getUsersRoles;

                console.log(userRoles);

                if(userRoles.some((role) => { 
                    return role.role == 'admin';
                    })) {
                    this.userIsAdmin = true;
                } else {
                    this.userIsAdmin = false;
                }
            }
        }
    }
</script>