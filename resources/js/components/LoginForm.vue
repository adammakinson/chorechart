<template>
    <div class="card">
        <div class="card-body">
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" v-model="username" id="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" v-model="password" id="password">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>
                <button class="btn btn-primary" v-on:click.prevent="handleLogin" type="submit">Login</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            username: '',
            password: ''
        }
    },

    methods: {
        handleLogin: function() {
            let me = this;
            
            // Send the POST request
            axios.post('/api/login', {
                username: this.username,
                password: this.password
            }).then(function(response) {
                console.log(response);
                localStorage.setItem('authtoken', response.data.access_token);
                me.$router.push('chores-list');
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
    }
}
</script>