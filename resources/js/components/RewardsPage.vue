<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <h1>Hello rewards!</h1>
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
            cardCollectionData: [
                {
                    id: 1,
                    title: "30 Min Video game time",
                    body: "Thirty minutes worth of roblox time.",
                    image: '/images/30_game_time.svg',
                    imgalt: '30 minutes of video game time!',
                    cost: '30 points'
                },
                {
                    id: 2,
                    title: "30 Min episode",
                    body: "One episode or show lasting approximately 30 minutes.",
                    image: '/images/tv_time.svg',
                    imgalt: 'One 30 minute TV episode',
                    cost: '30 points'
                }
            ],

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