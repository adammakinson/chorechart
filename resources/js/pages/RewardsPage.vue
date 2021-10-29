<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <button v-if="userIsAdmin" class="btn btn-primary" data-toggle="modal" data-target="#createRewardModal" v-on:click="showCreateRewardModal">Create a reward</button>
        <h1>Rewards</h1>
        <cardgrid :cardCollectionData="rewards"></cardgrid>
        <modal id="createRewardModal">
            <template v-slot:header>
                Create a Reward
            </template>
            <div class="modal-body">
                <form id="createRewardForm" name="createRewardForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="chore">Reward:</label>
                        <input id="reward" name="reward" class="form-control" type="text" v-bind:value="rewardName">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" name="pointvalue" class="form-control" type="number" v-bind:value="rewardPointValue">
                    </div>
                    <div class="form-group">
                        <label for="assignedto">Image:</label>
                        <input type="file" accept="image/*" class="form-control" id="file-input">
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="createReward">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="closeModal">Close</button>
                </footer>
            </template>
        </modal>
        <modal id="rewardConfirmationModal">
            <template v-slot:header>
                Are you sure you want to spend {{clickedCardData.point_value}} points on {{clickedCardData.title}}?
            </template>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="confirmPurchase">Spend points</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="closeModal">Close</button>
                </footer>
            </template>
        </modal>
    </div>
</template>

<script>
import Appmenu from '../components/AppMenu.vue';
import UserStatusBar from '../components/UserStatusBar.vue';
import Cardgrid from '../components/Cardgrid.vue';
import eventBus from '../eventBus';
import Modal from '../components/Modal.vue';

export default {
    data() {
        return {
            rewards: [],
            clickedCardData: [],
            userIsAdmin: false
        };
    },

    created() {
        eventBus.$on("reward-card-click", (cardData) => {
            this.clickedCardData = cardData;
            this.showRewardConfirmationModal();
        });

        eventBus.$on("editReward", (iconCmp) => {
            console.log(iconCmp);
            console.log("editing a reward");
        });
        
        eventBus.$on("deleteReward", (iconCmp) => {
            console.log(iconCmp);
            console.log("deleting a reward");
        });
    },

    mounted() {
        if (this.$store.getters.getUserAuthToken) {
            
            this.userIsAdmin = this.$store.getters.userIsAdmin;

            this.fetchAllRewards();
        } else {
            this.$router.push('login');
        }
    },

    components: {
        Appmenu,
        Cardgrid,
        UserStatusBar,
        Modal
    },

    methods: {
        fetchAllRewards() {
            axios.get('/api/rewards', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                let rewardData = response.data;

                rewardData.forEach(reward => {
                    reward.title = reward.reward;
                    reward.cost = reward.point_value;
                    if (this.userIsAdmin) {
                        reward.actionIcons = [
                            {
                                "event": "editReward",
                                "class": "fas fa-edit text-info",
                                "visibleTo": [
                                    "admins"
                                ]
                            },
                            {
                                "event": "deleteReward",
                                "class": "fas fa-trash text-danger",
                                "visibleTo": [
                                    "admins"
                                ]
                            }
                        ];
                    }
                });

                this.rewards = rewardData;
            });
        },

        showCreateRewardModal() {
            let createRewardModal = document.getElementById("createRewardModal");

            createRewardModal.style.display = 'block';
        },

        createReward() {
            let rewardData = new FormData()
            let rewardField = document.querySelector('#reward');
            let pointvalueField = document.querySelector('#pointvalue');
            let filesInput = document.querySelector('#file-input');

            rewardData.append('reward', rewardField.value);
            rewardData.append('pointvalue', pointvalueField.value);
            rewardData.append('file', filesInput.files[0]);

            axios({
                method: 'post',
                url: '/api/rewards',
                data: rewardData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                console.log(response);
            });
        },

        showRewardConfirmationModal() {
            let modalwindow = document.getElementById("rewardConfirmationModal");

            modalwindow.style.display = 'block';
        },

        confirmPurchase() {
            let user = this.$store.getters.getUser;

            this.clickedCardData.transactionType = 'rewardPurchase';

            axios({
                method: 'post',
                url: '/api/users/' + user.id + '/transactions', 
                data: this.clickedCardData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.$store.commit('setUserTransactions', response.data);
                this.closeModal();
            });
        },

        closeModal() {
            eventBus.$emit('close-modal');
        }
    }
}
</script>