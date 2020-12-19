<template>
    <div v-bind:id="id" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create a chore</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-on:click.prevent="closeWithoutSaving">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createChoreForm">
                    <div class="form-group">
                        <label for="chore">Chore:</label>
                        <input id="chore" class="form-control" type="text">
                    </div>
                    <div class="form-group">
                        <label for="pointvalue">Point value:</label>
                        <input id="pointvalue" class="form-control" type="number">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" v-on:click.prevent="saveChanges">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click.prevent="closeWithoutSaving">Close</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['id'],
    methods: {
        'saveChanges': function(){
            let formEls = document.querySelectorAll('.form-control');
            let choreData = {};

            console.log("calling saveChanges!");

            formEls.forEach((el) => {
                choreData[el.id] = el.value;
            });

            axios({
                method: 'post',
                url: '/api/chores',
                data: choreData,
                headers: {
                    authorization: 'Bearer ' + localStorage.getItem('authtoken')
                }
            }).then((response) => {
                console.log(response);
                console.log(this);

                // It's ugly but it works...
                // Overall, this isn't ideal because we're making
                // two requests per interaction, a POST and a GET...
                // If this is intended to be a multi-user (concurrent), this might be what we have to do.
                // At this point, I'm not going to play with server sent events or web workers.
                this.$parent.fetchChoresCollection();

                // Doesn't work so well...
                this.$el.style.display = 'none';
            });
        },

        'closeWithoutSaving': function() {
            this.$el.style.display = 'none';
        }
    }
}
</script>