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
            if (this.notice.type) {
                notificationColorClass = this.getNotificationColorClassFromType(this.notice.type);
            }

            if (this.notice.status) {
                notificationColorClass = this.getNotificationColorClassFromStatus(this.notice.status);
            }

            return notificationColorClass;
        },

        getNotificationColorClassFromType(noticetype) {
            let notificationColorClass = 'alert-info';
            
            if(noticetype == 'success'){
                notificationColorClass = 'alert-success';
            } else if (noticetype == 'error') {
                notificationColorClass = 'alert-danger';
            }

            return notificationColorClass;
        },

        getNotificationColorClassFromStatus(status) {
            let statusNumber = parseInt(status);
            let notificationColorClass = 'alert-info';

            if(statusNumber > 199 && statusNumber < 300) {
                notificationColorClass = 'alert-success';
            }

            if(statusNumber >= 400) {
                notificationColorClass = 'alert-danger';
            }

            return notificationColorClass;
        },
    }
}
</script>