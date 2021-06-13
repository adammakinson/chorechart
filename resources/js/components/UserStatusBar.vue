<template>
    <div>
        <h1>{{usersName}} - {{this.$store.getters.getUserPoints}} points</h1>
    </div>
</template>

<script>
export default {
    data() {
        return {
            authenticatedUser: false,
            usersName: ''
        };
    },

    mounted() {
        this.authenticatedUser = !!this.$store.getters.getUserAuthToken;
        
        if(this.authenticatedUser) {
            this.usersName = this.$store.getters.getUsersName;
        } else {
            this.logout();
        }
    }
}
</script>