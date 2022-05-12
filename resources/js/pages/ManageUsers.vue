<template>
    <div class="w-screen">
        <user-status-bar>
            <h1>Manage Users</h1>
        </user-status-bar>
        <div class="sm:flex w-full h-screen divide-x divide-solid divide-slate-100">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <datatable :columns="columns" :data="rows" class="w-full">
                    <template slot-scope="{row, columns}">
                        <tr v-bind:id="row.id">
                            <td>{{row.name}}</td>
                            <td>
                                <span v-on:click="showChangeCredentialsModal" class="fas fa-key" v-bind:data-userid="row.id"></span>
                                <span v-on:click="showEditUserModal" class="fas fa-edit" v-bind:data-userid="row.id"></span>
                                <span v-on:click="removeUser" class="fas fa-trash" v-bind:data-userid="row.id"></span>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <modal id="editUserModal">
                    <template v-slot:header>
                        Edit User
                    </template>
                    <div>
                        <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                        <form id="editUserForm" :key="editUserFormKey">
                            <FormInput v-for="formField in editUserModalForm" :key="formField.identifier"
                                :identifier="formField.identifier"
                                :type="formField.type"
                                :elementLabel="formField.label"
                                :errors="formField.errors"
                                :value="formField.value"
                            ></FormInput>
                        </form>
                    </div>
                    <template v-slot:footer>
                        <footer>
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" callback="updateUser">Update user</Button>
                            <Button colorClass="text-white" bgColorClass="bg-red-600" callback="closeModal">Close</Button>
                        </footer>
                    </template>
                </modal>
                <modal id="updateUserCredentialsModal">
                    <template v-slot:header>
                        Change user credentials
                    </template>
                    <div>
                        <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                        <form id="changeUserCredentialsForm">
                            <div>
                                <label for="uname">Username: <span v-if="modalErrors.username">{{modalErrors.username[0]}}</span></label>
                                <input id="uname" name="username" type="text" v-bind:value="editingUsernameValue">
                            </div>
                            <div>
                                <label for="password">Password: <span v-if="modalErrors.password">{{modalErrors.password[0]}}</span></label>
                                <input id="password" name="password" type="password" value="">
                            </div>
                            <div>
                                <label for="confirm_password">Confirm password: <span v-if="modalErrors.confirm_password">{{modalErrors.confirm_password[0]}}</span></label>
                                <input id="confirm_password" name="confirm_password" type="password" value="">
                            </div>
                        </form>
                    </div>
                    <template v-slot:footer>
                        <footer>
                            <button type="button" v-on:click.prevent="changeUserCredentials" class="border px-4 py-2 shadow-md">Update credentials</button>
                            <button type="button" data-dismiss="modal" v-on:click.prevent="sendEventBusMessage" class="border px-4 py-2 shadow-md">Close</button>
                        </footer>
                    </template>
                </modal>
            </div>
        </div>
    </div>
</template>

<script>
    import Modal from "../components/Modal";
    import Appmenu from "../components/AppMenu.vue";
    import eventBus from '../eventBus';
    import Button from '../components/Button.vue';
    import Notification from '../components/Notification.vue';
    import UserStatusBar from '../components/UserStatusBar.vue';
    import FormInput from '../components/FormInput.vue';
    
    export default {
        created() {
            eventBus.$on('callback', (callback, args) => {
                var fn = window[callback];
        
                // 'this' is the VueComponent object
                if(args){
                    this[callback](args);
                } else {
                    this[callback]();
                }
            });
        },

        data() {
            return {
                users: null,
                currentUser: null,
                columns: [],
                rows: [],
                userIsAdmin: false,
                editingUsersId: '',
                editingUsersNameValue: '',
                editingUsernameValue: '',
                editingUserEmailValue: '',
                errorMessages: [],
                modalNotice: '',
                modalErrors: {},
                editUserModalForm: {
                    name: {
                        identifier: 'name',
                        label: 'Name',
                        type: 'text',
                        errors: '',
                        value: ''
                    },
                    username: {
                        identifier: 'username',
                        label: 'Username',
                        type: 'text',
                        errors: '',
                        value: ''
                    },
                    email: {
                        identifier: 'email',
                        label: 'Email',
                        type: 'text',
                        errors: '',
                        value: ''
                    }
                },
                editUserFormKey: 0
            }
        },

        components: {
            Modal,
            Appmenu,
            Notification,
            UserStatusBar,
            Button,
            FormInput
        },

        mounted() {
            if (this.$store.getters.getUserAuthToken && this.$store.getters.userIsAdmin) {
                
                this.getAllUsers();

            } else {

                this.$router.push('login');
            }
        },

        methods: {
            getAllUsers() {
                axios.get('/api/users', {
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then(
                    (response) => {
                        this.users = response.data;

                        this.columns = [
                            {label: 'name', field: 'name'}
                        ];

                        this.rows = response.data;
                    }
                );
            },

            showEditUserModal(el) {
                let editUserModal = document.getElementById('editUserModal');
                let userId = el.target.dataset.userid;
                let userBeingEdited;

                this.modalNotice = '';
                this.modalErrors = {};

                this.users.forEach(user => {
                    if (user.id == userId) {
                        userBeingEdited = user;
                    }
                });

                this.editingUsersId = userId;

                this.editUserModalForm.name.value = userBeingEdited.name;
                this.editUserModalForm.username.value = userBeingEdited.username;
                this.editUserModalForm.email.value = userBeingEdited.email;
                this.editUserModalForm.name.errors = '';
                this.editUserModalForm.username.errors = '';
                this.editUserModalForm.email.errors = '';

                editUserModal.classList.add('visible');
                editUserModal.classList.remove('invisible');

                this.editUserFormKey += 1;
            },

            showChangeCredentialsModal(el) {
                let changeCredentialsModal = document.getElementById('updateUserCredentialsModal');
                let userId = el.target.dataset.userid;
                let userBeingEdited;

                this.modalNotice = '';
                this.modalErrors = {};

                this.users.forEach(user => {
                    if (user.id == userId) {
                        userBeingEdited = user;
                    }
                });

                this.editingUsersId = userId;
                this.editingUsernameValue = userBeingEdited.username;

                changeCredentialsModal.classList.add('visible');
                changeCredentialsModal.classList.remove('invisible');
            },

            changeUserCredentials() {
                let formEls = document.querySelectorAll('#updateUserCredentialsModal .form-control');
                let userData = {};
                let formErrors = {};
                let validPasswords = false;

                formEls.forEach((el) => {
                    userData[el.name] = el.value;
                });

                // TODO validate passwords on the fly with an onchange event.
                if (userData.password.length < 8) {
                    formErrors.password = ['You must have at least eight characters'];
                }
                
                if (userData.confirm_password.length < 8) {
                    formErrors.confirm_password = ['You must have at least eight characters'];
                }
                
                if (userData.password != userData.confirm_password) {
                    if(formErrors.password) {
                        formErrors.password.push('Your passwords do not match');
                    } else {
                        formErrors.password = ['Your passwords do not match'];
                    }
                }

                if(Object.keys(formErrors).length == 0) {
                    validPasswords = true;
                } else {
                    this.modalNotice = {
                        message: 'The given data was invalid.',
                        type: 'error'
                    };

                    this.modalErrors = formErrors;
                }

                if(validPasswords) {
                    console.log('we have valid passwords');
                    axios({
                        method: 'put',
                        url: '/api/update-credentials/' + this.editingUsersId, // TODO rework this. its not restful
                        data: userData,
                        headers: {
                            authorization: this.$store.getters.getUserAuthToken
                        }
                    }).then((response) => {
                        // Close the modal
                        this.modalNotice = '';
                        this.modalErrors = {};
                        eventBus.$emit('close-modal');
                    }).catch((error) => {
                        this.modalNotice = {
                            message: error.response.data.message,
                            status: error.response.status
                        };

                        this.modalErrors = error.response.data.errors;
                    });
                }
            },

            updateUser() {
                let formEls = document.querySelectorAll('#editUserForm input');
                let userData = {};

                formEls.forEach((el) => {
                    userData[el.name] = el.value;
                });

                axios({
                    method: 'put',
                    url: '/api/users/' + this.editingUsersId,
                    data: userData,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    this.users = response.data;
                    this.rows = response.data;
                    
                    // Close the modal
                    eventBus.$emit('close-modal');
                }).catch((error) => {
                    this.modalNotice = {
                        message: error.response.data.message,
                        status: error.response.status
                    };
                    
                    this.modalErrors = error.response.data.errors;

                    for (let prop in error.response.data.errors) {
                        this.editUserModalForm[prop].errors = error.response.data.errors[prop];
                    }

                    this.editUserModalForm.name.value = userData.name;
                    this.editUserModalForm.username.value = userData.username;
                    this.editUserModalForm.email.value = userData.email;
                    this.editUserFormKey += 1;
                });
            },

            removeUser(el) {
                let userId = el.target.dataset.userid;

                axios({
                    method: 'delete',
                    url: '/api/users/' + userId,
                    headers: {
                        authorization: this.$store.getters.getUserAuthToken
                    }
                }).then((response) => {
                    this.users = response.data;
                    this.rows = response.data;
                }).catch((error) => {
                    console.log(error);
                })

                // Close the modal
                eventBus.$emit('close-modal');
            },
            
            closeModal() {
                eventBus.$emit('close-modal');
            }
        }
    }
</script>