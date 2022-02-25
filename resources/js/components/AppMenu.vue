<template>
    <nav v-if="authenticatedUser" class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <a class="navbar-brand" href="/chores-list">Chore chart</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul id="navbarSupportedContent" class="nav navbar-collapse collapse justify-content-end px-0 flex-column flex-md-row">
            <li class="nav-item">
                <a href="/chores-list" class="nav-link">My Chores</a>
            </li>
            <li class="nav-item">
                <a v-if="userIsAdmin" href="/manage-chores" class="nav-link">Manage Chores</a>
            </li>
            <li class="nav-item">
                <a href="/rewards" class="nav-link">Rewards</a>
            </li>
            <li class="nav-item">
                <a v-if="userIsAdmin" class="nav-link" href="/manage-users">Manage users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/manage-account">Manage Account</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" v-on:click.prevent="logout">Logout</a>
            </li>
        </ul>
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