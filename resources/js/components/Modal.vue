<template>
    <div class="modalWrapper invisible max-w-full max-h-full w-screen h-screen absolute bg-gray-800 bg-opacity-50 top-0 left-0 flex justify-center content-center">
        <div v-bind:id="id" @close-modal="closeModal" class="modal invisible absolute top-36 bg-white w-auto mr-5 sm:w-96 border rounded-md shadow-md" tabindex="-1" role="dialog">
            <header class="flex justify-between border-b border-slate-200 p-4">
                <h5>
                    <slot name="header"></slot>
                </h5>
                <Button colorClass="text-white" bgColorClass="bg-red-600" paddingClass="px-3 py-1" rounded="true" callback="closeModal">
                    <span aria-hidden="true" class="fas fa-times"></span>
                </Button>
            </header>
            <div class="p-4">
                <slot></slot>
            </div>
            <footer class="p-4">
                <slot name="footer"></slot>
            </footer>
        </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
import Button from '../components/Button.vue';
export default {

    props: ['id'],

    methods: {
        closeModal() {

            this.$el.classList.remove('visible');
            this.$el.classList.add('invisible');

            this.$el.firstElementChild.classList.remove('visible');
            this.$el.firstElementChild.classList.add('invisible');
        }
    },

    components: {
        Button
    },

    created() {

        // Is this thing needed???
        eventBus.on("close-modal", () => {
            this.closeModal();
        });

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
    }
}
</script>