<template>
    <div class="w-full max-w-full flex justify-between border-b w-screen h-14">
        <div class="text-2xl h-10 ml-1.5 visible p-1.5 fas fa-bars" @click="clickMobileMainMenu"></div>
        <slot></slot>
        <p class="p-1.5 text-xl">{{this.$store.getters.getUserPoints}} pts</p>
    </div>
</template>

<script>
import eventBus from '../eventBus';

export default {

    data() {
        return {
            authenticatedUser: false,
            usersName: ''
        };
    },

    created() {
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
    },

    mounted() {
        this.authenticatedUser = !!this.$store.getters.getUserAuthToken;

        if (!this.$store.getters.getUserTransactions || this.$store.getters.getUserTransactions.length == 0) {
            this.fetchUsersTransactions();
        }
        
        if(this.authenticatedUser) {
            this.usersName = this.$store.getters.getUsersName;
        } else {
            this.logout();
        }
    },

    methods: {
        fetchUsersTransactions() {
            let user = this.$store.getters.getUser;

            axios.get('/api/users/' + user.id + '/transactions', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                this.$store.commit('setUserTransactions', response.data);
            });
        },

        clickMobileMainMenu() {
            eventBus.emit('mobileMainMenuIconClicked', this);
        },

        clickDropdownButton() {
            eventBus.emit('clickDropdownButton', this);
        }
    }
}
</script>