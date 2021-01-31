require('./bootstrap');

import Vue from "vue";
import Vuex from 'vuex';
import 'es6-promise/auto';
import VueRouter from "vue-router";

import App from "./components/App";
import { VuejsDatatableFactory } from 'vuejs-datatable';

import LoginForm from "./components/LoginForm";
import ChoresListComponent from "./components/ChoresList";
import WelcomeScreen from "./components/WelcomeScreen";
import RegistrationForm from "./components/RegistrationForm";
import ManageUsers from "./components/ManageUsers";

VuejsDatatableFactory.useDefaultType( false )
    .registerTableType( 'datatable', tableType => tableType.mergeSettings({
        table: {
            class: 'table table-striped'
        }
    }));

Vue.use(VuejsDatatableFactory);
Vue.use(VueRouter);
Vue.use(Vuex);

const routes = [
    {path: '/', name: 'welcome-screen', component: WelcomeScreen},
    {path: '/chores-list', name: 'chores-list', component: ChoresListComponent},
    {path: '/login', name: 'login', component: LoginForm},
    {path: '/register', name: 'register', component: RegistrationForm},
    {path: '/manage-users', name: 'manage-users', component: ManageUsers}
];

const router = new VueRouter({
    mode: 'history',
    routes
});

const store = new Vuex.Store({
    state: {
        user: {}
    },
    mutations: {
        setCurrentUser (state, data) {
            state.user = data;
            localStorage.setItem('user', JSON.stringify(data));
        },
        
        removeCurrentUser (state) {
            state.user = {};
            localStorage.removeItem('user');
        }
    },
    getters: {
        /**
         * Tries to get the user access token from vuex first. If vuex doesn't
         * have the token, looks in localStorage for stored user data. If no
         * user token is found in either place, returns undefined.
         * @param {*} state 
         */
        getUserAuthToken: state => {
            let authToken;

            if (state.user.token_type && state.user.access_token) {
                authToken = `${state.user.token_type} ${state.user.access_token}`;
            } else if (localStorage.getItem('user')) {
                let userData = JSON.parse(localStorage.getItem('user'));

                authToken = `${userData.token_type} ${userData.access_token}`;

                // If we get it from localStorage, we need to ensure it's in vuex
                // this.$store.commit('setCurrentUser', localStorage.getItem('user'));
                state.user = userData;
            }

            return authToken;
        },

        getUser: state => {
            return state.user;
        },

        getUsersName: state => {
            return state.user.name;
        },

        getUsersRoles: state => {
            return state.user.roles;
        },

        userIsAdmin: state => {
            let userRoles = state.user.roles;

            if (userRoles) {
                return userRoles.some(role => {
                    return role.role == 'admin';
                });
            } else {
                return false;
            }
        }
    }
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
    store,
    render: (h) => h(App)
});