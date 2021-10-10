<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <h1>Rewards</h1>
        <cardgrid :cardCollectionData="rewards"></cardgrid>
    </div>
</template>

<script>
import Appmenu from './AppMenu.vue';
import UserStatusBar from './UserStatusBar.vue';
import Cardgrid from './Cardgrid.vue';

export default {
    data() {
        return {
            rewards: []
        };
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
        UserStatusBar
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
        }
    }
}
</script>