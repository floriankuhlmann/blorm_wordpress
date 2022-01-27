<template>
  <span v-if="showPost">
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
      <span v-if="showPostContent">
        <a :href="getPostUrl">
          <blorm-feed-post-image v-bind:postImage="getPostImage"></blorm-feed-post-image>
        </a>
        <div class="BlormFeedPostContent">
          <a :href="getPostUrl">
            <p>{{getPostText}}</p>
          </a>
        </div>
      </span>
      <span v-if="showPostActions">
        <blorm-feed-post-actions v-bind:post="post"></blorm-feed-post-actions>
      </span>
      <span v-if="showPostReactions">
        <blorm-feed-post-reactions v-bind:post="post"></blorm-feed-post-reactions>
      </span>
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
          showPost: function () {
            if (this.post.object.verb === 'reblog' && !this.post.isOwner) {
              return false;
            }
            if (this.post.object.verb === 'create' || 'reblog' || 'share') {
              return true;
            }
            return false;
          },
          showPostContent: function () {
            if (this.post.object.verb === 'create') {
              return true;
            }
            if (this.post.object.verb === 'share' && this.post.isOwner) {
              return false;
            }
            if (this.post.object.verb === 'share' && !this.post.isOwner) {
              return true;
            }
            return false;
          },
          showPostActions: function () {
            return !this.post.isOwner;
          },
          showPostReactions: function () {
            if (this.post.object.verb === 'create' || 'share') {
              return true;
            }
            return false;
          },
        },// computed
    };
</script>
<style>

</style>
