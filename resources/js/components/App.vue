<template>
    <main>
        <router-view></router-view>
    </main>
</template>
<script>
export default {
    created() {
        window.addEventListener('resize', () => {
            this.$store.commit('setWindowWidth', window.innerWidth);
        });
    }
}
</script>