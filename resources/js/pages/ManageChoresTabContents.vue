<template>
    <div class="tabcontent">
        <div>
            <notification v-if="typeof generalNotice === 'object'" v-bind:notice="generalNotice"></notification>
            <Button colorClass="text-white" bgColorClass="bg-blue-600" callback="showAddChoreModal">Add chore</Button>
            <Button colorClass="text-white" v-if="userCardsHighlighted && choresAreHighlighted" bgColorClass="bg-blue-600" callback="assignToUser">add to all</Button>
            <Button colorClass="text-white" v-if="assignmentsArePending" bgColorClass="bg-blue-600" callback="assignChores">Assign</Button>
            <div>
                <list-group :listId="'chores-list'" class="mt-4">
                    <list-item v-for="choreData in choresList" :key="choreData.id" :listItem="choreData" :draggable="true" :selectable="true" class="border border-slate-400">
                        <div class="flex">
                            <div class="w-10 h-10 p-2 bg-gray-300 flex center">{{choreData.pointvalue}}</div>
                            <div class="h-8 p-1.5">{{choreData.chore}}</div>
                        </div>
                        <template v-slot:actions>
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" widthClass="w-10" paddingClass="px-0 py-2" callback="editChore" v-bind:args="choreData.id">
                                <Icon class="fas fa-edit"></Icon>
                            </Button>
                            <Button colorClass="text-white" bgColorClass="bg-red-600" widthClass="w-10" paddingClass="px-0 py-2" callback="deleteChore" :args="choreData.id">
                                <Icon class="fas fa-trash"></Icon>
                            </Button>
                        </template>
                    </list-item>
                </list-group>
                <cardgrid class="pt-4 w-100 sm:w-full">
                    <card v-for="cardData in users" :key="cardData.id" :cardData="cardData" v-bind:data-userid="cardData.id">
                        <template v-slot:header>
                            <h4>{{cardData.name}}</h4>
                        </template>
                        <list-group v-if="cardData.chores">
                            <list-item v-for="userChore in cardData.chores" :key="userChore.chore_id" :listItem="userChore" v-bind:data-itemId="userChore.chore_id" class="pl-2 border border-slate-400 leading-3">
                                {{userChore.chore}}
                                <template v-slot:actions>
                                    <Button v-if="isApprovable(userChore)" colorClass="text-white" bgColorClass="bg-green-600" widthClass="w-10" paddingClass="px-0 py-2" callback="approveUsersAssignment" :args="userChore">
                                        <Icon class="fas fa-check"></Icon>
                                    </Button>
                                    <Button v-if="isDeletable(userChore)" colorClass="text-white" bgColorClass="bg-red-600" widthClass="w-10" paddingClass="px-0 py-2" callback="deleteUserAssignment" :args="userChore.chore_id">
                                        <Icon class="fas fa-trash"></Icon>
                                    </Button>
                                </template>
                            </list-item>
                        </list-group>
                    </card>
                </cardgrid>
            </div>
        </div>
        <modal id="createChoreModal" v-if="userIsAdmin">
            <template v-slot:header>
                Create a chore
            </template>
            <div class="modal-body">
                <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                <form id="createChoreForm">
                    <FormInput v-for="formField in createChoreModalData" :key="formField.identifier"
                        :identifier="formField.identifier"
                        :type="formField.type"
                        :elementLabel="formField.label"
                        :errors="formField.errors"
                        :value="formField.value"
                        :callback="formField.callback"
                    ></FormInput>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <Button colorClass="text-white" bgColorClass="bg-blue-600" marginClass="mr-4" callback="createChore">Create chore</Button>
                    <Button colorClass="text-white" bgColorClass="bg-red-600" marginClass="mr-4" data-dismiss="modal" callback="fireModalCloseEvent">Close</Button>
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
                    <FormInput v-for="formField in editChoreModalData" :key="formField.identifier"
                        :identifier="formField.identifier"
                        :type="formField.type"
                        :elementLabel="formField.label"
                        :errors="formField.errors"
                        :value="formField.value"
                        :callback="formField.callback"
                    ></FormInput>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <Button colorClass="text-white" bgColorClass="bg-blue-600" marginClass="mr-4" callback="updateChore">Update chore</Button>
                    <Button colorClass="text-white" bgColorClass="bg-red-600" marginClass="mr-4" data-dismiss="modal" callback="fireModalCloseEvent">Close</Button>
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
import Button from '../components/Button.vue';
import FormInput from '../components/FormInput.vue';
import Icon from "../components/Icon.vue";

export default {
    created() {
        eventBus.on("chore-edit-click", (chore) => {
            this.choreBeingEdited = chore
        });

        eventBus.on('refetch-chores', () => {
            this.fetchChoresCollection();
        });

        eventBus.on('callback', (eventData) => {
    
            // 'this' is the VueComponent object
            if(this[eventData.callback]){
                if(eventData.args){
                    this[eventData.callback](eventData.args);
                } else {
                    this[eventData.callback]();
                }
            }
        });

        eventBus.on("chore-list-item-clicked", () => {
            this.choresAreHighlighted = document.querySelectorAll('#chores-list .list-group-item.active').length > 0;
        });

        eventBus.on('refetch-userchores', () => {
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
            activeElementId: '',
            choreBeingEdited: [],
            allUsersChores: {},
            generalNotice: '',
            modalNotice: '',
            modalErrors: [],
            userCardsHighlighted: false,
            choresAreHighlighted: false,
            assignmentsArePending: false,
            createChoreModalData: {
                chore: {
                    identifier: 'chore',
                    label: 'Chore',
                    type: 'text',
                    errors: '',
                    value: '',
                    callback: 'updateChoreFieldValue'
                },
                pointvalue: {
                    identifier: 'pointvalue',
                    label: 'Point value',
                    type: 'number',
                    errors: '',
                    value: '',
                    callback: 'updatePointvalueValue'
                }
            },
            editChoreModalData: {
                chore: {
                    identifier: 'chore',
                    label: 'Chore',
                    type: 'text',
                    errors: '',
                    value: '',
                    callback: 'updateChoreFieldValue'
                },
                pointvalue: {
                    identifier: 'pointvalue',
                    label: 'Point value',
                    type: 'number',
                    errors: '',
                    value: '',
                    callback: 'updatePointvalueValue'
                }
            }
        }
    },

    components: {
    GroupsSubView,
    ListGroup,
    ListItem,
    Cardgrid,
    Card,
    Modal,
    Notification,
    Button,
    FormInput,
    Icon
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

        updateChoreFieldValue(elValue) {
            this.createChoreModalData.chore.value = elValue;
            this.editChoreModalData.chore.value = elValue;
        },

        updatePointvalueValue(elValue) {
            this.createChoreModalData.pointvalue.value = elValue;
            this.editChoreModalData.pointvalue.value = elValue;
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
            let modalWindowUnderlay = modalwindow.parentNode;

            this.createChoreModalData.chore.value = '';
            this.createChoreModalData.pointvalue.value = '';

            this.activeElementId = '';

            modalwindow.classList.add('visible');
            modalwindow.classList.remove('invisible');
            modalWindowUnderlay.classList.add('visible');
            modalWindowUnderlay.classList.remove('invisible');
        },

        fireModalCloseEvent() {
            eventBus.emit('close-modal');
        },

        createChore(){
            let formEls = document.querySelectorAll('#createChoreModal input');
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
                
                eventBus.emit('close-modal');
            }).catch((error) => {

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

                eventBus.emit('close-modal');
            }).catch((error) => {

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
                        userChore.innerHTML = `<div class="border border-slate-400" style="display: flex; justify-content: space-between;"><div>${choreName}</div><div class="bg-red-600 w-10 h-10 px-3 py-2"><span class="fas fa-minus text-white discardAssignmentIcon" data-itemid="${choreId}"></span></div></div>`;
                    
                        recipientsListUl.append(userChore);
                
                        let choreDiscardButton = userChore.querySelector('.discardAssignmentIcon');
                        choreDiscardButton.addEventListener('click', this.discardAssignment);
                    }
                });

                this.assignmentsArePending = document.querySelectorAll('.card .assignment').length > 0;

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
            userCard.classList.toggle('bg-blue-400');

            this.userCardsHighlighted = !!document.querySelectorAll('.card.active').length;
            this.choresAreHighlighted = !!document.querySelectorAll('#chores-list .list-group-item.active').length;
        },

        dragEnter(e) {
            e.preventDefault();

            let userCard = e.toElement.closest('.card');

            userCard.classList.add('border-2');
            userCard.classList.add('border-green-600');
        },

        dragOver(e) {
            e.preventDefault();

            let userCard = e.toElement.closest('.card');

            userCard.classList.add('border-2');
            userCard.classList.add('border-green-600');
        },

        dragLeave(e) {
            let userCard = e.toElement;

            userCard.classList.remove('border-2');
            userCard.classList.remove('border-green-600');
        },

        drop(dragEvent) {
            dragEvent.stopPropagation();

            let card = dragEvent.toElement.closest('.card');
            let dropTarget = card.querySelector('.list-group');
            let userChoresList;
            let userChore;
            let droppedChoreName = dragEvent.dataTransfer.getData('text/html'); 
            let droppedChoreId = dragEvent.dataTransfer.getData('text/choreId');
            
            card.classList.remove('border-2');
            card.classList.remove('border-green-600');

            userChore = document.createElement('li');
            userChore.classList.add('assignment');
            userChore.classList.add('list-group-item');
            userChore.classList.add('border');
            userChore.classList.add('border-slate-400');
            userChore.innerHTML = `<div style="display: flex; justify-content: space-between;"><div class="pl-2 py-2">${droppedChoreName}</div><div class="bg-red-600 w-10 h-10 px-3 py-2"><span class="fas fa-minus text-white discardAssignmentIcon" data-itemid="${droppedChoreId}"></span></div></div>`;
            userChore.dataset.itemid = droppedChoreId;

            if (!this.assignmentsStarted(dropTarget)) {
                userChoresList = document.createElement('ul');
                userChoresList.className = 'list-group';
                card.appendChild(userChoresList);
            } else {
                userChoresList = dropTarget;
            }
            
            if(!this.userListHasChore(userChoresList, userChore)) {
                userChoresList.appendChild(userChore);
            }

            // Create an event listener to handle discarding assignments
            let choreDiscardButton = userChore.querySelector('.discardAssignmentIcon');
            choreDiscardButton.addEventListener('click', this.discardAssignment);

            this.assignmentsArePending = document.querySelectorAll('.card .assignment').length > 0;
            
            return false;
        },

        /**
         * Determine whether or not a chores list has been started on a user card
         * 
         * @param {*} dropTarget 
         */
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
                }
            });

            return hasChore;
        },

        discardAssignment(e) {
            e.stopPropagation();

            let assignment = e.target.closest('.assignment');
            let assignmentsList = assignment.parentNode;

            assignment.remove();

            this.assignmentsArePending = document.querySelectorAll('.card .assignment').length > 0;

            if(assignmentsList.querySelectorAll('li').length == 0) {
                assignmentsList.remove();
            }
        },

        editChore(choreId) {
            let modalwindow = document.getElementById("editChoreModal");
            let modalwindowUnderlay = modalwindow.parentNode;
            let choreBeingEdited = this.getChoreById(choreId);

            // doing it this way is pretty hacky. TODO - find a better way.
            document.querySelector('#editChoreForm input[name=chore]').value = choreBeingEdited.chore;
            document.querySelector('#editChoreForm input[name=pointvalue]').value = choreBeingEdited.pointvalue;

            this.editChoreModalData.chore.value = choreBeingEdited.chore;
            this.editChoreModalData.pointvalue.value = choreBeingEdited.pointvalue;

            this.activeElementId = choreId;

            eventBus.emit('chore-edit-click', choreBeingEdited);

            modalwindow.classList.add('visible');
            modalwindow.classList.remove('invisible');
            modalwindowUnderlay.classList.add('visible');
            modalwindowUnderlay.classList.remove('invisible');
        },

        getChoreById(id) {
            return this.choresList.filter((choreItem) => {
                return choreItem.id == id;
            })[0];
        },

        deleteChore(choreId) {
            axios.delete('/api/chores/' + choreId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                eventBus.emit('refetch-chores');
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
         * TODO - take another look at how the chore assignment is working to
         *        see if I can find a better solution.
         */
        deleteUserAssignment(e) {
            let userChoreId;
            let assignment;
            if (Number.isInteger(e)) {
                userChoreId = e;
            } else {
                assignment = e.target.closest('.list-group-item');
                
                userChoreId = assignment.dataset.itemid;
            }


            axios.delete('/api/user-chores/' + userChoreId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                assignment.remove();
                eventBus.emit('refetch-userchores');
            });
        },

        /**
         * Approves user assignment
         */
        approveUsersAssignment(userChore) {

            axios({
                method: 'put',
                url: '/api/users/' + userChore.user_id + '/chores/' + userChore.id,
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
                eventBus.emit('refetch-userchores');
            });
        }
    }
}
</script>