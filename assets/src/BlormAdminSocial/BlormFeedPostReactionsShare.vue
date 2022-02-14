<template>
       <span v-on:click="feedUser()" class="BlormFeedPostReactionsBlormhandle">{{getblormhandle}}</span>
</template>

<script>
    export default {
        props: ['shareReaction'],
        data: function () {
            return {}
        },
        mounted: function () {
            //console.log(this.shareReaction.user.data.data);
        },
        computed: {
            getblormhandle: function() {
                return this.shareReaction.user.data.data.blormhandle;
            },
        },
        methods: {
            feedUser: function () {
                console.log(this.shareReaction.user.id);
                this.$root.loadUserPage(this.shareReaction.user.id);
            },
        },
    }
</script>