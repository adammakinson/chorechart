<template>
    <Transition>
        <div>
            <!-- <div v-if="mainMenuIsOpen" class="underlay bg-gray-800 bg-opacity-50 transition ease-in-out sm:invisible absolute w-full h-full"></div> -->
            <nav v-if="authenticatedUser" class="opacity-100 w-36">
                <ul id="navbarSupportedContent" :class="{mainmenu: mainMenuIsOpen}" class="w-44 absolute -translate-x-32 sm:-translate-x-32 transition-transform ease-in-out duration-500 h-full sm:block border sm:border-none bg-gray-400 sm:visible">
                    <li class="nav-item p-2 border-b w-full">
                        <div class="flex justify-end">
                            <div class="text-2xl h-10 ml-1.5 visible p-1.5 fas fa-bars" @click="clickMobileMainMenu"></div>
                        </div>
                    </li>
                    <li class="nav-item border-b w-full flex align-items">
                        <a href="/chores-list" class="nav-link block w-full p-2 leading-8">Chores</a>
                        <span class="fas fa-brush text-2xl px-3 py-2"></span>
                    </li>
                    <li class="nav-item border-b w-full flex align-items">
                        <a href="/rewards" class="nav-link block w-full p-2 leading-8">Rewards</a>
                        <span class="fas fa-gamepad text-2xl px-2 py-2"></span>
                    </li>
                    <li v-if="userIsAdmin" class="nav-item border-b w-full flex align-items">
                        <a href="/manage-chores" class="nav-link block w-full p-2 leading-8">Manage Chores</a>
                        <span class="fas fa-clipboard text-2xl px-3 py-2"></span>
                    </li>
                    <li v-if="userIsAdmin" class="nav-item border-b w-full flex align-items">
                        <a class="nav-link block w-full p-2 leading-8" href="/manage-users">Manage users</a>
                        <span class="fas fa-user text-2xl px-3 py-2"></span>
                    </li>
                    <li class="nav-item border-b w-full flex align-items">
                        <a class="nav-link block w-full p-2 leading-8" href="/manage-account">My account</a>
                        <span class="fas fa-laptop text-2xl px-2 py-2"></span>
                    </li>
                    <li class="nav-item border-b w-full">
                        <a class="nav-link block w-full p-2 leading-8" href="/" @click.prevent="logout">Logout</a>
                        <span class="fas fa-arrow-right-from-bracket text-2xl px-3 py-2"></span>
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

            this.mainMenuIsOpen = !this.mainMenuIsOpen;

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

        clickMobileMainMenu() {
            // this.mainMenuIsOpen = !this.mainMenuIsOpen;
            eventBus.emit('mobileMainMenuIconClicked', this);
        }
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