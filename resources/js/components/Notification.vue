<template>
    <div v-if="typeof notice === 'object'" class="p-4" :class="notificationColorClass">{{notice.message}}</div>
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

        /**
         * Get the notification color class from the type or the status if type
         * isn't specified
         */
        getNotificationColorClass() {
            let notificationColorClass = 'bg-red-200';
            
            // If the type success, danger, or info is specified, use that.
            if (this.notice.type) {
                notificationColorClass = this.getNotificationColorClassFromType(this.notice.type);
            }

            if (this.notice.status) {
                notificationColorClass = this.getNotificationColorClassFromStatus(this.notice.status);
            }

            return notificationColorClass;
        },


        /**
         * Get the color by notice type
         * 
         * @param noticetype - a string of success, error or info
         */
        getNotificationColorClassFromType(noticetype) {
            let notificationColorClass = 'bg-blue-300';
            
            if (noticetype == 'success') {
                notificationColorClass = 'bg-green-200';
            } else if (noticetype == 'error') {
                notificationColorClass = 'bg-red-200';
            }

            return notificationColorClass;
        },

        /**
         * Get the color by HTTP status code
         * 
         * @param status - a http response code
         */
        getNotificationColorClassFromStatus(status) {
            let statusNumber = parseInt(status);
            let notificationColorClass = 'bg-blue-300';

            if (statusNumber > 199 && statusNumber < 300) {
                notificationColorClass = 'bg-green-200';
            }

            if (statusNumber >= 400) {
                notificationColorClass = 'bg-red-200';
            }

            return notificationColorClass;
        }
    }
}
</script>