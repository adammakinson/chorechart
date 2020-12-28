<template>
    <div class="container d-flex align-items-center justify-content-center" style="height: 100vh">
        <div class="card" style="width: 40em;">
            <div class="card-header">
                <h1>Chorechart Login</h1>
            </div>
            <div class="card-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" v-model="username" id="username" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" v-model="password" id="password" class="form-control">
                    </div>
                    <!-- <div class="form-group">
                        <input type="checkbox" id="remember_me" name="remember">
                        <label for="remember_me">Remember me</label>
                    </div> -->
                    <button class="btn btn-primary" v-on:click.prevent="handleLogin" type="submit">Login</button>
                    <p v-if="error">{{error}}</p>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            username: '',
            password: '',
            error: ''
        }
    },

    methods: {
        handleLogin: function() {
            let me = this;
            
            // Send the POST request
            axios.post('/api/login', {
                username: this.username,
                password: this.password
            }).then((response) => {

                this.$store.commit('setCurrentUser', response.data.user);

                this.$router.push('chores-list');
            }).catch((error) => {
                if (error.response) {
                    console.log(error.response);
                    this.error = error.response.data.message;
                }
            });
        }
    },

    mounted() {
        // if (!this.cookie.get('XSRF-TOKEN')) {
            axios.get('/sanctum/csrf-cookie').then(response => {
            //    this.csrfToken = this.cookie.get('XSRF-TOKEN');  
                console.log('cookie should be there');   
            });
        // }

        if(this.$store.getters.getUserAuthToken) {
            this.$router.push('chores-list'); // This may change to a different view...
        }
    }
}
</script>