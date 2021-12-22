<template>
    <div class="BlormFeedPostReactions" :data-activityid="post.activityId" :data-objecttype="post.object.type">
            <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane name="reblogs">
                    <template #label>
                        <span class="material-icons">filter_none</span>&nbsp;<span>{{noOfReblogs}}</span>
                    </template>
                    <blorm-feed-post-reactions-reblog v-for="reblogReaction in reblogReactions" v-bind:reblogReaction="reblogReaction"></blorm-feed-post-reactions-reblog>
                </el-tab-pane>
                <el-tab-pane name="shares">
                    <template #label>
                        <span class="material-icons">sync</span>&nbsp;<span>{{noOfShares}}</span>
                    </template>
                    <blorm-feed-post-reactions-share v-for="shareReaction in shareReactions" v-bind:shareReaction="shareReaction"></blorm-feed-post-reactions-share>
                </el-tab-pane>
                <el-tab-pane name="comments">
                    <template #label>
                        <span class="material-icons">comment</span>&nbsp;<span>{{noOfComments}}</span>
                    </template>
                    <blorm-feed-post-comments v-bind:post="post"></blorm-feed-post-comments>
                </el-tab-pane>
            </el-tabs>
    </div>
</template>

<script>
    import BlormFeedPostComments from "./BlormFeedPostComments.vue";
    import BlormFeedPostReactionsReblog from "./BlormFeedPostReactionsReblog.vue";
    import BlormFeedPostReactionsShare from "./BlormFeedPostReactionsShare.vue";
    export default {
        props: ['post'],
        components: {
            BlormFeedPostComments,
            BlormFeedPostReactionsShare,
            BlormFeedPostReactionsReblog,
        },
        data: function () {
            return {
                activeName: 'reblogs',
            }
        },
        mounted: function () {


        },
        computed: {
            // comments
            noOfComments: function () {
                if (typeof this.post.comments.noOfReactions === "undefined") {
                    return 0;
                }
                return this.post.comments.noOfReactions;
            },
            commentReactions: function() {
                let reactions = {};
                if (this.post.comments.hasReactions) {
                    this.post.comments.theReactions.forEach(function(currentReaction, index, allReactions){
                        reactions[index] = currentReaction;
                    });
                }
                return reactions;
            },
            // reblogs
            noOfReblogs: function () {
                if (typeof this.post.reblogs.noOfReactions === "undefined") {
                    return 0;
                }
                return this.post.reblogs.noOfReactions;
            },
            reblogReactions: function() {
                let reactions = {};
                if (this.post.reblogs.hasReactions) {
                    this.post.reblogs.theReactions.forEach(function(currentReaction, index, allReactions){
                        reactions[index] = currentReaction;
                    });
                }
                return reactions;
            },
            // shares
            noOfShares: function () {
                if (typeof this.post.shares.noOfReactions === "undefined") {
                    return 0;
                }
                return this.post.shares.noOfReactions;
            },
            shareReactions: function() {
                let reactions = {};
                if (this.post.shares.hasReactions) {
                    this.post.shares.theReactions.forEach(function(currentReaction, index, allReactions){
                        reactions[index] = currentReaction;
                    });
                }
                return reactions;
            },
        },
        methods: {
            hasShares: function () {
                return this.post.shares.hasReactions;
            },
            hasReblogs: function () {
                return this.post.reblogs.hasReactions;
            },
            hasComments: function () {
                return this.post.comments.hasReactions;
            },
            handleClick(tab, event) {
                console.log(tab, event)
            },
        },
    }
</script>