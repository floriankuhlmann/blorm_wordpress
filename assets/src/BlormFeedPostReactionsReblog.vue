<template>
    <span v-on:click="feedUser()" class="BlormFeedPostReactionsBlormhandle">{{getblormhandle}}</span>
</template>

<script>

    export default {
        props: ['reblogReaction'],
        data: function () {
            return {}
        },
        mounted: function () {
            console.log(this.reblogReaction);
        },
        computed: {
            getblormhandle: function() {
                return this.reblogReaction.user.data.data.blormhandle;
            },
        },
        methods: {
            feedUser: function () {
                console.log(this.reblogReaction.user.id);
                this.$root.loadUserPage(this.reblogReaction.user.id);
            },
        },
    }
</script>