<template>
    <div class="BlormWidgetContainer">
      <!-- <div class="BlormFeedHeader BlormContentBoxWhite">
            <div class="row">
                <div class="col s12">
                    <blorm-notification-feed></blorm-notification-feed>
                </div>
            </div>
        </div>-->
        <div id="BlormFeed" class="Blormfeed">
            <div class="row">
                <div v-if="feedHasPosts" class="col s12">
                    <blormfeedpost v-for="post in posts"
                            v-bind:key="post.id"
                            v-bind:post="post"
                    ></blormfeedpost>
                </div>
                <div v-else class="col s12 BlormfeedNoPostData">
                    No Posts available for this User
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import blormfeedpost from "./BlormFeedPost.vue";
    //import BlormNotificationFeed from "./BlormNotificationFeed.vue";

    export default {
        components: {
            blormfeedpost,
            //BlormNotificationFeed,
        },
        data() {
            return {
                blormusername: "",
                feedHasPosts: true,
            };
        },
        props: [],
        computed: {
            posts () {
                let posts = [];
                Array.from(this.$store.state.feed).forEach(function(post){

                    if (post.error === false ) {
                       posts.push(post);
                    }
                    if (post.error === true ) {
                        console.log("feed-error: post is not vaild:");
                        console.log(post.activityId );
                        console.log(post.errortype);
                    }
                });
                return posts;
            },
            getBlormHandle () {
              return this.$store.state.user.blormhandle;
            },
            getIconUrl () {
                return blormPluginUrl + "/assets/icons/circle-arrow_forward-next-glyph.png";
            }
        },
        updated: function () {
            this.$nextTick(function () {
                if (this.$store.state.feed.length == null) {
                    this.feedHasPosts = false
                }
            })
        }
    }
</script>

<style lang="css">
.BlormfeedNoPostData {
    margin-bottom: 1rem;
    padding: 0.5rem 0.5rem 0.5rem 0.5rem;
    border: 1px solid #eee;
    background-color: rgb(252, 252, 252);
    text-align: left;
    font-weight: bold;
}
#dashboard-widgets .postbox-container {
    width: 33%;
}
</style>
