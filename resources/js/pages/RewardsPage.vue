<template>
    <div class="container-fluid">
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <button v-if="userIsAdmin" class="btn btn-primary" data-toggle="modal" data-target="#rewardModal" v-on:click="showRewardModal">Create a reward</button>
        <h1>Rewards</h1>
        <cardgrid :cardCollectionData="rewards">
            <card v-for="cardData in cardCollectionData" :key="cardData.id" :cardData="cardData">
                <img v-for="image in cardData.images" :key="image.id" :src="image.path+image.filename" :alt="cardData.imgalt">
                <h4 class="text-dark">{{cardData.title}}</h4>
                <h5><b>cost: </b>{{cardData.cost}}</h5>
                <div v-if="cardData.actionIcons" class="flex-column m-3">
                    <icon v-for="actionicon in cardData.actionIcons" :key="actionicon.event" 
                        v-bind:class="actionicon.class" 
                        v-bind:iconevent="actionicon.event" 
                        v-bind:data-itemId="cardData.id">
                    </icon>
                </div>
            </card>
        </cardgrid>
        <modal id="rewardModal">
            <template v-slot:header>
                {{rewardModalTitle}}
            </template>
            <div class="modal-body">
                <form id="rewardForm" name="rewardForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="chore">Reward:</label>
                        <input id="reward" name="reward" class="form-control" type="text" v-bind:value="clickedCardData.reward">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" name="pointvalue" class="form-control" type="number" v-bind:value="clickedCardData.point_value">
                    </div>
                    <div class="form-group">
                        <label for="file-input">Image:</label>
                        <div id="image-wrapper" v-bind:class = "(editingReward)?'image-wrapper':''">
                            <div v-if="editingReward" id="currentImage">
                                <img v-bind:src="[clickedCardData.images[0].path + clickedCardData.images[0].filename]" v-bind:alt="clickedCardData.images[0].alt_text">
                            </div>
                            <input type="file" accept="image/*" class="form-control" id="file-input">
                        </div>
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button v-if="!editingReward" type="button" class="btn btn-primary" v-on:click.prevent="createReward">{{rewardModalPrimaryButtonLabel}}</button>
                    <button v-if="editingReward" type="button" class="btn btn-primary" v-on:click.prevent="updateReward(clickedCardData)">{{rewardModalPrimaryButtonLabel}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="closeModal">Close</button>
                </footer>
            </template>
        </modal>
        <modal id="rewardConfirmationModal">
            <template v-slot:header>
                <p v-if="userHasEnoughPoints(clickedCardData)">Are you sure you want to spend {{clickedCardData.point_value}} points on {{clickedCardData.title}}?</p>
                <p v-if="!userHasEnoughPoints(clickedCardData)">You don't have enough points for this reward</p>
            </template>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button v-if="userHasEnoughPoints(clickedCardData)" type="button" class="btn btn-primary" v-on:click.prevent="confirmPurchase">Spend points</button>
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
import Card from '../components/Card.vue';
import eventBus from '../eventBus';
import Modal from '../components/Modal.vue';
import icon from '../components/Icon';

export default {
    data() {
        return {
            rewards: [],
            cardCollectionData: [],
            clickedCardData: [],
            userIsAdmin: false,
            editingReward: false,
            rewardModalTitle: "Create Reward",
            rewardModalPrimaryButtonAction: "createReward",
            rewardModalPrimaryButtonLabel: "Create"
        };
    },

    created() {
        eventBus.$on("reward-card-click", (cardData) => {
            this.clickedCardData = cardData;
            this.showRewardConfirmationModal();
        });

        eventBus.$on("editReward", (iconCmp) => {
            let rewardId = iconCmp.$parent.$el.dataset.itemid;
            let reward = this.rewards.filter((reward) => reward.id == rewardId)[0];

            this.showEditRewardModal(reward);
        });
        
        eventBus.$on("deleteReward", (iconCmp) => {
            let rewardId = iconCmp.$parent.$el.dataset.itemid;

            this.deleteReward(rewardId);
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
        Card,
        UserStatusBar,
        Modal,
        icon
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
                    reward.eventToFire = 'reward-card-click';

                    reward.eventsObject = {
                        click: this.showRewardConfirmationModal
                    };

                    reward.contentItems = [];
                    
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

                // TODO - store rewards in vuex on fetch, update single record on update, remove on delete.
                // maybe have some sort of mechanism to invalidate the vuex "cache"
                this.cardCollectionData = rewardData;
                this.rewards = rewardData;
            });
        },

        userHasEnoughPoints(clickedCardData) {
            return this.$store.getters.getUserPoints >= clickedCardData.point_value;
        },

        showRewardModal() {
            let RewardModal = document.getElementById("rewardModal");

            this.clickedCardData = [];
            
            this.rewardModalTitle = "Create Reward";
            this.rewardModalPrimaryButtonAction = "createReward";
            this.rewardModalPrimaryButtonLabel = "Create";
            this.editingReward = false;

            RewardModal.style.display = 'block';
        },

        showEditRewardModal(reward) {
            let rewardModal = document.getElementById("rewardModal");

            this.clickedCardData = reward;

            this.rewardModalTitle = "Update Reward";

            this.rewardModalPrimaryButtonAction = "updateReward";
            this.rewardModalPrimaryButtonLabel = "Update";
            this.editingReward = true;

            rewardModal.style.display = 'block';

            console.log(reward);
        },

        createReward() {
            let rewardData = new FormData();
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
                // TODO -  this should return a 200 OK or an 403 Forbidden
                console.log(response);
                this.fetchAllRewards();
                this.closeModal();
            });
        },

        updateReward(reward) {
            if(this.userIsAdmin) {
                let rewardData = new FormData();
                let rewardField = document.querySelector('#reward');
                let pointvalueField = document.querySelector('#pointvalue');
                let filesInput = document.querySelector('#file-input');

                rewardData.append('reward', rewardField.value);
                rewardData.append('pointvalue', pointvalueField.value);
                
                /**
                 * This has to be done because PHP won't process multipart/formdata
                 * requests unless the HTTP verb is POST. We have to use laravels
                 * _method spoofing to get the job done. Kind of ugly, but works
                 */
                rewardData.append('_method', 'PUT');

                if(filesInput.files) {
                    rewardData.append('file', filesInput.files[0]);
                }

                console.log(rewardData.get('reward'));
                console.log(rewardData.get('pointvalue'));

                axios({
                    method: 'post',
                    url: '/api/rewards/' + reward.id,
                    data: rewardData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    // TODO -  this should return a 200 OK or an 403 Forbidden
                    console.log(response);
                    this.fetchAllRewards();
                    this.closeModal();
                });
            }
        },
        
        deleteReward(rewardId) {
            if(this.userIsAdmin) {
                axios({
                    method: 'delete',
                    url: '/api/rewards/' + rewardId,
                    data: rewardId,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    // TODO -  this should return a 200 OK or an 403 Forbidden
                    console.log(response);
                    this.fetchAllRewards();
                    this.closeModal();
                });
            }
        },

        showRewardConfirmationModal(e) {
            let modalwindow = document.getElementById("rewardConfirmationModal");
            let card = e.target.closest('.card');
            let rewardId = card.dataset.itemid;
            let reward;

            this.cardCollectionData.forEach((card) => {
                console.log(card);

                if(card.id == rewardId){
                    reward = card;
                }
            });

            this.clickedCardData = reward;

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

<style scoped>
    #currentImage img {
        width: 100%;
    }

    .image-wrapper {
        display: grid;
        grid-template-columns: 100px 1fr;
    }

    #file-input {
        align-self: center;
    }
</style>