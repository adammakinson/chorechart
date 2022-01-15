<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <hr>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr v-bind:id="row.id">
                    <td>{{row.chore}}</td>
                    <td>{{row.pointvalue}}</td>
                    <td>
                        <div class="actionsContainer">
                            <span v-if="row.submittable"
                                v-on:click="handleCheckClick" 
                                v-bind:class="[ getChoreRowCheckboxColorClass(row), 'fas fa-check']" 
                                v-bind:data-choreid="row.id">
                            </span>
                            <span v-if="choreIsFinished(row)" class="text-success">Complete</span>
                        </div>
                    </td>
                </tr>
            </template>
        </datatable>
    </div>
</template>

<script>
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
        UserStatusBar,
        Appmenu
    },

    mounted() {

        if (this.$store.getters.getUserAuthToken) {
            this.fetchChoresCollection();

            this.fetchUsersTransactions();
        } else {

            this.$router.push('login');
        }
    },

    methods: {
        fetchChoresCollection() {
            let user = this.$store.getters.getUser;

            axios.get('/api/user-chores/' + user.id, {
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

        getChoreRowCheckboxColorClass(row) {
            var colorClass = 'text-secondary';

            if (row.pending) {
                colorClass = 'text-warning';
            }

            if (row.inspection_passed) {
                colorClass = 'text-success';
            }

            return colorClass;
        },

        processFetchedChores(data) {
            data.forEach((row) => {

                row.submittable = false;

                if(!this.choreIsFinished(row)) {
                    if (this.choreIsSelfAssigned(row) || this.choreIsReadyForInspection(row)){
                        row.submittable = true;
                    }
                }


            });

            return data;
        },

        choreIsSelfAssigned(row) {
            return row.assigner_id && row.user_id && row.assigner_id == row.user_id;
        },

        choreIsReadyForInspection(row) {
            return !!row.inspection_ready;
        },

        choreIsFinished(row) {
            return !!row.inspection_passed;
        },

        setupTableData(data) {
            
            this.columns = [
                {label: 'chore', field: 'chore'}
            ];
            this.columns.push({label: 'points', field: 'pointvalue'});

            this.rows = data;
        },


        findChoreById(allChores, choreId) {
            return allChores.find(chore => chore.id == choreId);
        },


        handleCheckClick(el) {
            let choreId = el.target.dataset.choreid;
            let allChores = this.chores;
            let choreBeingEdited = this.findChoreById(allChores, choreId);
            let choreData = {
                id: choreBeingEdited.id,
                userId: choreBeingEdited.user_id,
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
            if (!choreBeingEdited.points_awarded == '1') {
                axios({
                    method: 'put',
                    url: '/api/users/' + choreBeingEdited.user_id + '/chores/' + choreBeingEdited.id,
                    data: choreData,
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
                            this.$store.commit('setUserTransactions', response.data);
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

    }
};
</script>