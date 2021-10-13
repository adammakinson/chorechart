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
        }
    }
}
</script>