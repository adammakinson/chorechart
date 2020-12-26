<template>
    <div>
        <user-status-bar></user-status-bar>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
        <hr>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr>
                    <!-- <td>{{row.id}}</td> -->
                    <td>{{row.chore}}</td>
                    <td>{{row.pointvalue}}</td>
                    <td>
                        <span v-on:click="handleCheckClick" class="fas fa-check" v-bind:data-id="row.id"></span>
                        <span v-on:click="showEditChoreModal" class="fas fa-edit" v-bind:data-id="row.id"></span>
                        <span v-on:click="handleTrashClick" class="fas fa-trash" v-bind:data-id="row.id"></span>
                    </td>
                </tr>
            </template>
        </datatable>
        <modal id="createChoreModal">
            <template v-slot:header>
                Create a chore
            </template>
            <div class="modal-body">
                <form id="createChoreForm">
                    <div class="form-group">
                        <label for="chore">Chore:</label>
                        <input id="chore" name="chore" class="form-control" type="text" v-bind:value="choreFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" name="pointvalue" class="form-control" type="number" v-bind:value="pointFieldValue">
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="createChore">Create chore</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="sendEventBusMessage">Close</button>
                </footer>
            </template>
        </modal>
        <modal id="editChoreModal">
            <template v-slot:header>
                Create a chore
            </template>
            <div class="modal-body">
                <form id="createChoreForm">
                    <div class="form-group">
                        <label for="editchore">Chore:</label>
                        <input id="editchore" class="form-control" type="text" name="chore" v-bind:value="choreFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="editpointvalue">Point value:</label>
                        <input id="editpointvalue" class="form-control" type="number" name="pointvalue" v-bind:value="pointFieldValue">
                    </div>
                </form>
            </div>
            <template v-slot:footer>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click.prevent="updateChore">Update chore</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="sendEventBusMessage">Close</button>
                </footer>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal";
import UserStatusBar from './UserStatusBar.vue';
import eventBus from '../eventBus';

export default {
    props: ['id'],

    data() {
        return {
            chores: null,

            columns: [],

            rows: [],

            choreFieldValue: '',

            pointFieldValue: '',

            activeElementId: ''
        }
    },

    components: {
        Modal,
        UserStatusBar
    },

    mounted() {

        if (this.$store.getters.getUserAuthToken) {
            
            this.fetchChoresCollection();
        } else {

            this.$router.push('login');
        }
    },

    methods: {
        fetchChoresCollection() {

            axios.get('/api/chores', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {

                this.chores = response.data;

                this.columns = [
                    // {label: 'id', field: 'id'},
                    {label: 'chore', field: 'chore'},
                    {label: 'pointvalue', field: 'pointvalue'}
                ];

                this.rows = response.data;
            });
        },

        showAddChoreModal() {
            let modalwindow = document.getElementById("createChoreModal");

            modalwindow.style.display = 'block';
        },

        showEditChoreModal(el) {
            let modalwindow = document.getElementById("editChoreModal");

            let currentElementName = el.target.parentNode.parentNode.children[1].innerText;
            let currentElementPoints = el.target.parentNode.parentNode.children[2].innerText;

            this.choreFieldValue = currentElementName;
            this.pointFieldValue = currentElementPoints;

            this.activeElementId = el.target.dataset.id;

            modalwindow.style.display = 'block';
        },

        handleCheckClick(el) {
            console.log(el);
        },

        handleTrashClick(el) {
            let itemId = el.target.dataset.id;

            axios.delete('/api/chores/' + itemId, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                
                console.log(response);

                this.chores = response.data;
                this.rows = response.data;
            });
        },

        'createChore': function(){
            let formEls = document.querySelectorAll('#createChoreModal .form-control');
            let choreData = {};

            formEls.forEach((el) => {
                choreData[el.name] = el.value;
            });

            axios({
                method: 'post',
                url: '/api/chores',
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.chores = response.data;
                this.rows = response.data;
                
                // Close the modal
                eventBus.$emit('close-modal');
            });
        },
        
        'updateChore': function(){
            let formEls = document.querySelectorAll('#editChoreModal .form-control');
            let choreData = {};

            formEls.forEach((formInput) => {
                choreData[formInput.name] = formInput.value;
            });

            axios({
                method: 'put',
                url: '/api/chores/' + this.activeElementId,
                data: choreData,
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                this.chores = response.data;
                this.rows = response.data;
                
                this.activeElementId = '';

                // Close the modal
                eventBus.$emit('close-modal');
            });
        },

        sendEventBusMessage() {
            eventBus.$emit('close-modal');
        }
    }
};
</script>