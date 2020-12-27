<template>
    <div>
        <user-status-bar></user-status-bar>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
        <hr>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr v-bind:id="row.id">
                    <td>{{row.chore}}</td>
                    <td v-if="userIsAdmin">{{row.assignedUsers}}</td>
                    <td>{{row.pointvalue}}</td>
                    <td>
                        <span v-on:click="handleCheckClick" class="fas fa-check" v-bind:data-choreid="row.id"></span>
                        <span v-if="userIsAdmin" v-on:click="showEditChoreModal" class="fas fa-edit" v-bind:data-choreid="row.id"></span>
                        <span v-if="userIsAdmin" v-on:click="handleTrashClick" class="fas fa-trash" v-bind:data-choreid="row.id"></span>
                    </td>
                </tr>
            </template>
        </datatable>
        <modal id="createChoreModal" v-if="userIsAdmin">
            <template v-slot:header>
                Create a chore
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
        <modal id="editChoreModal" v-if="userIsAdmin">
            <template v-slot:header>
                Edit chore
            </template>
            <div class="modal-body">
                <form id="createChoreForm">
                    <div class="form-group">
                        <label for="editchore">Chore:</label>
                        <input id="editchore" class="form-control" type="text" name="chore" v-bind:value="choreFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="editpointvalue">Point value:</label>
                        <input id="editpointvalue" class="form-control" type="number" name="pointvalue" v-bind:value="pointFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="editassignedto">Assigned to:</label>
                        <!-- <input id="editassignedto" name="assignedto" class="form-control" type="number" v-bind:value="pointFieldValue"> -->
                        <select class="form-select form-control" name="assignedto" aria-label="Assigned to user">
                            <option value="" :selected="assignee == ''">Unassigned</option>
                            <option v-for="user in allUsers" :key="user.id" v-bind:value="user.id" :selected="assignee == user.id">{{user.name}}</option>
                        </select>
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="updateChore">Update chore</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="sendEventBusMessage">Close</button>
                </footer>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal";
import UserStatusBar from './UserStatusBar.vue';
import eventBus from '../eventBus';

export default {
    props: ['id'],

    data() {
        return {
            chores: null,

            columns: [],

            rows: [],

            choreFieldValue: '',

            pointFieldValue: '',

            activeElementId: '',

            assignee: '',

            userIsAdmin: false,

            allUsers: []
        }
    },

    components: {
        Modal,
        UserStatusBar
    },

    mounted() {

        if (this.$store.getters.getUserAuthToken) {

            this.userHasAdminPrivelege();
            
            this.fetchChoresCollection();

            if(this.userIsAdmin) {
                this.getAllUsers();
            }
        } else {

            this.$router.push('login');
        }
    },

    methods: {
        fetchChoresCollection() {

            axios.get('/api/chores', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                
                response.data.forEach((row) => {

                    // Aggregates user names into a string... perhaps not ideal?
                    if(row.user){

                        row.assignedUsers = row.user.reduce((total, user) => {
                            return user.name + ', ';
                        }, '');
                    }
                });
                
                this.chores = response.data;

                this.columns = [
                    {label: 'chore', field: 'chore'}
                ];

                if(this.userIsAdmin) {
                    this.columns.push({label: 'assigned', field: 'assignedUsers'});
                }
                
                this.columns.push({label: 'points', field: 'pointvalue'});

                this.rows = response.data;
            });
        },

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
        },

        getAllUsers() {
            axios.get('/api/users', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then(
                (response) => {
                    this.allUsers = response.data;
                }
            );
        },

        showAddChoreModal() {
            let modalwindow = document.getElementById("createChoreModal");

            modalwindow.style.display = 'block';
        },

        showEditChoreModal(el) {
            let modalwindow = document.getElementById("editChoreModal");
            let choreId = el.target.dataset.choreid;
            let allChores = this.chores;
            let choreBeingEdited;

            allChores.forEach(chore => {
                console.log(chore);
                if (chore.id == choreId) {
                    choreBeingEdited = chore;
                }
            });

            this.choreFieldValue = choreBeingEdited.chore;
            this.pointFieldValue = choreBeingEdited.pointvalue;

            if(choreBeingEdited.user && choreBeingEdited.user.length > 0) {
                this.assignee = choreBeingEdited.user[0].id;
            } else {
                this.assignee = '';
            }

            this.activeElementId = choreId;

            modalwindow.style.display = 'block';
        },

        handleCheckClick(el) {
            console.log(el);
        },

        handleTrashClick(el) {
            let itemId = el.target.dataset.choreid;

            axios.delete('/api/chores/' + itemId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                this.chores = response.data;
                this.rows = response.data;
            });
        },

        'createChore': function(){
            let formEls = document.querySelectorAll('#createChoreModal .form-control');
            let choreData = {};

            formEls.forEach((el) => {
                choreData[el.name] = el.value;
            });

            axios({
                method: 'post',
                url: '/api/chores',
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                response.data.forEach((row) => {

                    // Aggregates user names into a string... perhaps not ideal?
                    if(row.user){

                        row.assignedUsers = row.user.reduce((total, user) => {
                            return user.name + ', ';
                        }, '');
                    }
                });

                this.chores = response.data;
                this.rows = response.data;
                
                // Close the modal
                eventBus.$emit('close-modal');
            });
        },
        
        'updateChore': function(){
            let formEls = document.querySelectorAll('#editChoreModal .form-control');
            let choreData = {};

            formEls.forEach((formInput) => {
                choreData[formInput.name] = formInput.value;
            });

            axios({
                method: 'put',
                url: '/api/chores/' + this.activeElementId,
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                response.data.forEach((row) => {

                    // Aggregates user names into a string... perhaps not ideal?
                    if(row.user){

                        row.assignedUsers = row.user.reduce((total, user) => {
                            return user.name + ', ';
                        }, '');
                    }
                });

                this.chores = response.data;
                this.rows = response.data;
                
                this.activeElementId = '';

                // Close the modal
                eventBus.$emit('close-modal');
            });
        },

        sendEventBusMessage() {
            eventBus.$emit('close-modal');
        }
    }
};
</script>