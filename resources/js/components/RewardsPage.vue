<template>
    <div>
        <appmenu></appmenu>
        <user-status-bar></user-status-bar>
        <h1>Hello rewards!</h1>
        <cardgrid :cardCollectionData="cardCollectionData"></cardgrid>
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
            ]
        };
    },

    components: {
        Appmenu,
        Cardgrid,
        UserStatusBar
    },

    methods: {}
}
</script>