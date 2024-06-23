<template>
    <div class="w-screen">
        <!-- <user-status-bar>
            <h1 class="self-center">Manage Users</h1>
        </user-status-bar> -->
        <div class="grid min-h-screen transition-all duration-500 ease-in-out" :class="[ mainMenuIsOpen ? 'grid-cols-menuexpanded' : 'grid-cols-menucollapsed' ]">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <ListGroup v-if="users.length > 0" :listId="'my-chores-list'" class="mt-4">
                    <list-item v-for="userData in users" :key="userData.id" :listItem="userData" :draggable="false" :selectable="false" class="border border-slate-400">
                        <div class="flex">
                            <span class="h-8 p-1">{{userData.name}}</span>
                        </div>
                        <template v-slot:actions>
                            <span v-on:click="showChangeCredentialsModal" class="fas fa-key px-1 py-2" v-bind:data-userid="userData.id"></span>
                            <span v-on:click="showEditUserModal" class="fas fa-edit px-1 py-2" v-bind:data-userid="userData.id"></span>
                            <span v-on:click="removeUser" class="fas fa-trash px-1 py-2" v-bind:data-userid="userData.id"></span>
                        </template>
                    </list-item>
                </ListGroup>
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
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" marginClass="mr-4" callback="updateUser">Update user</Button>
                            <Button colorClass="text-white" bgColorClass="bg-red-600" marginClass="mr-4" callback="closeModal">Close</Button>
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
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" marginClass="mr-4" callback="changeUserCredentials">Update credentials</Button>
                            <Button colorClass="text-white" bgColorClass="bg-red-600" marginClass="mr-4" callback="closeModal">Close</Button>
                        </footer>
                    </template>
                </modal>
            </div>
        </div>
    </div>
</template>

<script>
    import eventBus from '../eventBus';
    import Modal from "../components/Modal";
    import Button from '../components/Button.vue';
    import Appmenu from "../components/AppMenu.vue";
    import ListItem from "../components/ListItem.vue";
    import FormInput from '../components/FormInput.vue';
    import ListGroup from "../components/ListGroup.vue";
    import Notification from '../components/Notification.vue';
    import UserStatusBar from '../components/UserStatusBar.vue';
    
    export default {
        created() {
            eventBus.on('callback', (eventData) => {
    
                // 'this' is the VueComponent object
                if(this[eventData.callback]){
                    if(eventData.args){
                        this[eventData.callback](eventData.args);
                    } else {
                        this[eventData.callback]();
                    }
                }
            });

            eventBus.on("mobileMainMenuIconClicked", () => {
                this.mainMenuIsOpen = !this.mainMenuIsOpen;
            });

            if (this.windowWidth < 640) {
                this.mainMenuIsOpen = false;
            } else {
                this.mainMenuIsOpen = true;
            }

            window.matchMedia("(orientation: portrait)").addEventListener("change", e => {
                const portrait = e.matches;

                if (this.windowWidth < 640) {
                    this.mainMenuIsOpen = false;
                } else {
                    this.mainMenuIsOpen = true;
                }
            });
        },

        data() {
            return {
                users: [],
                currentUser: null,
                userIsAdmin: false,
                editingUsersId: '',
                editingUsersNameValue: '',
                editingUsernameValue: '',
                editingUserEmailValue: '',
                errorMessages: [],
                modalNotice: '',
                modalErrors: {},
                mainMenuIsOpen: false,
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

        computed: {
            windowWidth() {
                return this.$store.getters.getWindowWidth;
            }
        },

        components: {
            Modal,
            Appmenu,
            Notification,
            UserStatusBar,
            Button,
            FormInput,
            ListItem,
            ListGroup
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
                    }
                );
            },

            showEditUserModal(el) {
                let editUserModal = document.getElementById('editUserModal');
                let editUserModalUnderlay = editUserModal.parentNode;
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
                        eventBus.emit('close-modal');
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
                    // this.rows = response.data;
                    
                    // Close the modal
                    eventBus.emit('close-modal');
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
                }).catch((error) => {
                    console.log(error);
                })

                // Close the modal
                eventBus.emit('close-modal');
            },
            
            closeModal() {
                eventBus.emit('close-modal');
            }
        }
    }
</script>