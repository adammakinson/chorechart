<template>
    <div class="flex justify-between py-2 border-b">
        <div class="fas fa-bars text-2xl h-10 ml-1.5 visible sm:invisible p-1.5" @click="clickMobileMainMenu"></div>
        <slot></slot>
        <p class="p-1.5">{{this.$store.getters.getUserPoints}}</p>
    </div>
</template>

<script>
import Icon from './Icon.vue';
import Button from './Button.vue';
import eventBus from '../eventBus';

export default {

    components: {
    },

    data() {
        return {
            authenticatedUser: false,
            usersName: '',
            dropdownClass: 'fas fa-user text-2xl h-10 mr-1.5',
            dropdownCallback: 'clickDropdownButton',
            dropdownData: [
                {
                    'id': 'manageAccount',
                    'href': '/manage-account',
                    'label': 'Manage Account',
                    'event': ''
                },
                {
                    'id': 'logout',
                    'href': '#',
                    'label': 'Logout',
                    'event': 'logout'
                }
            ]
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