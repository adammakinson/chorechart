<template>
    <div class="flex justify-between p-4 border-b">
        <div class="fas fa-bars visible sm:invisible" @click="clickMobileMainMenu"></div>
        <h1>{{usersName}} - {{this.$store.getters.getUserPoints}} points</h1>
        <dropdown-menu :dropdownData="dropdownData">
            <icon 
                v-bind:class='dropdownClass'
                v-bind:iconevent="dropdownCallback">
            </icon>
        </dropdown-menu>
    </div>
</template>

<script>
import DropdownMenu from './DropdownMenu.vue';
import Icon from './Icon.vue';
import eventBus from '../eventBus';

export default {

    components: {
        DropdownMenu,
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
        }
    }
}
</script>