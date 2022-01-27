<template>
    <div class="blorm-feed-comment>">
        <blorm-feed-post-single-comment v-for="commentItem in latestReactionsComments" v-bind:commentItem="commentItem"></blorm-feed-post-single-comment>
    </div>
    <div class="BlormFeedAction--comment" :data-id="post.activityId">
        <div contenteditable class="BlormFeedAction--comment-textbox" v-html="newComment" v-on:keyup="commentchanged" v-on:blur="commentchanged" v-on:paste="commentchanged" v-on:delete="commentchanged" v-on:focus="commentchanged">
        </div>
        <div class="BlormFeedAction">
            <button v-on:click="postComment">comment</button>
        </div>
    </div>
</template>

<script>
    import BlormFeedPostSingleComment from "./BlormFeedPostComment.vue";
    export default {
        name: 'BlormFeedComment',
        props: ['post'],
        components: {
            BlormFeedPostSingleComment,
        },
        data: function() {
            return {
                newComment:  this.$root.initCommentText,
            }
        },
        computed: {
            latestReactionsComments: function() {
                return this.post.latestReactions.comment;
            }
        },
        methods: {
            commentchanged: function(event) {
                if (jQuery(event.target).html() === this.newComment ) {
                    jQuery(event.target).html("");
                }
                this.$root.commentdata_text = jQuery(event.target).html().trim();
                this.$root.commentdata_id = jQuery(event.target).parent().data('id');
            },
            postComment: function (event) {
                this.$root.postComment(
                    this.$root.commentdata_text,
                    this.$root.commentdata_id
                );
            },
            feedUser: function(id) {
                console.log(feedUser);
                this.$root.loadUserPage(id);
            },
        }
    };
</script>