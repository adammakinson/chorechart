<template>
    <div class="w-screen">
        <user-status-bar>
            <h1>Manage Users</h1>
        </user-status-bar>
        <div class="sm:flex w-full h-screen divide-x divide-solid divide-slate-100">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <datatable :columns="columns" :data="rows" class="w-full table-auto">
                    <template slot-scope="{row, columns}">
                        <tr v-bind:id="row.id" class="border-b border-t border-slate-300 leading-10">
                            <td>{{row.name}}</td>
                            <td>
                                <div class="flex justify-end">
                                    <span v-on:click="showChangeCredentialsModal" class="fas fa-key p-0.5" v-bind:data-userid="row.id"></span>
                                    <span v-on:click="showEditUserModal" class="fas fa-edit p-0.5" v-bind:data-userid="row.id"></span>
                                    <span v-on:click="removeUser" class="fas fa-trash p-0.5" v-bind:data-userid="row.id"></span>
                                </div>
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
                        <form id="changeUserCredentialsForm" :key="updateCredentialsFormKey">
                            <FormInput v-for="formField in updateCredentialsModalForm" :key="formField.identifier"
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
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" callback="changeUserCredentials">Update credentials</Button>
                            <Button colorClass="text-white" bgColorClass="bg-red-600" callback="closeModal">Close</Button>
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
        
                // 'this' is the VueComponent object
                if(this[callback]){
                    if(args){
                        this[callback](args);
                    } else {
                        this[callback]();
                    }
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
                editUserFormKey: 0,
                updateCredentialsModalForm: {
                    username: {
                        identifier: 'username',
                        label: 'Username',
                        type: 'text',
                        errors: '',
                        value: ''
                    },
                    password: {
                        identifier: 'password',
                        label: 'Password',
                        type: 'password',
                        errors: '',
                        value: ''
                    },
                    confirm_password: {
                        identifier: 'confirm_password',
                        label: 'Confirm password',
                        type: 'password',
                        errors: '',
                        value: ''
                    }
                },
                updateCredentialsFormKey: 0
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
                let editUserModalUnderlay = editUserModal.parentNode;
                let userId = el.target.dataset.userid;
                let userBeingEdited;

                console.log(editUserModal);

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
                editUserModalUnderlay.classList.add('visible');
                editUserModalUnderlay.classList.remove('invisible');

                this.editUserFormKey += 1;
            },

            showChangeCredentialsModal(el) {
                let changeCredentialsModal = document.getElementById('updateUserCredentialsModal');
                let changeCredentialsModalUnderlay = changeCredentialsModal.parentNode;
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

                this.updateCredentialsModalForm.username.value = userBeingEdited.username;
                this.updateCredentialsModalForm.username.errors = '';
                this.updateCredentialsModalForm.password.errors = '';
                this.updateCredentialsModalForm.confirm_password.errors = '';

                changeCredentialsModal.classList.add('visible');
                changeCredentialsModal.classList.remove('invisible');
                changeCredentialsModalUnderlay.classList.add('visible');
                changeCredentialsModalUnderlay.classList.remove('invisible');

                this.updateCredentialsFormKey += 1;
            },

            changeUserCredentials() {
                let formEls = document.querySelectorAll('#updateUserCredentialsModal input');
                let userData = {};
                let numFormErrors = 0;
                let validPasswords = false;

                formEls.forEach((el) => {
                    userData[el.name] = el.value;
                });

                // TODO validate passwords on the fly with an onchange event.
                if (userData.password.length < 8) {
                    this.updateCredentialsModalForm.password.errors = ['You must have at least eight characters'];
                    numFormErrors++;
                }
                
                if (userData.confirm_password.length < 8) {
                    this.updateCredentialsModalForm.confirm_password.errors = ['You must have at least eight characters'];
                    numFormErrors++;
                }
                
                if (userData.password != userData.confirm_password) {
                    this.updateCredentialsModalForm.password.errors = ['Your passwords do not match'];
                    numFormErrors++;
                }

                if(numFormErrors == 0) {
                    validPasswords = true;
                } else {
                    this.modalNotice = {
                        message: 'The given data was invalid.',
                        type: 'error'
                    };

                    this.updateCredentialsFormKey += 1;
                }

                if(validPasswords) {
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

                        // This will get replaced too...
                        for (let prop in error.response.data.errors) {
                            this.updateCredentialsModalForm[prop].errors = error.response.data.errors[prop];
                        }

                        this.updateCredentialsModalForm.username.value = userData.username;
                        this.updateCredentialsModalForm.password.value = userData.password;
                        this.updateCredentialsModalForm.confirm_password.value = userData.confirm_password;

                        this.updateCredentialsFormKey += 1;
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