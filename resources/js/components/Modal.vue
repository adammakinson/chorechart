<template>
    <div v-bind:id="id" @close-modal="closeModal" class="modal invisible absolute top-36 bg-white w-auto mr-5 sm:w-96 border rounded-md shadow-md" tabindex="-1" role="dialog">
        <header class="flex justify-between border-b border-slate-200 p-4">
            <h5>
                <slot name="header"></slot>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-on:click.prevent="closeModal">
                <span aria-hidden="true">&times;</span>
            </button>
        </header>
        <div class="p-4">
            <slot></slot>
        </div>
        <footer class="p-4">
            <slot name="footer"></slot>
        </footer>
    </div>
</template>

<script>
import eventBus from '../eventBus';
export default {

    props: ['id'],

    methods: {
        'closeModal': function() {
            let formEls = document.querySelectorAll('.form-control');

            this.$el.classList.remove('visible');
            this.$el.classList.add('invisible');
        }
    },

    created() {
        eventBus.$on("close-modal", () => {
            this.closeModal();
        });
    }
}
</script>