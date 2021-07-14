<template>
    <div class="BlormWidgetContainer">
        <div class="BlormFeedHeader">
            <div class="row">
                <div class="col s12">
                    What happened on your platform? Why don't you share it now?
                    <img :src="getIconUrl" class="blormIcon">
                </div>
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

    export default {
        components: {
            blormfeedpost,
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
                return this.$store.state.feed;
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
</style>
