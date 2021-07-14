<template>
    <div class="BlormFeedPost" :class="post.object.verb" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">
        <div class="BlormFeedEdit">
            <div class="BlormFeedEdit--Date">
                <blorm-feed-post-headline v-bind:post="post"></blorm-feed-post-headline>
                <hr class="BlormFeedBorder">
            </div>
      </div>
      <div class="BlormFeedTitle">
         <h2 class="BlormFeedTitle"><a :href="getPostUrl">{{getPostHeadline}}</a></h2>
      </div>
        <blorm-feed-post-image v-bind:postImage="getPostImage"></blorm-feed-post-image>
        <div class="BlormFeedContent">
            <p>{{getPostText}}</p>
        </div>
        <div class="BlormFeedURL">
            <a :href="getPostUrl"><i>read this</i></a>
        </div>
        <span v-if="!isOwner">
            <blorm-feed-post-actions v-bind:post="post"></blorm-feed-post-actions>
        </span>
        <blorm-feed-post-comments v-bind:post="post"></blorm-feed-post-comments>
    </div>
</template>

<script>
    import BlormFeedPostHeadline from "./BlormFeedPostHeadline.vue";
    import BlormFeedPostImage from "./BlormFeedPostImage.vue";
    import BlormFeedPostComments from "./BlormFeedPostComments.vue";
    import BlormFeedPostActions from "./BlormFeedPostActions.vue";


    export default {
        name: 'BlormFeedPost',
        components: {
           BlormFeedPostImage,
           BlormFeedPostComments,
           BlormFeedPostActions,
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
