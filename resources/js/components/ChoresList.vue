<template>
        <!-- <div>
        <table id="choresTable">
            <thead>
                <th>Points</th>
                <th>Chore</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <tr v-for="chore in chores" v-bind:key="chore.id">
                    <td>{{chore.pointvalue}}</td>
                    <td>{{chore.chore}}</td>
                        <a href="#"><span class="fas fa-check"></span></a>
                        <a href="#"><span class="fas fa-edit"></span></a>
                        <a href="#"><span class="fas fa-trash"></span></a>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <datatable :columns="columns" :data="rows"></datatable>
        </div> -->
        <div>
            <h1>Chore chart</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal">Add chore</button>
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
        </div>
</template>

<script>
export default {
    props: ['id'],

    data() {
        return {
            chores: null,

            columns: [],

            rows: [],

            authtoken: ''
        }
    },

    mounted() {
        let authToken;
            
        // authToken = 'Bearer ' + $('#authtoken')[0].dataset.authToken;
        authToken = 'Bearer ' + localStorage.getItem('authtoken');
        
        // me.authtoken = authToken;
        this.authtoken = authToken;
        
        this.fetchChoresCollection();
    },

    methods: {
        fetchChoresCollection() {

            console.log(this.authtoken);

            axios.get('/api/chores', {
                headers: {
                    authorization: this.authtoken
                }
            }).then((response) => {

                console.log(response.data);

                // this.chores = choresObj;
                this.chores = response.data;

                this.columns = [
                    {label: 'id', field: 'id'},
                    {label: 'chore', field: 'chore'},
                    {label: 'pointvalue', field: 'pointvalue'}
                    // {
                    //     label: 'actions', 
                    //     representedAs: row => `<span class="fas fa-check"></span>`
                    // }
                ];

                this.rows = response.data;
            });
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