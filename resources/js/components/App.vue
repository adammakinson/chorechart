<template>
    <main class="container">
        <router-view></router-view>
    </main>
</template>

<script>
import ChoreslistComponent from "./ChoresList";
import LoginForm from "./LoginForm";

export default {

    components: {
        // 'chores-list': ChoreslistComponent,
        // 'modal': Modal,
        // 'loginform': LoginForm
    },

    mounted() {
        console.log('mounted!');
        // this.$router.push('login');
    }

}
</script>