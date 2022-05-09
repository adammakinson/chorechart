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
        getNotificationColorClass() {
            let notificationColorClass = 'bg-red-200';

            console.log(this.notice);
            
            // I want to use success, danger, and info primarily
            // If the type is specified, use that.
            if (this.notice.type) {
                console.log('getting color class from type');

                notificationColorClass = this.getNotificationColorClassFromType(this.notice.type);
            }

            if (this.notice.status) {
                notificationColorClass = this.getNotificationColorClassFromStatus(this.notice.status);
            }

            return notificationColorClass;
        },

        getNotificationColorClassFromType(noticetype) {
            let notificationColorClass = 'bg-blue-300';
            
            if(noticetype == 'success'){
                console.log('setting it to green');
                notificationColorClass = 'bg-green-200';
            } else if (noticetype == 'error') {
                notificationColorClass = 'bg-red-200';
            }

            return notificationColorClass;
        },

        getNotificationColorClassFromStatus(status) {
            let statusNumber = parseInt(status);
            let notificationColorClass = 'bg-blue-300';

            if(statusNumber > 199 && statusNumber < 300) {
                notificationColorClass = 'bg-green-200';
            }

            if(statusNumber >= 400) {
                notificationColorClass = 'bg-red-200';
            }

            return notificationColorClass;
        },
    }
}
</script>