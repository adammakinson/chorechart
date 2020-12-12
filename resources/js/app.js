require('./bootstrap');

import Vue from "vue";
import VueRouter from "vue-router";

import App from "./components/App";
import { VuejsDatatableFactory } from 'vuejs-datatable';

import LoginForm from "./components/LoginForm";
import ChoresListComponent from "./components/ChoresList";

VuejsDatatableFactory.useDefaultType( false )
    .registerTableType( 'datatable', tableType => tableType.mergeSettings({
        table: {
            class: 'table table-striped'
        }
    }));

Vue.use(VuejsDatatableFactory);
Vue.use(VueRouter);

const routes = [
    {path: '/chores-list', name: 'chores-list', component: ChoresListComponent},
    {path: '/login', name: 'login', component: LoginForm}
];

const router = new VueRouter({
    mode: 'history',
    routes
});

// const app = new Vue({
//     router,
//     components: {
//         App
//     }
// }).$mount('#app');

const app = new Vue({
    el: '#app',
    router,
    render: (h) => h(App)
});