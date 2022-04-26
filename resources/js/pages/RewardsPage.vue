<template>
    <div>
        <user-status-bar></user-status-bar>
        <div class="sm:flex w-screen h-screen divide-x divide-solid divide-slate-100">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <Button v-if="userIsAdmin" colorClass="text-white" bgColorClass="bg-blue-600" callback="showRewardModal">New reward</Button>
                <h1>Rewards</h1>
                <cardgrid :cardCollectionData="rewards">
                    <card class="flex sm:block" v-for="cardData in cardCollectionData" :key="cardData.id" :cardData="cardData">
                        <img class="w-20 sm:w-full" v-for="image in cardData.images" :key="image.id" :src="image.path+image.filename" :alt="cardData.imgalt">
                        <div>
                            <h4>{{cardData.title}}</h4>
                            <h5><b>cost: </b>{{cardData.cost}}</h5>
                            <div v-if="cardData.actionIcons">
                                <icon v-for="actionicon in cardData.actionIcons" :key="actionicon.event" 
                                    v-bind:class="actionicon.class"
                                    v-bind:iconevent="actionicon.event" 
                                    v-bind:data-itemId="cardData.id">
                                </icon>
                            </div>
                        </div>
                    </card>
                </cardgrid>
                <modal id="rewardModal">
                    <template v-slot:header>
                        {{rewardModalTitle}}
                    </template>
                    <div class="modal-body">
                        <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                        <form id="rewardForm" name="rewardForm" enctype="multipart/form-data">
                            <div>
                                <label for="chore">Reward: <span v-if="modalErrors.reward">{{modalErrors.reward[0]}}</span></label>
                                <input id="reward" name="reward" type="text" v-bind:value="clickedCardData.reward">
                            </div>
                            <div>
                                <label for="pointvalue">Point value: <span v-if="modalErrors.pointvalue">{{modalErrors.pointvalue[0]}}</span></label>
                                <input id="pointvalue" name="pointvalue" type="number" v-bind:value="clickedCardData.point_value">
                            </div>
                            <div>
                                <label for="file-input">Image: <span v-if="modalErrors.file">{{modalErrors.file[0]}}</span></label>
                                <div id="image-wrapper" v-bind:class = "(editingReward)?'image-wrapper':''">
                                    <div v-if="editingReward" id="currentImage">
                                        <img v-bind:src="[clickedCardData.images[0].path + clickedCardData.images[0].filename]" v-bind:alt="clickedCardData.images[0].alt_text">
                                    </div>
                                    <input type="file" name="image" accept="image/*" id="file-input">
                                </div>
                            </div>
                        </form>
                    </div>
                    <template v-slot:footer>
                        <footer>
                            <Button v-if="!editingReward" callback="createReward" colorClass="text-white" bgColorClass="bg-blue-600">{{rewardModalPrimaryButtonLabel}}</button>
                            <Button v-if="editingReward" callback="updateReward" :args="clickedCardData" colorClass="text-white" bgColorClass="bg-blue-600">{{rewardModalPrimaryButtonLabel}}</button>
                            <Button data-dismiss="modal" callback="closeModal" colorClass="text-white" bgColorClass="bg-red-600">Close</button>
                        </footer>
                    </template>
                </modal>
                <modal id="rewardConfirmationModal">
                    <template v-slot:header>
                        Get reward
                    </template>
                    <template>
                        <p v-if="userHasEnoughPoints(clickedCardData)">Are you sure you want to spend {{clickedCardData.point_value}} points on {{clickedCardData.title}}?</p>
                        <p v-if="!userHasEnoughPoints(clickedCardData)">You don't have enough points for this reward</p>
                    </template>
                    <template v-slot:footer>
                        <footer>
                            <Button v-if="userHasEnoughPoints(clickedCardData)" callback="confirmPurchase" colorClass="text-white" bgColorClass="bg-blue-600">Spend points</Button>
                            <Button type="button" data-dismiss="modal" callback="closeModal" colorClass="text-white" bgColorClass="bg-red-600">Close</Button>
                        </footer>
                    </template>
                </modal>
            </div>
        </div>
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
import Notification from '../components/Notification.vue';
import Button from '../components/Button.vue';

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
            rewardModalPrimaryButtonLabel: "Create",
            modalNotice: '',
            modalErrors: []
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

        eventBus.$on('callback', (callback, args) => {
            var fn = window[callback];
    
            // 'this' is the VueComponent object
            if(args){
                this[callback](args);
            } else {
                this[callback]();
            }
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
        icon,
        Notification,
        Button
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

            RewardModal.classList.remove('invisible');
            RewardModal.classList.add('visible');
        },

        showEditRewardModal(reward) {
            let rewardModal = document.getElementById("rewardModal");

            this.clickedCardData = reward;

            this.modalNotice = '';
            this.modalErrors = [];

            this.rewardModalTitle = "Update Reward";

            this.rewardModalPrimaryButtonAction = "updateReward";
            this.rewardModalPrimaryButtonLabel = "Update";
            this.editingReward = true;

            rewardModal.classList.remove('invisible');
            rewardModal.classList.add('visible');
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
                this.fetchAllRewards();
                this.closeModal();
            }).catch((error) => {
                this.modalNotice = {
                    message: error.response.data.message,
                    status: error.response.status
                };

                this.clickedCardData.reward = rewardData['reward'];
                this.clickedCardData.point_value = rewardData['pointvalue'];

                this.modalErrors = error.response.data.errors;
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

                axios({
                    method: 'post',
                    url: '/api/rewards/' + reward.id,
                    data: rewardData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    this.fetchAllRewards();
                    this.closeModal();
                }).catch((error) => {
                    this.modalNotice = {
                        message: error.response.data.message,
                        status: error.response.status
                    };

                    this.clickedCardData.reward = rewardData.get('reward');
                    this.clickedCardData.point_value = rewardData.get('pointvalue');

                    this.modalErrors = error.response.data.errors;
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

            modalwindow.classList.add('visible');
            modalwindow.classList.remove('invisible');
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