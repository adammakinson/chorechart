<template>
    <div v-if="typeof notice === 'object'" class="alert mt-4" :class="notificationColorClass">{{notice.message}}</div>
</template>

<script>
export default {
    props: [
        'notice'
    ],

    data() {
        return {
            notificationColorClass: this.getNotificationColorClass()
        };

    },
    
    methods: {
        getNotificationColorClass() {
            let notificationColorClass = 'alert-info';
            
            // I want to use success, danger, and info primarily
            // If the type is specified, use that.
            if (this.notice.type == 'error') {
                notificationColorClass = 'alert-danger';
            }

            // If a type is not specified, and the notification has a status code,
            // use a status code mapping to determine what should be returned.
            if (this.notice.status == 403) {
                notificationColorClass = 'alert-danger';
            }

            return notificationColorClass;
        }
    }
}
</script>