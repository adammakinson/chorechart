<template>
    <nav v-if="authenticatedUser" class="sm:p-5 sm:w-64">
        <ul id="navbarSupportedContent" class="absolute sm:block border sm:border-none bg-white sm:visible p-5 sm:p-0" :class="{visible: mainMenuIsOpen, invisible: !mainMenuIsOpen}">
            <li class="nav-item">
                <a href="/chores-list" class="nav-link">Chores</a>
            </li>
            <li class="nav-item">
                <a href="/rewards" class="nav-link">Rewards</a>
            </li>
            <li class="nav-item">
                <a v-if="userIsAdmin" href="/manage-chores" class="nav-link">Manage Chores</a>
            </li>
            <li class="nav-item">
                <a v-if="userIsAdmin" class="nav-link" href="/manage-users">Manage users</a>
            </li>
        </ul>
    </nav>
</template>
<script>
import eventBus from '../eventBus';

export default {
    created() {
        eventBus.$on("mobileMainMenuIconClicked", () => {
            this.mainMenuIsOpen = !this.mainMenuIsOpen;
        });
    },

    data() {
        return {
            authenticatedUser: false,
            usersName: '',
            userIsAdmin: false,
            mainMenuIsOpen: false
        };
    },

    methods: {
        
    },

    mounted() {
        this.authenticatedUser = !!this.$store.getters.getUserAuthToken;
        
        if(this.authenticatedUser) {
            this.usersName = this.$store.getters.getUsersName;

            this.userIsAdmin = this.$store.getters.userIsAdmin;
        } else {
            eventBus.$emit('logout', this);
        }
    }
}
</script>