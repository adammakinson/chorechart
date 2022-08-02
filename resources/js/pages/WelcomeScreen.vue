<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="flex flex-col items-center p-4">
            <h1 class="mx-auto mb-4 text-2xl text-center font-bold">Welcome to the chore chart!</h1>
            <p class="mx-auto text-center">A great way to keep things going around the house!</p>
            <div class="mx-auto mt-4">
                <Link href="/login">Login</Link>
                <b>or</b>
                <Link href="/register">Register</Link>
            </div>
        </div>
    </div>
</template>

<script>
import Link from '../components/Link.vue';

export default {
    components: {
        Link
    },

    mounted() {
        if(this.$store.getters.getUserAuthToken) {
            this.$router.push('chores-list');
        }
    }
}
</script>