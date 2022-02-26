<template>
    <div class="d-flex align-items-center justify-content-center" style="height: 100vh">
        <div class="row" style="--bs-gutter-x:0;">

            <div class="card" style="width: 40em;">
                <div class="card-header">
                    <h1>Welcome to the chore chart!</h1>
                </div>
                <div class="card-body">
                    <div style="text-align:center;">
                    <p>A great way to keep things going around the house!</p>
                    <a class="btn btn-primary" href="/login">Login</a> <b>or</b> <a class="btn btn-primary" href="/register">register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        if(this.$store.getters.getUserAuthToken) {
            this.$router.push('chores-list');
        }
    }
}
</script>