<template>
    <div class="w-full max-w-full h-screen">
        <title-bar></title-bar>
        <div class="grid transition-all duration-500 ease-in-out">
            <appmenu></appmenu>
            <div class="w-full">
                <div v-if="!myChores || myChores.length == 0" class="grid h-screen justify-center items-center">
                    <div class="w-96 p-4 h-96">
                        <h2 v-if="!userIsAdmin" class="text-4xl text-center">You don't have any chores assigned to you. Check back later.</h2>
                        <h2 v-if="userIsAdmin" class="text-4xl text-center">You don't have any chores assigned to you. Assign one now!</h2>
                    </div>
                </div>
                <h2 v-if="myChores.length > 0" class="pl-5 pt-5">My chores</h2>
                <ListGroup v-if="myChores.length > 0" :listId="'my-chores-list'" class="px-5">
                    <list-item v-for="choreData in myChores" :key="choreData.id" :listItem="choreData" :draggable="false" :selectable="false" class="flex border border-slate-400">
                            <div class="grow h-12 p-1.5 leading-10">
                                {{choreData.chore}} <span class="text-green-600">{{choreData.pointvalue}}P</span>
                            </div>
                        <template v-slot:actions>
                            <div class="grow-0">

                                <span v-if="!choreIsFinished(choreData)"
                                    v-on:click="handleCheckClick" 
                                    v-bind:class="[ getChoreRowCheckboxColorClass(choreData), 'fas fa-check-square fa-2x']" 
                                    v-bind:data-choreid="choreData.chore_id"
                                    v-bind:data-userid="choreData.user_id"
                                    class="pr-2 w-full h-full text-center">
                                </span>
                                <span v-if="choreIsFinished(choreData)" class="text-yellow-500 self-center pr-2 w-full h-full text-center fas fa-trophy fa-lg"></span>
                            </div>
                        </template>
                    </list-item>
                </ListGroup>
                <h2 v-if="choresToReview.length > 0" class="pl-5 pt-5">Chores to review</h2>
                <ListGroup v-if="choresToReview.length > 0" :listId="'my-chores-list'" class="px-5 pb-5">
                    <list-item v-for="choreData in choresToReview" :key="choreData.id" :listItem="choreData" :draggable="false" :selectable="false" class="flex border border-slate-400">
                            <div class="grow h-12 p-1.5 leading-10">
                                {{choreData.chore}} <span class="text-green-600">{{choreData.pointvalue}}P</span>
                            </div>
                        <template v-slot:actions>
                            <div class="grow-0">

                                <span v-if="!choreIsFinished(choreData)"
                                    v-on:click="handleCheckClick" 
                                    v-bind:class="[ getChoreRowCheckboxColorClass(choreData), 'fas fa-check-square fa-2x']" 
                                    v-bind:data-choreid="choreData.chore_id"
                                    v-bind:data-userid="choreData.user_id"
                                    class="pr-2 w-full h-full text-center">
                                </span>
                                <span v-if="choreIsFinished(choreData)" class="text-yellow-500 self-center pr-2 w-full h-full text-center fas fa-trophy fa-lg"></span>
                            </div>
                        </template>
                    </list-item>
                </ListGroup>
            </div>
        </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
import Appmenu from '../components/AppMenu.vue';
import ListItem from "../components/ListItem.vue";
import ListGroup from "../components/ListGroup.vue";
import TitleBar from '../components/TitleBar.vue';

export default {
    props: ['id'],

    created() {
        eventBus.on("mobileMainMenuIconClicked", () => {

            this.mainMenuIsOpen = !this.mainMenuIsOpen;

        });

        this.mainMenuIsOpen = false;
    },

    data() {
        return {
            myChores: [],
            choresToReview: [],
            rows: [],
            choreFieldValue: '',
            pointFieldValue: '',
            activeElementId: '',
            assignee: '',
            userIsAdmin: false,
            allUsers: [],
            mainMenuIsOpen: false
        }
    },

    computed: {
        windowWidth() {
            return this.$store.getters.getWindowWidth;
        }
    },

    components: {
        Appmenu,
        ListItem,
        ListGroup,
        TitleBar
    },

    mounted() {
        /**
         * When the component is mounted, if the user is logged in, set the
         * user type, fetch the chores collection and fetch the users transactions
         */
        if (this.$store.getters.getUserAuthToken) {

            this.userIsAdmin = this.$store.getters.userIsAdmin;

            this.fetchChoresCollection();

            this.fetchUsersTransactions();
        } else {

            this.$router.push('login');
        }
    },

    methods: {

        /**
         * Fetch chores associated with a user from the /api/user-chores route
         * and store them in data.
         */
        fetchChoresCollection() {
            let user = this.$store.getters.getUser;
            let myChores = [];
            let choresToReview = [];

            if (this.userIsAdmin) {

                axios.get('/api/user-chores', {
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    // separate the chores by ones belonging to currently logged in user and ones not belonging to them
                    let allChores = response.data;

                    allChores.forEach((chore) => {
                        if (chore.user_id == user.id) {
                            myChores.push(chore);
                        } else {
                            choresToReview.push(chore);
                        }
                    });
                    
                    this.myChores = myChores;
                    this.choresToReview = choresToReview;
                });
            } else {
                axios.get('/api/user-chores/' + user.id, {
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    
                    this.myChores = response.data;
                });
            }
        },

        /**
         * Fetch user transactions from the backend. Upon successful
         * response, call updateUserTransactions passing in the transactions data
         */
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

        /**
         * Define the color of the checkbox for a chore.
         * The default is gray. A pending chore is orange
         * and a completed one is green.
         */
        getChoreRowCheckboxColorClass(row) {
            var colorClass = 'text-stone-400';

            if (row.pending) {
                colorClass = 'text-orange-500';
            }

            if (row.inspection_passed) {
                colorClass = 'text-green-600';
            }

            return colorClass;
        },

        /**
         * Returns boolean value of inspection_passed on the row
         */
        choreIsFinished(row) {
            return !!row.inspection_passed;
        },

        /**
         * Return chores where the user id and chore id match the passed
         * in userId and choreId
         * @param {aray} allChores 
         * @param {int} userId 
         * @param {int} choreId 
         */
        findUserChoreByChoreId(allChores, userId, choreId) {
            return allChores.find(chore => chore.id == choreId && chore.user_id == userId);
        },

        /**
         * 
         * @param {*} el the chore element
         */
        handleCheckClick(el) {
            let choreId = el.target.dataset.choreid;
            let user = this.$store.getters.getUser;
            let userId = user.id;
            let allChores = this.chores;
            let choreBeingEdited;

            if (this.userIsAdmin) {
                userId = el.target.dataset.userid;
            }

            choreBeingEdited = this.findUserChoreByChoreId(allChores, userId, choreId);

            // The way the update function works right now, I'm not even using the data payload.
            let choreData = {
                chore_id: choreBeingEdited.chore_id,
                userId: choreBeingEdited.user_id,
                inspection_ready: true
            };

            /**
             * If no points have been awarded to the chore,
             * send the chore to declare it ready for inspection
             */
            if (!choreBeingEdited.points_awarded == '1') {
                axios({
                    method: 'put',
                    url: '/api/users/' + choreBeingEdited.user_id + '/chores/' + choreBeingEdited.id,
                    data: choreData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {

                    /**
                     * If the chore has been approved, add it to the transactions
                     * table with a type of 'choreCompletion' and update the
                     * user transactions in the store.
                     */
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

        /**
         * Save the transactions to the vuex store
         */
        updateUserTransactions(transactions) {
            this.$store.commit('setUserTransactions', transactions);
        },
    }
};
</script>