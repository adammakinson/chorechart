<template>
    <div>
        <slot></slot>
        <div class="absolute bg-white border border-solid border-slate-200 p-4 top-14 right-4" :class="{visible: isOpen, invisible: !isOpen}">
            <list-group>
                <list-item v-for="item in dropdownData" :key="item.href" :listItem="item">
                    <a v-if="!item.event" :href="item.href">{{item.label}}</a>
                    <a v-if="item.event" :href="item.href" v-on:click="handleAnchorClick(`${item.event}`)">{{item.label}}</a>
                </list-item>
            </list-group>
        </div>
    </div>
</template>

<script>
import ListGroup from './ListGroup.vue';
import ListItem from './ListItem.vue';
import eventBus from '../eventBus';

export default {

    created() {
        eventBus.on("clickDropdownButton", () => {
            this.handleIconClick();
        });

        eventBus.on("logout", () => {
            this.logout();
        });
    },

    data() {
        return {
            isOpen: false
        };
    },

    components: {
        ListGroup,
        ListItem
    },

    props: [
        'dropdownData'
    ],

    methods: {
        handleIconClick: function() {
            this.isOpen = !this.isOpen;
        },

        handleAnchorClick(anchorEvent) {

            eventBus.emit(anchorEvent, this);
        },

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
            
        }
    }
}
</script>
