<template>
    <div class="card">
        <div class="card-body">
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>
                <button class="btn btn-primary" v-on:click="handleLogin" type="submit">Login</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data: {

    },

    methods: {
        handleLogin: function() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                console.log(response);
            });
        }
    }
}
</script>