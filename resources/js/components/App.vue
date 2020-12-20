<template>
    <main class="container">
        <router-view></router-view>
    </main>
</template>

<script>

export default {
    components: {
    
    },

    mounted() {
        if (!this.$store.getters.getUserAuthToken) {
            this.$router.push('login');
        }
    }

}
</script>