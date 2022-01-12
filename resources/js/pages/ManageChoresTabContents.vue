<template>
    <div class="tabcontent">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2 style="margin-top: .75em">chores</h2>
                    <div class="card">
                        <div class="card-body">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
                            <list-group :listId="'chores-list'">
                                <component v-for="choreData in choresList" :key="choreData.id" :is='listGroupContents' :listItem="choreData"></component>
                            </list-group>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <!-- <h2>Assign to:</h2> -->
                    <button class="btn btn-primary mt-4 mb-2" v-on:click="assignToUser">Assign to ></button>
                    <div class="card">
                        <div class="card-body">
                            <tab-component :tabsData="assignmentTabsData">
                                <list-group :listId="'recipients-list'">
                                    <component v-for="assigneeData in assignmentTabContentData" :key="assigneeData.id" :is="assignmentTabContents" :listItem="assigneeData"></component>
                                </list-group>
                            </tab-component>
                            <button class="btn btn-primary mt-4" v-on:click="assignChores">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="createChore">Create chore</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="fireModalCloseEvent">Close</button>
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
                        <input id="editchore" class="form-control" type="text" name="chore" v-bind:value="choreBeingEdited.chore">
                    </div>
                    <div class="form-group">
                        <label for="editpointvalue">Point value:</label>
                        <input id="editpointvalue" class="form-control" type="number" name="pointvalue" v-bind:value="choreBeingEdited.pointvalue">
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="updateChore">Update chore</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="fireModalCloseEvent">Close</button>
                </footer>
            </template>
        </modal>
    </div>
</template>

<script>
import IndividualsSubView from "./IndividualsSubView.vue";
import GroupsSubView from "./GroupsSubView.vue";
import TabComponent from "../components/TabComponent.vue";
import ListGroup from "../components/ListGroup.vue";
import ChoreListItem from "../components/ChoreListItem.vue";
import eventBus from "../eventBus.js";
import Modal from "../components/Modal";

export default {
    created() {
        eventBus.$on("assign-to-individuals-tab-click", (contents) => {
            this.assignmentTabContents = contents;
            this.assignmentTabContentData = this.users;
        });
        
        eventBus.$on("assign-to-group-tab-click", (contents) => {
            this.assignmentTabContents = contents;
            this.assignmentTabContentData = this.groups;
        });

        eventBus.$on("chore-edit-click", (chore) => {
            this.choreBeingEdited = chore
        });

        eventBus.$on('refetch-chores', () => {
            this.fetchChoresCollection();
        });

        // const chores = document.querySelectorAll('#chores-list .list-group-item');

        this.getAllUsers().then((response) => {
            this.users = response.data;
            this.assignmentTabContentData = this.users;
        });
    },

    data() {
        return {
            assignmentTabsData: [{
                'id': 1,
                'label': 'Individuals',
                'active': true,
                'firesEvent': 'assign-to-individuals-tab-click',
                'loadsContent': 'IndividualsSubView',
                'dataToPass': 'users'
            },
            {
                'id': 2,
                'label': 'Groups',
                'firesEvent': 'assign-to-group-tab-click',
                'loadsContent': 'GroupsSubView',
                'dataToPass': 'groups'
            }],

            choresList: [],

            users: [],

            groups: [
                {
                    id: 1,
                    title: 'Whole family'
                },
                {
                    id: 2,
                    title: 'Boys team'
                },
                {
                    id: 3,
                    title: 'Girls team'
                },
                {
                    id: 4,
                    title: 'Parents'
                },
                {
                    id: 5,
                    title: 'Kids'
                }
            ],

            assignmentTabContents: 'IndividualsSubView',

            assignmentTabContentData: '',

            listGroupContents: 'ChoreListItem',

            userIsAdmin: false,

            pointFieldValue: '',

            choreFieldValue: '',

            activeElementId: '',

            choreBeingEdited: []
        }
    },

    components: {
        IndividualsSubView,
        GroupsSubView,
        TabComponent,
        ListGroup,
        ChoreListItem,
        Modal
    },

    mounted() {
        this.userIsAdmin = this.$store.getters.userIsAdmin;

        this.fetchChoresCollection();
    },

    methods: {
        fetchChoresCollection() {

            axios.get('/api/chores', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.choresList = this.processFetchedChores(response.data);
            });
        },

        processFetchedChores(data) {

            /**
             * I'm sure this can be cleaned up
             */

            data.forEach((row) => {

                // row.editable = true;
                // row.deletable = true;

                if (row.user && row.user.length) {
                    row.user = row.user[0];
                    row.pivot = row.user.pivot;
                }
                
                if (this.userIsAdmin) {
                    if(row.assigner && row.assigner.length) {
                        row.assigner = row.assigner[0];
                    }

                    row.assignedUsers = row.user.name;

                    // if (this.choreIsReadyForInspection(row)) {
                    //     row.editable = false;
                    // }

                    // if (this.choreIsFinished(row)) {
                    //     row.deletable = false;
                    // }
                } else {
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

        getAllUsers() {
            return axios.get('/api/users', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            });
        },

        showAddChoreModal() {
            let modalwindow = document.getElementById("createChoreModal");

            this.choreFieldValue = '';
            this.pointFieldValue = '';
            this.activeElementId = '';

            modalwindow.style.display = 'block';
        },

        fireModalCloseEvent() {
            eventBus.$emit('close-modal');
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
                url: '/api/chores/' + this.choreBeingEdited.id,
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                this.choreBeingEdited = '';

                this.fetchChoresCollection();

                // Close the modal
                eventBus.$emit('close-modal');
            });
        },

        assignToUser() {
            let highlightedChores = document.querySelectorAll('#chores-list .list-group-item.active');
            let highlightedUsers = document.querySelectorAll('#recipients-list .list-group-item.active');

            highlightedUsers.forEach((user) => {
                let recipientsListUl;

                if (!user.querySelector('ul.assignmentsList')) {
                    recipientsListUl = document.createElement('ul');
                    recipientsListUl.classList.add('assignmentsList');
                    user.appendChild(recipientsListUl);
                } else {
                    recipientsListUl = user.querySelector('ul.assignmentsList');
                }

                highlightedChores.forEach((chore) => {
                    let choreName = chore.innerText.trim();
                    let choreId = chore.dataset.itemid.trim();

                    let userChore = document.createElement('li');
                    userChore.classList.add('assignment');
                    userChore.dataset.choreid = choreId;
                    userChore.innerHTML = `<div style="display: flex; justify-content: space-between;"><div>${choreName}</div><div><span class="fas fa-trash text-danger discardAssignmentIcon" data-itemid="${choreId}"></span></div></div>`;
                
                    recipientsListUl.append(userChore);

                    chore.classList.remove('active');
                });

                user.classList.remove('active');

            });
        },

        assignChores() {
            let highlightedUsers = document.querySelectorAll('#recipients-list .list-group-item');
            let assignmentsData = {};

            highlightedUsers.forEach((user) => {
                let userAssignments = user.querySelectorAll('.assignment');

                userAssignments.forEach((assignment) => {
                    if(! assignmentsData.hasOwnProperty(user.dataset.userid)) {
                        assignmentsData[user.dataset.userid] = [];
                    }

                    assignmentsData[user.dataset.userid].push(assignment.dataset.choreid);
                });
            });

            if (! this.isEmpty(assignmentsData)) {

                axios({
                    method: 'post',
                    url: '/api/user-chores',
                    data: assignmentsData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    console.log(response);
                });
            } else {
                // TODO - create user friendly notifications
                console.log('no chores to assign.')
            }
        },

        isEmpty(obj) {
            return Object.keys(obj).length === 0;
        }
    }
}
</script>