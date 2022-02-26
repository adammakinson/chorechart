require('./bootstrap');

import Vue from "vue";
import Vuex from 'vuex';
import 'es6-promise/auto';
import VueRouter from "vue-router";

import App from "./components/App";
import { VuejsDatatableFactory } from 'vuejs-datatable';

import RegistrationForm from "./pages/RegistrationForm";
import LoginForm from "./pages/LoginForm";
import ChoresListComponent from "./pages/ChoresList";
import ManageChoresComponent from "./components/ManageChores";
import WelcomeScreen from "./pages/WelcomeScreen";
import ManageUsers from "./components/ManageUsers";
import RewardsPage from "./pages/RewardsPage";
import ManageAccount from "./pages/ManageAccount";

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
    {path: '/manage-chores', name: 'manage-chores', component: ManageChoresComponent},
    {path: '/login', name: 'login', component: LoginForm},
    {path: '/register', name: 'register', component: RegistrationForm},
    {path: '/manage-users', name: 'manage-users', component: ManageUsers},
    {path: '/rewards', name: 'rewards', component: RewardsPage},
    {path: '/manage-account', name: 'manage-account', component: ManageAccount}
];

const router = new VueRouter({
    mode: 'history',
    routes
});

const store = new Vuex.Store({
    state: {
        user: {},
        userTransactions: []
    },
    mutations: {
        setCurrentUser (state, data) {
            state.user = data;
            localStorage.setItem('user', JSON.stringify(data));
        },
        
        removeCurrentUser (state) {
            state.user = {};
            localStorage.removeItem('user');
        },

        setUserPoints (state, data) {
            state.user.userPoints = data;
        },

        setUserTransactions (state, transactions) {
            state.userTransactions = transactions;
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
            let userData;

            if (state.user.name) {
                userData = state.user; // fetch from vuex.
            } else if (localStorage.getItem('user')) {
                userData = JSON.parse(localStorage.getItem('user'));
                state.user = userData;
            } else {
                // fetch the user from the database
            }

            return userData;
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
        },

        getUserPoints: state => {
            if (state.userTransactions[0]) {
                return state.userTransactions[0].user_points;
            } else {
                return 0;
            }
        },

        getUserTransactions: state => {
            if (state.userTransactions) {
                return state.userTransactions;
            } else {
                return [];
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