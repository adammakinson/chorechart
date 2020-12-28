<template>
    <div class="container d-flex align-items-center justify-content-center" style="height: 100vh">
        <div class="card" style="width: 40em;">
            <div class="card-header">
                <h1>Register</h1>
            </div>
            <div class="card-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" v-model="name" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" v-model="username" id="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" name="email" v-model="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" v-model="password" id="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="passwordconfirm">Confirm password:</label>
                        <input type="password" name="passwordconfirm" v-model="passwordconfirm" id="passwordconfirm" class="form-control" required>
                    </div>
                    <button class="btn btn-primary" v-on:click.prevent="handleRegistration" type="submit">Register</button>
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
            name: '',
            username: '',
            email: '',
            password: '',
            passwordconfirm: '',
            error: ''
        };
    },

    methods: {
        handleRegistration() {
            console.log("registering the user!");

            axios.post('/api/register', {
                name: this.name,
                username: this.username,
                email: this.email,
                password: this.password,
                confirm_password: this.passwordconfirm
            }).then((response) => {
                console.log(response.data);
                this.$router.push('login');
            }).catch((error) => {
                if (error.response) {
                    console.log(error.response);
                    this.error = error.response.data.message;
                }
            });
        }
    }
}
</script>