<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <h1>Rewards</h1>
        <cardgrid :cardCollectionData="rewards"></cardgrid>
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
import Appmenu from './AppMenu.vue';
import UserStatusBar from './UserStatusBar.vue';
import Cardgrid from './Cardgrid.vue';
import eventBus from '../eventBus';
import Modal from './Modal.vue';

export default {
    data() {
        return {
            rewards: [],
            clickedCardData: []
        };
    },

    created() {
        eventBus.$on("reward-card-click", (cardData) => {
            this.clickedCardData = cardData;
            this.showRewardConfirmationModal();
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
                });

                this.rewards = rewardData;
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