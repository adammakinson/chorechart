<template>
    <Transition>
        <nav v-if="authenticatedUser" class="opacity-100 w-44 h-full bg-gray-400 h-full -translate-x-32 transition-transform ease-in-out duration-500 sm:visible" :class="{mainmenu: mainMenuIsOpen}">
            <ul id="navbarSupportedContent" :class="{mainmenu: mainMenuIsOpen}" class="w-44">
                <li class="nav-item p-2 border-b w-full">
                    <div class="flex justify-end">
                        <div class="text-2xl h-10 ml-1.5 visible p-1.5 fas fa-bars" @click="clickMobileMainMenu"></div>
                    </div>
                </li>
                <li class="nav-item border-b w-full flex align-items">
                    <a href="/chores-list" class="nav-link block w-full p-2 leading-8">Chores</a>
                    <a href="/chores-list">
                        <span class="fas fa-brush text-2xl px-3 py-2"></span>
                    </a>
                </li>
                <li class="nav-item border-b w-full flex align-items">
                    <a href="/rewards" class="nav-link block w-full p-2 leading-8">Rewards</a>
                    <a href="/rewards">
                        <span class="fas fa-gamepad text-2xl px-2 py-2"></span>
                    </a>
                </li>
                <li v-if="userIsAdmin" class="nav-item border-b w-full flex align-items">
                    <a href="/manage-chores" class="nav-link block w-full p-2 leading-8">Manage Chores</a>
                    <a href="/manage-chores">
                        <span class="fas fa-clipboard text-2xl px-3 py-2"></span>
                    </a>
                </li>
                <li v-if="userIsAdmin" class="nav-item border-b w-full flex align-items">
                    <a href="/manage-users" class="nav-link block w-full p-2 leading-8">Manage users</a>
                    <a href="/manage-users">
                        <span class="fas fa-user text-2xl px-3 py-2"></span>
                    </a>
                </li>
                <li class="nav-item border-b w-full flex align-items">
                    <a href="/manage-account" class="nav-link block w-full p-2 leading-8">My account</a>
                    <a href="/manage-account">
                        <span class="fas fa-laptop text-2xl px-2 py-2"></span>
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