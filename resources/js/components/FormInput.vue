<template>
    <div>
        <label :for="identifier" class="block">
            {{elementLabel}}: <span v-if="errors" class="text-red-600">{{errors[0]}}</span>
        </label>
        <input :type="type" :name="identifier" v-model="elementValue" :id="identifier" class="block border h-8 w-full pl-2" @change="handleChangeEvent">
    </div>
</template>

<script>
import eventBus from '../eventBus.js';

export default {
    props: [
        'errors',
        'type',
        'identifier',
        'elementLabel',
        'value',
        'callback'
    ],

    data() {
        return {
            elementValue: this.value,
            elementCallback: this.callback
        }
    },

    methods: {
        handleChangeEvent(e) {

            console.log(e);

            eventBus.$emit('callback', this.elementCallback, this.elementValue);
        }
    }
}
</script>