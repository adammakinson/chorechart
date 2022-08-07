/**
 * mitt: https://www.npmjs.com/package/mitt
 */
import mitt from 'mitt';

let emitter = mitt();

emitter.$on = emitter.on;
emitter.$emit = emitter.emit;

const eventBus = emitter;

export default eventBus;