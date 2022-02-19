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
                            <span v-if="!choreIsFinished(row)"
                                v-on:click="handleCheckClick" 
                                v-bind:class="[ getChoreRowCheckboxColorClass(row), 'fas fa-check']" 
                                v-bind:data-choreid="row.chore_id">
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
                
                this.chores = response.data;
                
                this.setupTableData(this.chores);
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

        
        findUserChoreByChoreId(allChores, userId, choreId) {
            return allChores.find(chore => chore.chore_id == choreId && chore.user_id == userId);
        },

        handleCheckClick(el) {
            let choreId = el.target.dataset.choreid;
            let user = this.$store.getters.getUser;
            let allChores = this.chores;
            let choreBeingEdited = this.findUserChoreByChoreId(allChores, user.id, choreId);

            // The way the update function works right now, I'm not even using the data payload.
            let choreData = {
                chore_id: choreBeingEdited.chore_id,
                userId: choreBeingEdited.user_id,
                inspection_ready: true
            };

            // Hrm... do we really need user.id in the route???
            if (!choreBeingEdited.points_awarded == '1') {
                axios({
                    method: 'put',
                    url: '/api/users/' + choreBeingEdited.user_id + '/chores/' + choreBeingEdited.chore_id,
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
                            this.$store.commit('setUserTransactions', transactionResponse.data);
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