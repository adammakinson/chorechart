<template>
    <Transition>
        <div v-if="mainMenuIsOpen">
            <div v-if="mainMenuIsOpen" class="underlay bg-gray-800 bg-opacity-50 transition ease-in-out sm:invisible absolute w-full h-full"></div>
            <nav v-if="authenticatedUser" class="opacity-100 w-48">
                <ul id="navbarSupportedContent" class="mainmenu absolute w-48 -translate-x-48 transition-transform ease-in-out h-full sm:block border sm:border-none bg-white sm:visible">
                    <li class="nav-item p-2 border-b w-full">
                        <a href="/chores-list" class="nav-link block w-full h-full">Chores</a>
                    </li>
                    <li class="nav-item p-2 border-b w-full">
                        <a href="/rewards" class="nav-link block w-full h-full">Rewards</a>
                    </li>
                    <li v-if="userIsAdmin" class="nav-item p-2 border-b w-full">
                        <a href="/manage-chores" class="nav-link block w-full h-full">Manage Chores</a>
                    </li>
                    <li v-if="userIsAdmin" class="nav-item p-2 border-b w-full">
                        <a class="nav-link block w-full h-full" href="/manage-users">Manage users</a>
                    </li>
                </ul>
            </nav>
        </div>
    </Transition>
</template>
<script>
import eventBus from '../eventBus';

export default {
    created() {
        eventBus.on("mobileMainMenuIconClicked", () => {
            if(this.windowWidth < 640) {
                this.mainMenuIsOpen = !this.mainMenuIsOpen;
            } else {
                this.mainMenuIsOpen = true;
            }
        });

        if (this.windowWidth < 640) {
            this.mainMenuIsOpen = false;
        } else {
            this.mainMenuIsOpen = true;
        }
    },

    data() {
        return {
            authenticatedUser: false,
            usersName: '',
            userIsAdmin: false,
            mainMenuIsOpen: false
        };
    },

    computed: {
        windowWidth() {
            return this.$store.getters.getWindowWidth;
        }
    },

    watch: {
        windowWidth(newWidth, oldWidth) {
            if (newWidth > 640) {
                this.mainMenuIsOpen = true;
            } else {
                this.mainMenuIsOpen = false;
            }
        }
    },

    methods: {
        
    },

    mounted() {
        this.authenticatedUser = !!this.$store.getters.getUserAuthToken;
        
        if(this.authenticatedUser) {
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