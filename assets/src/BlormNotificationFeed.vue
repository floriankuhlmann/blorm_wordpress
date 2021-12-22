<template>
    <el-dropdown>
        <span class="el-dropdown-link BlormNotfificationMenu">
            <span class="material-icons">{{numberOfNotifications}}</span>&nbsp;<span class="material-icons">list</span>
        </span>
        <template #dropdown>
            <el-dropdown-menu>
                <blorm-notification-el-dropdown-menu v-for="notification in notifications" v-bind:notification="notification">

                </blorm-notification-el-dropdown-menu>
            </el-dropdown-menu>
        </template>
    </el-dropdown>
</template>

<script>

    import BlormNotificationElDropdownMenu from "./BlormNotificationElDropdownMenu.vue";
    export default {
        components: {
            BlormNotificationElDropdownMenu,
        },
        computed: {
            notifications () {
                let notifications = [
                    {
                        id: "0001",
                        event: "liked",
                        user: "fred",
                        object: "the post name",
                        isSeen: "false",
                    },
                    { id: "0002",
                        event: "shared",
                        user: "cindy",
                        object: "and this is the postname",
                        isSeen: "false",
                    },
                    { id: "0003",
                        event: "rebloged",
                        user: "buba hubu",
                        object: "postname extra",
                        isSeen: "false",
                    }

                    ];

                return notifications;
            },
            numberOfNotifications () {
                let notifications = [
                    {
                        id: "0001",
                        event: "liked",
                        user: "fred",
                        object: "the post name",
                        isSeen: "false",
                    },
                    { id: "0002",
                        event: "shared",
                        user: "cindy",
                        object: "and this is the postname",
                        isSeen: "false",
                    },
                    { id: "0003",
                        event: "rebloged",
                        user: "buba hubu",
                        object: "postname extra",
                        isSeen: "false",
                    }

                ];

                let length = notifications.length;
                if (length < 1) { length = "none"; }
                if (length > 9) { length = "9_plus"; }
                return "filter_"+length;
            }
        },
        methods: {

        }
    };
</script>

<style>
    .BlormNotfificationMenu span.material-icons {
        color:red;
    }
</style>
