<template>
    <div>
        <user-status-bar></user-status-bar>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
        <hr>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr>
                    <td>{{row.id}}</td>
                    <td>{{row.chore}}</td>
                    <td>{{row.pointvalue}}</td>
                    <td>
                        <span v-on:click="handleCheckClick" class="fas fa-check" v-bind:data-id="row.id"></span>
                        <span v-on:click="handleEditClick" class="fas fa-edit" v-bind:data-id="row.id"></span>
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
                        <input id="chore" class="form-control" type="text" v-bind:value="choreFieldValue">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" class="form-control" type="number" v-bind:value="pointFieldValue">
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

            pointFieldValue: ''
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
                    {label: 'id', field: 'id'},
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

        handleCheckClick(el) {
            console.log(el);
        },
        
        handleEditClick(el) {
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
            let formEls = document.querySelectorAll('.form-control');
            let choreData = {};

            formEls.forEach((el) => {
                choreData[el.id] = el.value;
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

        sendEventBusMessage() {
            eventBus.$emit('close-modal');
        }
    }
};
</script>