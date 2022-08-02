<template>
    <div class="flex justify-between py-2 border-b">
        <div class="fas fa-bars visible sm:invisible p-1.5" @click="clickMobileMainMenu"></div>
        <slot></slot>
        <dropdown-menu :dropdownData="dropdownData" class="flex">
            <p class="p-1.5">{{this.$store.getters.getUserPoints}}</p>
            <Button bgColorClass="bg-white" colorClass="text-black" widthClass="w-10" paddingClass="p-1" :callback="dropdownCallback">
                <icon v-bind:class='dropdownClass'></icon>
            </Button>
        </dropdown-menu>
    </div>
</template>

<script>
import DropdownMenu from './DropdownMenu.vue';
import Icon from './Icon.vue';
import Button from './Button.vue';
import eventBus from '../eventBus';

export default {

    components: {
        DropdownMenu,
        Button,
        Icon
    },

    data() {
        return {
            authenticatedUser: false,
            usersName: '',
            dropdownClass: 'fas fa-user',
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
        eventBus.$on('callback', (callback, args) => {    
            // 'this' is the VueComponent object
            if(this[callback]){
                if(args){
                    this[callback](args);
                } else {
                    this[callback]();
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
            eventBus.$emit('mobileMainMenuIconClicked', this);
        },

        clickDropdownButton() {
            console.log('firing eventbus clickDropdownButton event!');
            eventBus.$emit('clickDropdownButton', this);
        }
    }
}
</script>