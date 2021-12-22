<template>
    <span v-if="post.object.verb !== 'reblog'">
        <div class="BlormFeedPost BlormContentBoxWhite" :class="post.object.verb" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">
            <div class="BlormFeedEdit">
                <div class="BlormFeedEdit--Date">
                    <blorm-feed-post-headline v-bind:post="post"></blorm-feed-post-headline>
                    <hr class="BlormFeedBorder">
                </div>
          </div>
          <div class="BlormFeedTitle">
             <h2 class="BlormFeedTitle"><a :href="getPostUrl">{{getPostHeadline}}</a></h2>
          </div>
            <a :href="getPostUrl">
                <blorm-feed-post-image v-bind:postImage="getPostImage"></blorm-feed-post-image>
            </a>
            <div class="BlormFeedPostContent">
                <a :href="getPostUrl">
                <p>{{getPostText}}</p>
                </a>
            </div>

            <!-- <div class="BlormFeedURL">
                <a :href="getPostUrl"><i>read this</i></a>
            </div>-->
            <span v-if="!isOwner">
                <blorm-feed-post-actions v-bind:post="post"></blorm-feed-post-actions>
            </span>
            <blorm-feed-post-reactions v-bind:post="post"></blorm-feed-post-reactions>

        </div>
    </span>
</template>

<script>
    import BlormFeedPostHeadline from "./BlormFeedPostHeadline.vue";
    import BlormFeedPostImage from "./BlormFeedPostImage.vue";
    import BlormFeedPostActions from "./BlormFeedPostActions.vue";
    import BlormFeedPostReactions from "./BlormFeedPostReactions.vue";

    export default {
        name: 'BlormFeedPost',
        components: {
           BlormFeedPostImage,
           BlormFeedPostActions,
           BlormFeedPostReactions,
           BlormFeedPostHeadline,
        },
        props: ['post'],
        data: function() {
            return {
                newComment:  this.$root.initCommentText,
            }
        },
        computed: {
            getPostImage: function() {
                return this.post.object.image;
            },
            getPostText: function() {
                return this.post.object.text;
            },
            getPostHeadline: function() {
                return this.post.object.headline;
            },
            getPostUrl: function() {
                return this.post.object.url;
            },
            isOwner: function() {
                return this.post.isOwner;
            },
        },
    };
</script>
