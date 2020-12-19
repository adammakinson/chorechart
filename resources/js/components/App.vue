<template>
    <main class="container">
        <h1>app</h1>
        <!-- <chores-list id="foobar"></chores-list> -->
        <!-- <modal id="createChoreModal"></modal> -->
        <p>
            <router-link :to="{ name: 'chores-list'}">Chores</router-link>
        </p>
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