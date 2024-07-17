<template>
    <div class="w-full max-w-full h-screen">
        <title-bar></title-bar>
        <div class="grid transition-all duration-500 ease-in-out">
            <appmenu></appmenu>
            <div class="w-full max-w-[960px] m-auto">
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
            chores: [],
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
         * Fetch data based on a passed in route
         */
        async getData(route) {
            let payload = await axios.get(route, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            });

            return payload.data;
        },


        /**
         * Fetch chores pertinent to the logged in user. If the user is an admin,
         * fetch all chores; otherwise only fetch the chores belonging to the user.
         */
        async fetchChoresCollection() {
            let user = this.$store.getters.getUser;
            let chores = [];
            let myChores = [];
            let choresToReview = [];
            let route = '/api/user-chores/' + user.id;

            if (this.userIsAdmin) {
                route = '/api/user-chores';
            }

            chores = await this.getData(route);

            this.chores = this.myChores = chores;
            
            if (this.userIsAdmin) {

                chores.forEach((chore) => {
                    if (chore.user_id == user.id) {
                        myChores.push(chore);
                    } else {
                        choresToReview.push(chore);
                    }
                });
                
                this.chores = chores;
                this.myChores = myChores;
                this.choresToReview = choresToReview;
            }
        },


        /**
         * Fetch user transactions from the backend. Upon successful
         * response, call updateUserTransactions passing in the transactions data
         */
        async fetchUsersTransactions() {
            let user = this.$store.getters.getUser;
            let userTransactions = await this.getData(`/api/users/${user.id}/transactions`);

            this.updateUserTransactions(userTransactions);
        },

        
        /**
         * Define the color of the checkbox for a chore. The default is gray. A pending chore is orange and a completed one is green.
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
         * 
         * @param {object} row
         */
        choreIsFinished(row) {
            return !!row.inspection_passed;
        },

        
        /**
         * Return chores where the user id and chore id match the passed in userId and choreId
         * 
         * @param {array} allChores
         * @param {int} userId
         * @param {int} choreId
         */
        findUserChoreByChoreId(allChores, userId, choreId) {
            return allChores.find(chore => chore.chore_id == choreId && chore.user_id == userId);
        },

        
        /**
         * Handles the click of a chore checkbox.
         * 
         * Gets the user and checks if the user is an admin. If they are an admin, get the user id from the clicked
         * element. If points have NOT been awarded for the chore, send a request to the server for the user and chore
         * to mark the chore as ready for inspection. If the response indicates points have been awarded for the chore,
         * make a call to create a transaction for the user/chore with the response data.
         * 
         * @param {*} el the chore element
         */
        handleCheckClick(el) {
            let choreId = el.target.dataset.choreid;
            let user = this.$store.getters.getUser;
            let userId = user.id;

            let allChores = this.chores;
            let choreBeingEdited = this.findUserChoreByChoreId(allChores, userId, choreId);
            
            let choreData = {
                chore_id: choreBeingEdited.chore_id,
                userId: choreBeingEdited.user_id,
                inspection_ready: true
            };
            
            if (this.userIsAdmin) {
                userId = el.target.dataset.userid;
            }

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