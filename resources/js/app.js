require('./bootstrap');

import { createApp } from 'vue';
import { createStore } from 'vuex';
import 'es6-promise/auto';
import { createWebHistory, createRouter } from "vue-router";

import App from "./components/App";
// import { VuejsDatatableFactory } from 'vuejs-datatable';

// Import pages. Note, need to clearly define pages vs. components
import RegistrationForm from "./pages/RegistrationForm";
import LoginForm from "./pages/LoginForm";
import ChoresListComponent from "./pages/ChoresList";
import ManageChoresComponent from "./pages/ManageChores";
import WelcomeScreen from "./pages/WelcomeScreen";
import ManageUsers from "./pages/ManageUsers";
import RewardsPage from "./pages/RewardsPage";
import ManageAccount from "./pages/ManageAccount";

// VuejsDatatableFactory.useDefaultType( false )
//     .registerTableType( 'datatable', tableType => tableType.mergeSettings({
//         table: {
//             class: 'table table-striped'
//         }
//     }));

// Vue.use(VuejsDatatableFactory);

// Define routes for the router.
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

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create the vuex store. This globally handles user, user points and user transactions state
const store = new createStore({
    state: {
        user: {},
        userTransactions: [],
        windowWidth: window.innerWidth
    },
    mutations: {

        // Sets the current user in the vuex store as well as in localstorage,
        // which is used as a fallback for the vuex store.
        setCurrentUser (state, data) {
            state.user = data;
            localStorage.setItem('user', JSON.stringify(data));
        },
        
        // Revmoves the user from the vuex store AND from local storage
        removeCurrentUser (state) {
            state.user = {};
            localStorage.removeItem('user');
        },

        // Set user points on the vuex store
        setUserPoints (state, data) {
            state.user.userPoints = data;
        },

        // Set user transactions in the vuex store
        setUserTransactions (state, transactions) {
            state.userTransactions = transactions;
        },

        // Set window width in hte vuex store
        setWindowWidth (state, windowWidth) {
            state.windowWidth = windowWidth;
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

        /**
         * Fetches the user from the vuex store by preference. If not found
         * there, tries to fetch it from localStorage
         * 
         * @param {*} state 
         * @returns userData
         */
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

        /**
         * Fetches the users name from the vuex store
         * 
         * @param {*} state 
         * @returns  the users name from the vuex store
         */
        getUsersName: state => {
            return state.user.name;
        },

        /**
         * Fetches user roles from the vuex store
         * 
         * @param {*} state 
         * @returns user roles
         */
        getUsersRoles: state => {
            return state.user.roles;
        },

        /**
         * Uses the some() array method on the userRoles array to
         * determine whether the string 'admin' is included in those
         * roles. some returns a boolean. If there are no roles for
         * the user, automatically return false.
         * 
         * @param {*} state 
         * @returns 
         */
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

        /**
         * Transactions are returned from the database ordered by
         * most recent transaction first, and going back historically.
         * The most recent transaction will contain the most up-to-date
         * points value, which is why we're fetching it from the "first"
         * transaction.
         * 
         * @param {*} state 
         * @returns 
         */
        getUserPoints: state => {
            if (state.userTransactions[0]) {
                return state.userTransactions[0].user_points;
            } else {
                return 0;
            }
        },

        /**
         * Returns all user transactions from the vuex store
         * @param {*} state 
         * @returns 
         */
        getUserTransactions: state => {
            if (state.userTransactions) {
                return state.userTransactions;
            } else {
                return [];
            }
        },

        /**
         * Returns the window width from the vuex store
         * 
         * @param {*} state 
         * @returns 
         */
        getWindowWidth: state => {
            return state.windowWidth;
        }
    }
});

const app = createApp(App)
.use(router)
.use(store)
.mount('#app');