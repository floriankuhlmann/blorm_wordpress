<template>
    <div class="BlormWidgetContainer">
        <div class="BlormFeedHeader BlormContentBoxWhite">
            <div class="BlormFeedHeaderLeft">
            &nbsp;   <a href="#"><span class="material-icons" v-on:click="reloadPage()">replay</span></a>
                <a href="/wp-admin/admin.php?page=blorm-plugin"><span class="material-icons">settings</span></a>
            </div>
            <div class="BlormFeedHeaderRight">
                    <blorm-notification-feed></blorm-notification-feed>
            </div>
        </div>
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
    import BlormNotificationFeed from "./BlormNotificationFeed.vue";

    export default {
        components: {
            blormfeedpost,
            BlormNotificationFeed,
        },
        data() {
            return {
                blormusername: "",
                feedHasPosts: true,
            };
        },
        props: [],
        methods: {
          reloadPage: function () {
              this.$root.reloadAccountPage();
          }
        },
        computed: {
            posts () {
                let posts = [];
                //let $this = this;
                Array.from(this.$store.state.feed).forEach(function(post){

                    if (post.error === false ) {
                       posts.push(post);
                    }
                    if (post.error === true ) {
                      if (this.$store.state.logToConsole) {
                        console.log("feed-error: post is not vaild:");
                        console.log(post.activityId );
                        console.log(post.errortype);
                      }
                    }
                },this);
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
.BlormFeedHeader {
  display: block;
}
.BlormFeedHeader div {
  width: 50%;
  display: inline-block;
}

.BlormFeedHeaderLeft {
  text-align: left;
}

.BlormFeedHeaderLeft a {
  font-size: 24px;
}

.BlormFeedHeaderRight a:hover,
.BlormFeedHeaderLeft a:hover {
  color: #3c434a;
}

.BlormFeedHeaderRight {
  text-align: right;
}

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
