<template>
    <div class="w-screen">
        <title-bar></title-bar>
        <div class="grid min-h-screen transition-all duration-500 ease-in-out">
            <appmenu></appmenu>
            <div class="p-5 w-full max-w-[960px] mx-auto">
                <Button colorClass="text-white" bgColorClass="bg-blue-600" marginClass="mr-4" callback="showCreateUserModal">Add user</Button>
                <ListGroup v-if="users.length > 0" :listId="'my-chores-list'" class="mt-4">
                    <list-item v-for="userData in users" :key="userData.id" :listItem="userData" :draggable="false" :selectable="false" class="flex border border-slate-400">
                        <div class="flex">
                            <span class="h-8 p-1">{{userData.name}}</span>
                        </div>
                        <template v-slot:actions>
                            <div class="divide-x divide-solid divide-white">
                                <Button colorClass="text-white" bgColorClass="bg-blue-600" widthClass="w-10" paddingClass="px-0 py-2" callback="showChangeCredentialsModal" v-bind:args="userData.id">
                                    <Icon class="fas fa-key"/>
                                </Button>
                                <Button colorClass="text-white" bgColorClass="bg-blue-600" widthClass="w-10" paddingClass="px-0 py-2" callback="showEditUserModal" v-bind:args="userData.id">
                                    <Icon class="fas fa-edit"/>
                                </Button>
                                <Button colorClass="text-white" bgColorClass="bg-red-600" widthClass="w-10" paddingClass="px-0 py-2" callback="removeUser" v-bind:args="userData.id">
                                    <Icon class="fas fa-trash"/>
                                </Button>
                            </div>
                        </template>
                    </list-item>
                </ListGroup>
                <modal id="createUserModal">
                    <template v-slot:header>
                        Create User
                    </template>
                    <div>
                        <notification v-if="typeof modalNotice === 'object'" v-bind:notice="modalNotice"></notification>
                        <form id="createUserForm" :key="createUserFormKey">
                            <FormInput v-for="formField in createUserModalForm" :key="formField.identifier"
                                :identifier="formField.identifier"
                                :type="formField.type"
                                :elementLabel="formField.label"
                                :errors="formField.errors"
                                :value="formField.value"
                                :callback="formField.callback"
                            ></FormInput>
                        </form>
                    </div>
                    <template v-slot:footer>
                        <footer>
                            <Button colorClass="text-white" bgColorClass="bg-blue-600" marginClass="mr-4" callback="createUser">Create user</Button>
                            <Button colorClass="text-white" bgColorClass="bg-red-600" marginClass="mr-4" callback="closeModal">Close</Button>
                        </footer>
                    </template>
                </modal>
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
    import TitleBar from '../components/TitleBar.vue';
    import Icon from "../components/Icon.vue";
    
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

            this.mainMenuIsOpen = false;
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
                createUserModalForm: {
                    name: {
                        identifier: 'name',
                        label: 'Name',
                        type: 'text',
                        errors: '',
                        value: '',
                        callback: 'updateNameFieldValue'
                    },
                    username: {
                        identifier: 'username',
                        label: 'Username',
                        type: 'text',
                        errors: '',
                        value: '',
                        callback: 'updateUserNameFieldValue'
                    },
                    email: {
                        identifier: 'email',
                        label: 'Email',
                        type: 'text',
                        errors: '',
                        value: '',
                        callback: 'updateEmailFieldValue'
                    },
                    password: {
                        identifier: 'password',
                        label: 'Password',
                        type: 'password',
                        errors: '',
                        value: '',
                        callback: 'updatePasswordFieldValue'
                    },
                    passwordconfirm: {
                        identifier: 'passwordconfirm',
                        label: 'Confirm Password',
                        type: 'password',
                        errors: '',
                        value: '',
                        callback: 'updateConfirmPasswordFieldValue'
                    }
                },
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
            Button,
            FormInput,
            ListItem,
            ListGroup,
            TitleBar,
            Icon
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

            showCreateUserModal(el) {
                
                let createUserModal = document.getElementById('createUserModal');
                let createUserModalUnderlay = createUserModal.parentNode;

                createUserModal.classList.add('visible');
                createUserModal.classList.remove('invisible');
                createUserModalUnderlay.classList.add('visible');
                createUserModalUnderlay.classList.remove('invisible');
            },

            createUser() {

                let userData = {
                    name: this.createUserModalForm.name.value,
                    username: this.createUserModalForm.username.value,
                    email: this.createUserModalForm.email.value,
                    password: this.createUserModalForm.password.value,
                    confirm_password: this.createUserModalForm.passwordconfirm.value
                }

                axios.post('/api/register', userData).then((response) => {
                    // When an admin creates a user, we want to manually Create
                    // the user record from the data sent and add it to the users
                    // array.
                    this.users.push(userData)

                    this.createUserModalForm.name.value = '';
                    this.createUserModalForm.username.value = '';
                    this.createUserModalForm.email.value = '';
                    this.createUserModalForm.password.value = '';
                    this.createUserModalForm.passwordconfirm.value = '';

                    this.closeModal();

                }).catch((error) => {
                    if (error.response) {
                        this.registerFormNotification = {
                            message: error.response.data.message,
                            status: error.response.status
                        };

                        this.errors = error.response.data.errors;

                        for (const property in error.response.data.errors) {
                            if (property === 'message') {
                                continue;
                            }

                            this.registerFormData[property].errors = error.response.data.errors[property];
                        }
                    }
                });

            },

            showEditUserModal(userId) {

                let editUserModal = document.getElementById('editUserModal');
                let editUserModalUnderlay = editUserModal.parentNode;
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

            showChangeCredentialsModal(userId) {
                let changeCredentialsModal = document.getElementById('updateUserCredentialsModal');
                let changeCredentialsModalUnderlay = changeCredentialsModal.parentNode;
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

            removeUser(userId) {

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
            },

            updateNameFieldValue(elValue) {
                this.createUserModalForm.name.value = elValue;
            },

            updateUserNameFieldValue(elValue) {
                this.createUserModalForm.username.value = elValue;
            },
            
            updateEmailFieldValue(elValue) {
                this.createUserModalForm.email.value = elValue;
            },

            updatePasswordFieldValue(elValue) {
                this.createUserModalForm.password.value = elValue;
            },

            updateConfirmPasswordFieldValue(elValue) {
                this.createUserModalForm.passwordconfirm.value = elValue;
            }
        }
    }
</script>