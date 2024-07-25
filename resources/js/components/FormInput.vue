<template>
    <div>
        <label :for="id" class="block">
            {{elementLabel}}: <span v-if="errors" class="text-red-600">{{errors[0]}}</span>
        </label>
        <input :type="type" :name="identifier" v-model="value" :id="id" class="block border h-8 w-full pl-2" @keyup="handleKeyUp" @change="handleChangeEvent">
    </div>
</template>

<script>
import eventBus from '../eventBus.js';

export default {
    props: [
        'errors',
        'type',
        'id',
        'identifier',
        'elementLabel',
        'value',
        'callback'
    ],

    data() {
        return {
            elementCallback: this.callback
        }
    },

    methods: {
        handleChangeEvent(e) {

            eventBus.emit('callback', {"callback": this.elementCallback, "args": this.value});
        }
    }
}
</script>