<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <button v-if="userIsAdmin" class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
        <hr>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr v-bind:id="row.id">
                    <td>{{row.chore}}</td>
                    <td v-if="userIsAdmin">{{row.assignedUsers}}</td>v-bind
                    <td>{{row.pointvalue}}</td>
                    <td>
                        <div class="actionsContainer">
                            <span v-if="row.submittable && row.user.id" v-on:click="handleCheckClick" v-bind:class="[{ 'text-warning': row.pivot && row.pivot.pending, 'text-success': row.pivot && row.pivot.inspection_passed, 'text-secondary': ((row.pivot && !row.pivot.inspection_passed) && (row.pivot && !row.pivot.pending)) }, 'fas fa-check']" v-bind:data-choreid="row.id"></span>
                            <span v-if="userIsAdmin" v-on:click="showEditChoreModal" class="fas fa-edit text-info" v-bind:data-choreid="row.id"></span>
                            <span v-if="userIsAdmin" v-on:click="handleTrashClick" class="fas fa-trash text-danger" v-bind:data-choreid="row.id"></span>
                        </div>
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
                        <input id="chore" name="chore" class="form-control" type="text" v-bind:value="choreFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" name="pointvalue" class="form-control" type="number" v-bind:value="pointFieldValue">
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
                <form id="editChoreForm">
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
                        <select id="editassignedto" class="form-select form-control" name="assignedto" aria-label="Assigned to user">
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
import Appmenu from './AppMenu.vue';
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
        UserStatusBar,
        Appmenu
    },

    mounted() {

        if (this.$store.getters.getUserAuthToken) {

            this.userIsAdmin = this.$store.getters.userIsAdmin;
            
            this.fetchChoresCollection();

            this.fetchUsersTransactions();

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
                
                this.chores = this.processFetchedChores(response.data);
                
                this.setupTableData(response.data);
            });
        },

        fetchUsersTransactions() {
            let user = this.$store.getters.getUser;

            axios.get('/api/users/' + user.id + '/transactions', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.updateUserTransactions(response.data);
            });
        },

        processFetchedChores(data) {
            data.forEach((row) => {

                row.submittable = false;

                if (row.user && row.user.length) {
                    row.user = row.user[0];
                    row.pivot = row.user.pivot;
                }
                
                if (this.userIsAdmin) {
                    
                    if(row.assigner && row.assigner.length) {
                        row.assigner = row.assigner[0];
                    }

                    // Hrm, I think the thought here was if a chore was assigned to multiple users. I'm not sure I want 
                    // to go that route, but rather perhaps have chore instances? would need to stay an array for reduce 
                    // to work
                    // row.assignedUsers = row.user.reduce((total, user) => {
                    //     return user.name + ', ';
                    // }, '');
                    row.assignedUsers = row.user.name;

                    if (this.choreIsSelfAssigned(row) || this.choreIsReadyForInspection(row)) {
                        row.submittable = true;
                    }
                } else {
                    /**
                     * I think we can do this because a non admin user wont have unassigned chores in their list.
                     * In addition, the user is not returned because as a non admin user, all the chores
                     * should belong to the currently logged in user and passing back a user should be
                     * unneccessary.
                     */
                    row.submittable = true;

                    if(!row.user || Array.isArray(row.user)){
                        row.user = {};
                    }

                    if(!row.user.id && row.pivot){
                        row.user.id = row.pivot.user_id;
                    }
                }
            });

            return data;
        },

        choreIsSelfAssigned(row) {
            return row.assigner && row.user && row.assigner.id == row.user.id;
        },

        choreIsReadyForInspection(row) {
            return row.user && row.user.id && row.user.pivot.inspection_ready;
        },

        setupTableData(data) {
            
            this.columns = [
                {label: 'chore', field: 'chore'}
            ];

            if(this.userIsAdmin) {
                this.columns.push({label: 'assigned', field: 'assignedUsers'});
            }
            
            this.columns.push({label: 'points', field: 'pointvalue'});

            this.rows = data;
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

            this.choreFieldValue = '';
            this.pointFieldValue = '';
            this.assignee = '';
            this.activeElementId = '';

            modalwindow.style.display = 'block';
        },

        findChoreById(allChores, choreId) {
            return allChores.find(chore => chore.id == choreId);
        },

        showEditChoreModal(el) {
            let modalwindow = document.getElementById("editChoreModal");
            let choreId = el.target.dataset.choreid;
            let allChores = this.chores;
            let choreBeingEdited = this.findChoreById(allChores, choreId);

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
            let choreId = el.target.dataset.choreid;
            let allChores = this.chores;
            let choreBeingEdited = this.findChoreById(allChores, choreId);
            let user = this.$store.getters.getUser;
            let choreData = {
                id: choreBeingEdited.id,
                userId: choreBeingEdited.user.id,
                inspection_ready: true
            };

            // TODO: If a chore has been approved and had points awarded for it,
            // subsequent clicks on the checkbox should NOT incur a server request
            // anymore.
            // QUESTION: should a user be allowed to edit or delete a chore after
            // points have been awarded? Should all actions be disabled?
            // I think what needs to happen is a chore can be edited or deleted
            // until it has a pending status. Once it has a pending status, the
            // ability to edit the chore (for example, modify the points or change the user)
            // OR delete the chore should be disabled.

            // Hrm... do we really need user.id in the route???
            if (!choreBeingEdited.pivot.points_awarded == '1') {
                axios({
                    method: 'put',
                    url: '/api/users/' + choreBeingEdited.user.id + '/chores/' + choreBeingEdited.id,
                    data: choreData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    if (response.data.points_awarded) {

                        axios({
                            method: 'post',
                            url: '/api/users/' + choreBeingEdited.user.id + '/transactions', 
                            data: choreData,
                            headers: {
                                authorization: this.$store.getters.getUserAuthToken
                            }
                        }).then(() => {
                            this.updateUserTransactions(response.data);
                        });
                    }

                }).then(() => {
                    this.fetchChoresCollection();
                });
            }
        },

        updateUserTransactions(transactions) {
            this.$store.commit('setUserTransactions', transactions);
        },

        handleTrashClick(el) {
            let itemId = el.target.dataset.choreid;

            axios.delete('/api/chores/' + itemId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                // TODO refactor this out because it's duplicated everywhere
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
            });
        },

        createChore(){
            let formEls = document.querySelectorAll('#createChoreModal .form-control');
            let choreData = {};

            formEls.forEach((el) => {
                choreData[el.name] = el.value;
            });

            // Create the chore
            axios({
                method: 'post',
                url: '/api/chores',
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                let chore = response.data;

                // If a user has been specified in the form,
                // assign the chore to the user.
                if (choreData['assignedto'] !== '') {
                    axios({
                        method: 'post',
                        url: '/api/users/' + choreData['assignedto'] + '/chores/' + chore.id,
                        data: choreData,
                        headers: {
                            authorization: this.$store.getters.getUserAuthToken
                        }
                    });
                }

            }).then(() => {
                this.fetchChoresCollection();
                
                // Close the modal
                eventBus.$emit('close-modal');
            });
        },
        
        updateChore() {
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

                this.chores = this.processFetchedChores(response.data);

                this.rows = this.chores;
                
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