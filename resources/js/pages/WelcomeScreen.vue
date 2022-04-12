<template>
    <div class="flex flex-col justify-center h-screen">
        <div class="flex flex-col items-center">
            <h1 class="mx-auto mb-4 text-3xl font-bold">Welcome to the chore chart!</h1>
            <p class="mx-auto">A great way to keep things going around the house!</p>
            <div class="mx-auto mt-4">
                <a href="/login" class="inline-block border px-4 py-2 shadow-md bg-blue-600 hover:bg-blue-400 text-white rounded-lg">Login</a> 
                <b>or</b> 
                <a href="/register" class="inline-block border px-4 py-2 shadow-md bg-blue-600 hover:bg-blue-400 text-white rounded-lg">register</a>
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