<template>
    <Transition>
        <nav v-if="authenticatedUser" class="absolute top-0 opacity-100 w-48 h-full bg-gray-400 -translate-x-48 transition-transform ease-in-out duration-500 sm:visible" :class="{mainmenu: mainMenuIsOpen}">
            <ul id="navbarSupportedContent" :class="{mainmenu: mainMenuIsOpen}" class="w-48">
                <li class="nav-item p-2 border-b w-full">
                    <div class="flex justify-end">
                        <div class="text-2xl h-10 ml-1.5 visible p-1.5 fas fa-bars" @click="clickMobileMainMenu"></div>
                    </div>
                </li>
                <li v-for="menuItem in menuItems" :key="menuItem.url" class="nav-item border-b w-full flex align-items">
                    <a :href="menuItem.url" class="nav-link block w-full p-2 leading-8">{{ menuItem.label }}</a>
                    <a :href="menuItem.url">
                        <span class="text-2xl px-3 py-2" :class="menuItem.icon"></span>
                    </a>
                </li>
                <li class="nav-item border-b w-full flex align-items">
                    <a class="nav-link block w-full p-2 leading-8" href="/" @click.prevent="logout">Logout</a>
                    <a href="/" @click.prevent="logout">
                        <span class="fas fa-door-open text-2xl px-3 py-2"></span>
                    </a>
                </li>
            </ul>
        </nav>
    </Transition>
</template>
<script>
import eventBus from '../eventBus';

export default {
    created() {
        eventBus.on("mobileMainMenuIconClicked", () => {

            this.mainMenuIsOpen = !this.mainMenuIsOpen;
        });
    },

    data() {
        return {
            authenticatedUser: false,
            usersName: '',
            userIsAdmin: false,
            mainMenuIsOpen: false,
            menuItems: [
                {label: 'My Chores', url: '/chores-list', icon: 'fas fa-brush'},
                {label: 'Rewards', url: '/rewards', icon: 'fas fa-gamepad'},
                {label: 'Manage Chores', url: '/manage-chores', icon: 'fas fa-clipboard'},
                {label: 'Users', url: '/manage-users', icon: 'fas fa-user'},
                {label: 'My account', url: '/manage-account', icon: 'fas fa-laptop'}
            ]
        };
    },

    methods: {

        /**
         * Send a request to the server to remove the server side session, then
         * remove the session from the vue store and from sessionStorage
         */
        logout() {
            axios({
                method: 'post',
                url: '/api/logout',
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.$store.commit('removeCurrentUser');
                
                this.$router.push('login');
            });
            
        },


        /**
         * Send an event to open or close the menu
         */
        clickMobileMainMenu() {
            eventBus.emit('mobileMainMenuIconClicked', this);
        }
    },

    mounted() {
        this.authenticatedUser = !!this.$store.getters.getUserAuthToken;
        
        if (this.authenticatedUser) {
            this.usersName = this.$store.getters.getUsersName;

            this.userIsAdmin = this.$store.getters.userIsAdmin;
        } else {
            eventBus.emit('logout', this);
        }
    }
}
</script>

<style scoped>
    .v-enter-active .underlay,
    .v-leave-active .underlay {
    transition: opacity 0.5s ease;
    }

    .v-enter-from .underlay,
    .v-leave-to .underlay {
    opacity: 0;
    }
    .v-enter-active .mainmenu,
    .v-leave-active .mainmenu {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
    }

    .v-enter-from .mainmenu {
        transform: translateX(-16rem);
    }
    .v-leave-to .mainmenu {
    transform: translateX(-16rem);
    }

    .mainmenu {
        transform: translateX(0rem);
    }
</style>