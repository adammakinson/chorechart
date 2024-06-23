<template>
    <div class="w-screen max-w-full">
        <div class="grid min-h-screen transition-all duration-500 ease-in-out" :class="[ mainMenuIsOpen ? 'grid-cols-menuexpanded' : 'grid-cols-menucollapsed' ]">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <div class="flex justify-between">
                    
                    <Button v-if="userIsAdmin" class="" colorClass="text-white" bgColorClass="bg-blue-600" callback="showRewardModal">New reward</Button>
                    <p class="p-1.5 text-xl">{{this.$store.getters.getUserPoints}} points</p>
                </div>
                <div v-if="!rewards || rewards.length == 0" class="grid justify-center items-center">
                    <div class="w-96 h-96">
                        <h2 v-if="!userIsAdmin" class="text-5xl">No rewards have been created. Ask an admin to create one!</h2>
                        <h2 v-if="userIsAdmin" class="text-5xl">No rewards have been created. Create one now!</h2>
                    </div>
                </div>
                <cardgrid :cardCollectionData="rewards" class="mt-4">
                    <card v-for="cardData in cardCollectionData" :key="cardData.id" :cardData="cardData">
                        <template v-slot:header>
                            <h4 class="p-4">{{cardData.title}} - {{cardData.cost}}P</h4>
                        </template>
                        <div class="w-1/3 sm:w-full aspect-square overflow-hidden border border-slate-100 border-rounded border-2">
                            <img class="sm:w-full" v-for="image in cardData.images" :key="image.id" :src="image.path+image.filename" :alt="cardData.imgalt">
                        </div>
                        <div v-if="userIsAdmin" class="flex flex-col sm:flex-row sm:items-stretch gap-4">
                            <Button v-if="userIsAdmin" colorClass="text-white" bgColorClass="bg-blue-600" widthClass="w-16 sm:w-1/2" callback="editReward" :args="cardData.id">
                                <icon class="fas fa-edit"></icon>
                            </Button>
                            <Button v-if="userIsAdmin" colorClass="text-white" bgColorClass="bg-red-600" widthClass="w-16 sm:w-1/2" callback="deleteReward" :args="cardData.id">
                                <icon class="fas fa-trash"></icon>
                            </Button>
                        </div>
                    </card>
                </cardgrid>
                <modal id="rewardModal">
                    <template v-slot:header>
                        {{rewardModalTitle}}
                    </template>
                    <div class="modal-body">
                        <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                        <form id="rewardForm" name="rewardForm" enctype="multipart/form-data" class="">
                            <FormInput v-for="formField in rewardModalFormData" :key="formField.identifier"
                                :identifier="formField.identifier"
                                :type="formField.type"
                                :elementLabel="formField.label"
                                :errors="formField.errors"
                                :value="formField.value"
                                :callback="formField.callback"
                            ></FormInput>
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
                            <Button v-if="!editingReward" callback="createReward" colorClass="text-white" marginClass="mr-4" bgColorClass="bg-blue-600">{{rewardModalPrimaryButtonLabel}}</button>
                            <Button v-if="editingReward" callback="updateReward" :args="clickedCardData" colorClass="text-white" marginClass="mr-4" bgColorClass="bg-blue-600">{{rewardModalPrimaryButtonLabel}}</button>
                            <Button data-dismiss="modal" callback="closeModal" colorClass="text-white" marginClass="mr-4" bgColorClass="bg-red-600">Close</button>
                        </footer>
                    </template>
                </modal>
                <modal id="rewardConfirmationModal">
                    <template v-slot:header>
                        Get reward
                    </template>
                    <div>
                        <p v-if="userHasEnoughPoints(clickedCardData)">Are you sure you want to spend {{clickedCardData.point_value}} points on {{clickedCardData.title}}?</p>
                        <p v-if="!userHasEnoughPoints(clickedCardData)">You don't have enough points for this reward</p>
                    </div>
                    <template v-slot:footer>
                        <footer>
                            <Button v-if="userHasEnoughPoints(clickedCardData)" callback="confirmPurchase" colorClass="text-white" marginClass="mr-4" bgColorClass="bg-blue-600">Spend points</Button>
                            <Button type="button" data-dismiss="modal" callback="closeModal" colorClass="text-white" marginClass="mr-4" bgColorClass="bg-red-600">Close</Button>
                        </footer>
                    </template>
                </modal>
            </div>
        </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
import icon from '../components/Icon';
import Card from '../components/Card.vue';
import Modal from '../components/Modal.vue';
import Button from '../components/Button.vue';
import Appmenu from '../components/AppMenu.vue';
import Cardgrid from '../components/Cardgrid.vue';
import FormInput from '../components/FormInput.vue';
import Notification from '../components/Notification.vue';
import UserStatusBar from '../components/UserStatusBar.vue';

export default {
    data() {
        return {
            rewards: [],
            cardCollectionData: [],
            clickedCardData: [],
            user: [],
            userIsAdmin: false,
            userTransactions: [],
            editingReward: false,
            rewardModalTitle: "Create Reward",
            rewardModalPrimaryButtonAction: "createReward",
            rewardModalPrimaryButtonLabel: "Create",
            modalNotice: '',
            modalErrors: [],
            mainMenuIsOpen: false,
            rewardModalFormData: {
                reward: {
                    identifier: 'reward',
                    label: 'Reward',
                    type: 'text',
                    errors: '',
                    value: '',
                    callback: 'updateRewardFieldValue'
                },
                pointvalue: {
                    identifier: 'pointvalue',
                    label: 'Point value',
                    type: 'number',
                    errors: '',
                    value: '',
                    callback: 'updatePointvalueFieldValue'
                }
            }
        };
    },

    computed: {
        windowWidth() {
            return this.$store.getters.getWindowWidth;
        }
    },

    created() {
        eventBus.on("reward-card-click", (cardData) => {
            this.clickedCardData = cardData;
            this.showRewardConfirmationModal();
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

        eventBus.on("mobileMainMenuIconClicked", () => {
            this.mainMenuIsOpen = !this.mainMenuIsOpen;
        });

        if (this.windowWidth < 640) {
            this.mainMenuIsOpen = false;
        } else {
            this.mainMenuIsOpen = true;
        }

        window.matchMedia("(orientation: portrait)").addEventListener("change", e => {
            const portrait = e.matches;

            if (this.windowWidth < 640) {
                this.mainMenuIsOpen = false;
            } else {
                this.mainMenuIsOpen = true;
            }
        });
    },

    mounted() {
        if (this.$store.getters.getUserAuthToken) {
            
            if (!this.$store.getters.getUserTransactions || this.$store.getters.getUserTransactions.length == 0) {
                this.fetchUsersTransactions();
            }

            this.user = this.$store.getters.getUser;
            this.userIsAdmin = this.$store.getters.userIsAdmin;
            this.userTransactions = this.$store.getters.getUserTransactions;


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
        Button,
        FormInput
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

                    // reward.contentItems = [];
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

        editReward(rewardId) {
            let reward = this.rewards.filter((reward) => reward.id == rewardId)[0];

            this.showEditRewardModal(reward);
        },

        showRewardModal() {
            let RewardModal = document.getElementById("rewardModal");
            let RewardModalUnderlay = RewardModal.parentNode;

            this.clickedCardData = [];
            this.modalNotice = '';
            this.rewardModalFormData.reward.errors = '';
            this.rewardModalFormData.pointvalue.errors = '';
            
            // hack, not the way to do it...
            document.getElementById('reward').value = '';
            document.getElementById('pointvalue').value = '';
            this.rewardModalFormData.reward.value = '';
            this.rewardModalFormData.pointvalue.value = '';

            this.rewardModalTitle = "Create Reward";
            this.rewardModalPrimaryButtonAction = "createReward";
            this.rewardModalPrimaryButtonLabel = "Create";
            this.editingReward = false;

            RewardModal.classList.remove('invisible');
            RewardModal.classList.add('visible');
            RewardModalUnderlay.classList.remove('invisible');
            RewardModalUnderlay.classList.add('visible');
        },

        showEditRewardModal(reward) {
            let RewardModal = document.getElementById("rewardModal");
            let RewardModalUnderlay = RewardModal.parentNode;

            this.clickedCardData = reward;

            // Pretty hacky way to do this. See if I can figure out better way
            document.getElementById('reward').value = reward.reward;
            document.getElementById('pointvalue').value = reward.cost;
            this.rewardModalFormData.reward.value = reward.reward;
            this.rewardModalFormData.pointvalue.value = reward.cost;

            this.modalNotice = '';
            this.modalErrors = [];

            this.rewardModalTitle = "Update Reward";

            this.rewardModalPrimaryButtonAction = "updateReward";
            this.rewardModalPrimaryButtonLabel = "Update";
            this.editingReward = true;

            rewardModal.classList.remove('invisible');
            rewardModal.classList.add('visible');
            RewardModalUnderlay.classList.remove('invisible');
            RewardModalUnderlay.classList.add('visible');
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

                for (const property in error.response.data.errors) {
                    if (property === 'message') {
                        continue;
                    }

                    this.rewardModalFormData[property].errors = error.response.data.errors[property];
                }
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
                    this.fetchAllRewards();
                    this.closeModal();
                });
            }
        },

        showRewardConfirmationModal(e) {
            let modalwindow = document.getElementById("rewardConfirmationModal");
            let modalUnderlay = modalwindow.parentNode;
            let card = e.target.closest('.card');
            let rewardId = card.dataset.itemid;
            let reward;

            this.cardCollectionData.forEach((card) => {
                if(card.id == rewardId){
                    reward = card;
                }
            });

            this.clickedCardData = reward;

            modalwindow.classList.add('visible');
            modalwindow.classList.remove('invisible');
            modalUnderlay.classList.add('visible');
            modalUnderlay.classList.remove('invisible');
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
            eventBus.emit('close-modal');
        },

        updateRewardFieldValue(elValue) {
            this.rewardModalFormData.reward.value = elValue;
        },

        updatePointvalueFieldValue(elValue) {
            this.rewardModalFormData.pointvalue.value = elValue;
        },

        clickMobileMainMenu() {
            eventBus.emit('mobileMainMenuIconClicked', this);
        },

        fetchUsersTransactions() {
            let user = this.$store.getters.getUser;

            axios.get('/api/users/' + user.id + '/transactions', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                this.$store.commit('setUserTransactions', response.data);
            });
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