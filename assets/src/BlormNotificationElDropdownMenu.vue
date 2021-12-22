<template>
    <el-dropdown-item v-bind:class="readClassObject" v-on:click="updateReadClassObject" v-bind:command="getCommand"><b>{{getNotification.user}}</b> {{getNotification.event}} <b>{{getNotification.object}}</b></el-dropdown-item>
    <hr>
</template>


<script>
    export default {
        data() {
            return {
                readClassObject: {
                    BlormNotificationElDropdownMenuIsRead: false,
                }
            }
        },
        mounted() {
          console.log(this.readClassObject.active);
        },
        components: {
        },
        props: ['notification'],
        methods: {
            updateReadClassObject: function () {
                console.log("updateReadClassObject");
                console.log(this.readClassObject.active);
                this.readClassObject.BlormNotificationElDropdownMenuIsRead = true;
            }
        },
        computed: {
            getNotification: function() {
                return this.notification;
            },
            getCommand: function () {
                return this.notification.user;
            },
            getClassReadingStatus: function () {
                if (this.notification.isSeen === "false") {
                    return "notRead";
                }
                return "isRead";
            },

        },
    };
</script>

<style>
    .el-dropdown-menu__item {
        color: #3c434a;
    }
    .el-dropdown-menu__item:focus, .el-dropdown-menu__item:not(.is-disabled):hover {
        background-color: #eeeadd;
        color: #3c434a;
    }
</style>