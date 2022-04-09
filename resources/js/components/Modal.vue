<template>
    <div v-bind:id="id" @close-modal="closeModal" class="modal" tabindex="-1" role="dialog">
        <div role="document">
            <div>
                <header>
                    <h5>
                        <slot name="header"></slot>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-on:click.prevent="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </header>
                <slot></slot>
                <slot name="footer"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
export default {

    props: ['id'],

    methods: {
        'closeModal': function() {
            let formEls = document.querySelectorAll('.form-control');

            this.$el.style.display = 'none';
        }
    },

    created() {
        eventBus.$on("close-modal", () => {
            this.closeModal();
        });
    }
}
</script>