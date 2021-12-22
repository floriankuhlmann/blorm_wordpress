<template>
    <div class="BlormFeedPostReactionsLike">
        BlormFeedPostReactionsLike
    </div>
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
    }
</script>