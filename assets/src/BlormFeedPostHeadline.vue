<template>
    <div class="blorm-feed-post-headline">
        <div style="margin-bottom: 0.5rem;">
            <i style="color:grey">{{renderDate}}</i>
        </div>
        <div>
            <img :src="renderIcon" style="height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;">
            <template v-if="isOwner">
                <b>{{renderUser}} {{renderAction}}</b>
            </template>
            <template v-else>
                <b><span v-on:click="feedUser()" class="BlormFeedHeadlineUser">{{renderUser}}</span> {{renderAction}}</b>
            </template>
        </div>
    </div>
    <span v-if="isOwner">
        <template v-if="post.object.verb === 'create'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="postDelete(post.activityId)">delete</button>
            </div>
        </template>
        <template v-if="post.object.verb === 'reblog'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="reblogUndo(post.activityId)">undo</button>
            </div>
        </template>
        <template v-if="post.object.verb === 'share'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="shareUndo(post.activityId)">undo</button>
            </div>
        </template>
    </span>
</template>

<script>
    import moment from 'moment'

    export default {
        props: ['post'],
        computed: {
            renderDate: function() {
                let date = new Date(this.post.object.time);
                return moment(date.getTime() + 60*60*1000*2).fromNow();
            },
            renderIcon: function() {
                let icon = "";
                let imgsrc = templateUrl+"/blorm/assets/icons/";
                switch (this.post.object.verb) {
                    case "share":
                        icon = "circle-sync-backup-1-glyph.png";
                        break;
                    case "reblog":
                        icon = "editor-copy-2-duplicate-outline-stroke.png";
                        break;
                    case "create":
                        icon = "other-arrow-right-other-outline-stroke.png";
                        break;
                    default:
                        imgsrc = "other-arrow-right-other-outline-stroke.png";
                        break;
                }
                return imgsrc + icon;
            },
            renderAction: function() {
                let action = "";
                switch (this.post.object.verb) {
                    case "share":
                        action = "shared this";
                        break;
                    case "reblog":
                        action = "reblogged this";
                        break;
                    case "create":
                        action = "posted this";
                        break;
                    default:
                        action = "";
                        break;
                }
                return action;
            },
            renderUser: function() {
                if (this.post.isOwner) {
                    return "You";
                }
                return this.post.actor.name;
            },
            isOwner: function() {
                return this.post.isOwner;
            },
        },
        methods: {
            feedUser: function() {
                console.log(this.post.actor.id);
                this.$root.loadUserPage(this.post.actor.id);
            },
            postDelete: function (activityId) {
                let $this = this;
                let responsePromise = this.$root.postDelete(activityId);
                responsePromise.then(function() {
                    $this.$root.feedTimeline();
                }, function (response) {
                    $this.logError(response);
                });
            },
            reblogUndo: function (activityId) {
                let $this = this;
                let responsePromise = this.$root.reblogUndo(activityId);
                responsePromise.then(function() {
                    $this.$root.feedTimeline();
                }, function (response) {
                    $this.logError(response);
                });
            },
            shareUndo: function (activityId) {
                let $this = this;
                let responsePromise = this.$root.shareUndo(activityId);
                responsePromise.then(function() {
                    $this.$root.feedTimeline();
                }, function (response) {
                    $this.logError(response);
                });
            },
            logError: function(response) {
                this.$root.logError("reblogUndo", response);
            }
        },
    }
</script>