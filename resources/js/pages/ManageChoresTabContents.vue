<template>
    <div class="tabcontent">
        <div class="container-fluid">
            <div class="row">
                <notification v-if="typeof generalNotice === 'object'" v-bind:notice="generalNotice"></notification>
            </div>
            <div class="row">
                <div class="col-sm p-2 m-2">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
                    <button v-if="userCardsHighlighted && choresAreHighlighted" class="btn btn-primary" v-on:click="assignToUser">add to all</button>
                    <button class="btn btn-primary" v-on:click="assignChores">Assign</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <list-group :listId="'chores-list'">
                                <list-item v-for="choreData in choresList" :key="choreData.id" :listItem="choreData">
                                    {{choreData.chore}} ({{choreData.pointvalue}} pts)
                                    <template v-slot:actions>
                                        <span class="fas fa-edit text-info" v-bind:data-itemId="choreData.id" v-on:click.stop="editChore"></span>
                                        <span class="fas fa-trash text-danger" v-bind:data-itemId="choreData.id" v-on:click.stop="deleteChore"></span>
                                    </template>
                                </list-item>
                            </list-group>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div>
                        <cardgrid>
                            <card v-for="cardData in users" :key="cardData.id" :cardData="cardData" v-bind:data-userid="cardData.id">
                                <h4>{{cardData.name}}</h4>
                                <list-group v-if="cardData.chores">
                                    <list-item v-for="userChore in cardData.chores" :key="userChore.chore_id" :listItem="userChore" v-bind:data-itemId="userChore.chore_id">
                                        {{userChore.chore}}
                                        <template v-slot:actions>
                                            <span v-if="isApprovable(userChore)" class="fas fa-check" v-on:click.stop="approveUsersAssignment"></span><!-- chore approval if chore has been submitted -->
                                            <span v-if="isDeletable(userChore)" class="fas fa-trash text-danger" v-bind:data-itemId="userChore.chore_id" v-on:click.stop="deleteUserAssignment"></span>
                                        </template>
                                    </list-item>
                                </list-group>
                            </card>
                        </cardgrid>
                    </div>
                </div>
            </div>
        </div>
        <modal id="createChoreModal" v-if="userIsAdmin">
            <template v-slot:header>
                Create a chore
            </template>
            <div class="modal-body">
                <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                <form id="createChoreForm">
                    <div class="form-group">
                        <label for="chore">Chore: <span class="text-danger text-opacity-50" v-if="modalErrors.chore">{{modalErrors.chore[0]}}</span></label>
                        <input id="chore" name="chore" class="form-control" type="text" v-bind:value="choreFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value: <span class="text-danger text-opacity-50" v-if="modalErrors.pointvalue">{{modalErrors.pointvalue[0]}}</span></label>
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
                <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                <form id="editChoreForm">
                    <div class="form-group">
                        <label for="editchore">Chore: <span class="text-danger text-opacity-50" v-if="modalErrors.chore">{{modalErrors.chore[0]}}</span></label>
                        <input id="editchore" class="form-control" type="text" name="chore" v-bind:value="choreBeingEdited.chore">
                    </div>
                    <div class="form-group">
                        <label for="editpointvalue">Point value: <span class="text-danger text-opacity-50" v-if="modalErrors.pointvalue">{{modalErrors.pointvalue[0]}}</span></label>
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
import GroupsSubView from "./GroupsSubView.vue";
import Cardgrid from '../components/Cardgrid.vue';
import Card from '../components/Card.vue';
import ListGroup from "../components/ListGroup.vue";
import ListItem from "../components/ListItem.vue";
import eventBus from "../eventBus.js";
import Modal from "../components/Modal";
import Notification from "../components/Notification.vue";

export default {
    created() {
        eventBus.$on("chore-edit-click", (chore) => {
            this.choreBeingEdited = chore
        });

        eventBus.$on('refetch-chores', () => {
            this.fetchChoresCollection();
        });

        eventBus.$on('refetch-userchores', () => {
            this.fetchAllIncompletedUserChores().then((response) => {
                let allUsersChores = response.data;
                let users = this.users;
                let choresSegregatedByUser = {};

                allUsersChores.forEach((chore) => {
                    if(!Array.isArray(choresSegregatedByUser[chore.user_id])) {
                        choresSegregatedByUser[chore.user_id] = [];
                    }

                    choresSegregatedByUser[chore.user_id].push(chore);
                });

                users.forEach((user) => {
                user.chores = choresSegregatedByUser[user.id];
                user.eventsObject = {
                    click: this.handleIndividualClick,
                    dragenter: this.dragEnter,
                    dragover: this.dragOver,
                    dragleave: this.dragLeave,
                    drop: this.drop
                };
            });

            this.users = users;
                
            });
        });

        this.fetchChoresCollection();

        Promise.all([
            this.getAllUsers(),
            this.fetchAllIncompletedUserChores()
        ]).then((response) => {
            let users = response[0].data;
            let allUsersChores = response[1].data;
            let choresSegregatedByUser = {};

            allUsersChores.forEach((chore) => {
                if(!Array.isArray(choresSegregatedByUser[chore.user_id])) {
                    choresSegregatedByUser[chore.user_id] = [];
                }

                choresSegregatedByUser[chore.user_id].push(chore);
            });

            users.forEach((user) => {
                user.chores = choresSegregatedByUser[user.id];
                user.eventsObject = {
                    click: this.handleIndividualClick,
                    dragenter: this.dragEnter,
                    dragover: this.dragOver,
                    dragleave: this.dragLeave,
                    drop: this.drop
                };
            });

            this.users = users;
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
            }],

            choresList: [],
            users: [],
            assignmentTabContentData: this.users,
            userIsAdmin: false,
            pointFieldValue: '',
            choreFieldValue: '',
            activeElementId: '',
            choreBeingEdited: [],
            allUsersChores: {},
            generalNotice: '',
            modalNotice: '',
            modalErrors: [],
            userCardsHighlighted: false,
            choresAreHighlighted: false
        }
    },

    components: {
        GroupsSubView,
        ListGroup,
        ListItem,
        Cardgrid,
        Card,
        Modal,
        Notification
    },

    mounted() {
        this.userIsAdmin = this.$store.getters.userIsAdmin;
    },

    methods: {
        fetchChoresCollection() {
            axios.get('/api/chores', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.choresList = response.data;
            });
        },

        fetchAllIncompletedUserChores(users) {
            return axios.get('/api/user-chores', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            });
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

            axios({
                method: 'post',
                url: '/api/chores',
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then(() => {
                this.fetchChoresCollection();
                
                eventBus.$emit('close-modal');
            }).catch((error) => {
                console.log(error.response);

                this.modalNotice = {
                    message: error.response.data.message,
                    status: error.response.status
                };

                this.choreFieldValue = choreData.chore;
                this.pointFieldValue = choreData.pointvalue;

                this.modalErrors = error.response.data.errors;
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

                eventBus.$emit('close-modal');
            }).catch((error) => {
                console.log(error.response);

                this.modalNotice = {
                    message: error.response.data.message,
                    status: error.response.status
                }

                this.choreFieldValue = choreData.chore;
                this.pointFieldValue = choreData.pointvalue;

                this.modalErrors = error.response.data.errors;
            });
        },

        assignToUser() {
            let highlightedChores = document.querySelectorAll('#chores-list .list-group-item.active');
            let highlightedUsers = document.querySelectorAll('.card.active');

            highlightedUsers.forEach((user) => {
                let recipientsListUl;

                if (!user.querySelector('ul.list-group')) {
                    recipientsListUl = document.createElement('ul');
                    recipientsListUl.classList.add('list-group');
                    user.appendChild(recipientsListUl);
                } else {
                    recipientsListUl = user.querySelector('ul.list-group');
                }

                highlightedChores.forEach((chore) => {

                    if(!this.userListHasChore(recipientsListUl, chore)) {
                        let choreName = chore.innerText.trim();
                        let choreId = chore.dataset.itemid.trim();

                        let userChore = document.createElement('li');
                        userChore.classList.add('assignment');
                        userChore.classList.add('list-group-item');
                        userChore.dataset.itemid = choreId;
                        userChore.innerHTML = `<div style="display: flex; justify-content: space-between;"><div>${choreName}</div><div><span class="fas fa-minus text-danger discardAssignmentIcon" data-itemid="${choreId}"></span></div></div>`;
                    
                        recipientsListUl.append(userChore);
                
                        let choreDiscardButton = userChore.querySelector('.discardAssignmentIcon');
                        choreDiscardButton.addEventListener('click', this.discardAssignment);
                    }
                });

                user.classList.remove('active');

            });
        },

        assignChores() {
            let userCards = document.querySelectorAll('.card');
            let assignmentsData = {};

            userCards.forEach((user) => {
                let userAssignments = user.querySelectorAll('.assignment');

                userAssignments.forEach((assignment) => {
                    if(! assignmentsData.hasOwnProperty(user.dataset.userid)) {
                        assignmentsData[user.dataset.userid] = [];
                    }

                    assignmentsData[user.dataset.userid].push(assignment.dataset.itemid);
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

                    if(response.status == 200){
                        let choresList = document.querySelectorAll('#chores-list .list-group-item.active');
                        let assignedItems = document.querySelectorAll('.assignment');

                        choresList.forEach((choreListItem) => {
                            choreListItem.classList.remove('active');
                        });

                        assignedItems.forEach((assignedChore) => {
                            let discardAssignmentButton = assignedChore.querySelector('.discardAssignmentIcon');

                            discardAssignmentButton.classList.remove('fa-minus');
                            discardAssignmentButton.classList.add('fa-trash');
                            discardAssignmentButton.removeEventListener('click', this.discardAssignment);
                            discardAssignmentButton.addEventListener('click', this.deleteUserAssignment);
                        });
                    }
                });
            } else {
                this.generalNotice = {
                    message: 'You need to add chores to users lists to be able to assign them.',
                    type: 'error'
                }

                setTimeout(() => {
                    this.generalNotice = '';
                }, 2000);
            }
        },

        isEmpty(obj) {
            return Object.keys(obj).length === 0;
        },

        // toggles active class on the user cards
        handleIndividualClick(event) {
            let userCard = event.target.closest('.card');

            userCard.classList.toggle('active');

            this.userCardsHighlighted = !!document.querySelectorAll('.card.active').length;
            this.choresAreHighlighted = !!document.querySelectorAll('.list-group-item.active').length;
        },

        dragEnter(e) {
            e.preventDefault();

            let userCard = e.toElement.closest('.card');

            userCard.classList.add('border-success');
        },

        dragOver(e) {
            e.preventDefault();

            let userCard = e.toElement.closest('.card');

            userCard.classList.add('border-success');
        },

        dragLeave(e) {
            let userCard = e.toElement;

            userCard.classList.remove('border-success');
        },

        drop(e) {
            e.stopPropagation();

            let card = e.toElement.closest('.card');
            let dropTarget = card.querySelector('.list-group');
            let userChoresList;
            let userChore;
            let droppedChoreName = e.dataTransfer.getData('text/html'); 
            let droppedChoreId = e.dataTransfer.getData('text/choreId');
            
            card.classList.remove('border-success');

            userChore = document.createElement('li');
            userChore.classList.add('assignment');
            userChore.classList.add('list-group-item');
            userChore.innerHTML = `<div style="display: flex; justify-content: space-between;"><div>${droppedChoreName}</div><div><span class="fas fa-minus text-danger discardAssignmentIcon" data-itemid="${droppedChoreId}"></span></div></div>`;
            userChore.dataset.itemid = droppedChoreId;

            if (!this.assignmentsStarted(dropTarget)) {
                userChoresList = document.createElement('ul');
                userChoresList.className = 'list-group';
                card.appendChild(userChoresList);
            } else {
                userChoresList = dropTarget;
            }
            
            // User shouldn't have duplicate chores
            if(!this.userListHasChore(userChoresList, userChore)) {
                userChoresList.appendChild(userChore);
            }

            // Create an event listener to handle discarding assignments
            let choreDiscardButton = userChore.querySelector('.discardAssignmentIcon');
            choreDiscardButton.addEventListener('click', this.discardAssignment);
            
            return false;
        },

        assignmentsStarted(dropTarget) {
            if(!dropTarget) {
                return false;
            }

            let choresList = dropTarget.querySelectorAll('.list-group-item');

            return choresList.length > 0;
        },

        userListHasChore(userChoresList, userChore) {
            let hasChore = false;

            userChoresList.childNodes.forEach((assignment) => {
                if(assignment.dataset.itemid === userChore.dataset.itemid) {
                    hasChore = true;
                    console.log(hasChore);
                }
            });

            return hasChore;
        },

        discardAssignment(e) {
            e.stopPropagation();

            let assignment = e.target.closest('.assignment');
            let assignmentsList = assignment.parentNode;

            assignment.remove();

            if(assignmentsList.querySelectorAll('li').length == 0) {
                assignmentsList.remove();
            }
        },

        editChore(el) {
            console.log(el);

            let modalwindow = document.getElementById("editChoreModal");
            let choreId = el.target.dataset.itemid;
            let choreBeingEdited = this.getChoreById(choreId);

            this.choreFieldValue = choreBeingEdited.chore;
            this.pointFieldValue = choreBeingEdited.pointvalue;
            this.activeElementId = choreId;

            eventBus.$emit('chore-edit-click', choreBeingEdited);

            modalwindow.style.display = 'block';
        },

        getChoreById(id) {
            return this.choresList.filter((choreItem) => {
                return choreItem.id == id;
            })[0];
        },

        deleteChore(el) {
            let choreId = el.target.dataset.itemid;

            axios.delete('/api/chores/' + choreId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                eventBus.$emit('refetch-chores');
            });
        },

        isApprovable(chore) {
            return chore.inspection_ready !== null && chore.pending === 1;
        },

        isDeletable(chore) {
            return chore.inspection_ready === null && !chore.pending;
        },

        /**
         * We need to check that the assignment has not been submitted by the user
         * before deleting. We should only be able to delete chores that have not
         * been submitted.
         */
        deleteUserAssignment(e) {
            let assignment = e.target.closest('.list-group-item');

            let userChoreId = assignment.id;

            axios.delete('/api/user-chores/' + userChoreId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                eventBus.$emit('refetch-userchores');
            });
        },

        /**
         * Approves user assignment
         */
        approveUsersAssignment(e) {
            let assignment = e.target.closest('.list-group-item');
            let userCard = e.target.closest('.card');
            let choreId = assignment.dataset.itemid;
            let userId = userCard.dataset.userid;

            axios({
                method: 'put',
                url: '/api/users/' + userId + '/chores/' + choreId,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                if (response.data.points_awarded) {

                        // Set the transaction type
                        response.data.transactionType = 'choreCompletion';

                        axios({
                            method: 'post',
                            url: '/api/users/' + response.data.user_id + '/transactions', 
                            data: response.data,
                            headers: {
                                authorization: this.$store.getters.getUserAuthToken
                            }
                        }).then((transactionResponse) => {
                            this.$store.commit('setUserTransactions', transactionResponse.data);
                        });
                    }
            }).then((response) => {
                eventBus.$emit('refetch-userchores');
            });
        }
    }
}
</script>