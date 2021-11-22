<template>
    <nav v-if="authenticatedUser" class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/chores-list">Chore chart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="container-fluid d-flex justify-content-end px-0">
                    <a href="/chores-list" class="nav-link">My Chores</a>
                    <a v-if="userIsAdmin" href="/manage-chores" class="nav-link">Manage Chores</a>
                    <a href="/rewards" class="nav-link">Rewards</a>
                    <a v-if="userIsAdmin" class="nav-link" href="/manage-users">Manage users</a>
                    <a class="nav-link" href="#" v-on:click.prevent="logout">Logout</a>
                </div>
            </div>
        </div>
    </nav>
</template>
<script>
export default {
    data() {
        return {
            authenticatedUser: false,
            usersName: '',
            userIsAdmin: false,
        };
    },

    methods: {
        logout() {
            this.$store.commit('removeCurrentUser');
            // localStorage.removeItem('user');
            this.$router.push('login');
        }
    },

    mounted() {
        this.authenticatedUser = !!this.$store.getters.getUserAuthToken;
        
        if(this.authenticatedUser) {
            this.usersName = this.$store.getters.getUsersName;

            this.userIsAdmin = this.$store.getters.userIsAdmin;
        } else {
            this.logout();
        }
    }
}
</script>