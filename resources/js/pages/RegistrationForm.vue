<template>
    <div class="container-fluid">
        <div class="row align-items-center justify-content-center vh-100">
            <div class="col col-md-4">
                <h1>Register</h1>
                <div class="card">
                    <div class="card-header">
                        <notification v-if="typeof errorsNotice === 'object'" v-bind:notice="errorsNotice"></notification> <!-- v-bind:notice="error" -->
                    </div>
                    <div class="card-body">
                        <form id="loginForm">
                            <div class="form-group mb-3">
                                <label for="name">Name: <span class="text-danger text-opacity-50" v-if="errors.name">{{errors.name[0]}}</span></label>
                                <input type="text" name="name" v-model="name" id="name" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="username">Username: <span class="text-danger text-opacity-50" v-if="errors.username">{{errors.username[0]}}</span></label>
                                <input type="text" name="username" v-model="username" id="username" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email: <span class="text-danger text-opacity-50" v-if="errors.email">{{errors.email[0]}}</span></label>
                                <input type="text" name="email" v-model="email" id="email" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password: <span class="text-danger text-opacity-50" v-if="errors.password">{{errors.password[0]}}</span></label>
                                <input type="password" name="password" v-model="password" id="password" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="passwordconfirm">Confirm password: <span class="text-danger text-opacity-50" v-if="errors.confirm_password">{{errors.confirm_password[0]}}</span></label>
                                <input type="password" name="passwordconfirm" v-model="passwordconfirm" id="passwordconfirm" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <button class="btn btn-primary" v-on:click.prevent="handleRegistration" type="submit">Register</button>
                                <!-- <p v-if="error">{{error}}</p> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../components/Notification.vue';

export default {
    components: {
        Notification
    },

    data() {
        return {
            name: '',
            username: '',
            email: '',
            password: '',
            passwordconfirm: '',
            errorsNotice: '',
            errors: ''
        };
    },

    methods: {
        handleRegistration() {
            axios.post('/api/register', {
                name: this.name,
                username: this.username,
                email: this.email,
                password: this.password,
                confirm_password: this.passwordconfirm
            }).then((response) => {
                this.$router.push('login');
            }).catch((error) => {
                if (error.response) {
                    console.log(error.response.data.errors);

                    this.errorsNotice = {
                        message: error.response.data.message,
                        type: 'error'
                    };

                    this.errors = error.response.data.errors;
                }
            });
        }
    }
}
</script>