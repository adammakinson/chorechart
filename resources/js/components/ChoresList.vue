<template>
    <div>
        <h1>Chore chart</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal" v-on:click="showAddChoreModal">Add chore</button>
        <hr>
        <datatable :columns="columns" :data="rows">
            <template slot-scope="{row, columns}">
                <tr>
                    <td>{{row.id}}</td>
                    <td>{{row.chore}}</td>
                    <td>{{row.pointvalue}}</td>
                    <td>
                        <span v-on:click="handleCheckClick" class="fas fa-check"></span>
                        <span v-on:click="handleEditClick" class="fas fa-edit"></span>
                        <span v-on:click="handleTrashClick" class="fas fa-trash"></span>
                    </td>
                </tr>
            </template>
        </datatable>
        <modal id="createChoreModal"></modal>
    </div>
</template>

<script>
import Modal from "./Modal";

export default {
    props: ['id'],

    data() {
        return {
            chores: null,

            columns: [],

            rows: []
        }
    },

    components: {
        Modal
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
            console.log(el);
        }
    }
};
</script>