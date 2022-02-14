<template>
    <div class="BlormFeedPostHeadline">
        <div style="margin-bottom: 0.5rem;">
            <i style="color:grey">{{renderDate}}</i>
        </div>
        <div>
            <img :src="renderIcon" style="height: 1rem; margin-bottom:-0.25rem; margin-right: 0.25rem;">
            <template v-if="isAccount">
                <b>Thank you for {{renderAction}}</b>
            </template>
            <template v-else>
                <b><span v-on:click="feedUser()" class="BlormFeedHeadlineUser">{{renderUser}}</span> {{renderAction}}</b>
            </template>
        </div>
    </div>
    <span v-if="isAccount">
        <template v-if="post.object.verb === 'create'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="undoPost('delete',post.activityId)">delete</button>
            </div>
        </template>
        <template v-if="post.object.verb === 'reblog'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="undoPost('delete',post.activityId)">undo</button>
            </div>
        </template>
        <template v-if="post.object.verb === 'share'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="undoPost('delete',post.activityId)">undo</button>
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
                let imgsrc = blormPluginUrl+"/assets/icons/";
                switch (this.post.object.verb) {
                    case "share":
                        //             <span class="material-icons">sync</span>
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
                      if (this.isAccount) {
                        action = "sharing:";
                      } else {
                        action = "shared:";
                      }
                      break;
                    case "reblog":
                      if (this.isAccount) {
                        action = "rebloging:";
                      } else {
                        action = "rebloged:";
                      }
                      break;
                    case "create":
                      if (this.isAccount) {
                        action = "posting:";
                      } else {
                        action = "posted:";
                      }
                      break;
                    default:
                      action = "";
                      break;
                }
                return action;
            },
            renderUser: function() {
                if (this.isAccount) {
                    return "you";
                }
                return this.post.actor.name;
            },
            isAccount: function() {
              return this.$root.isAccountDataOnDisplay(this.post.actor.id);
            },
        },
        methods: {
            feedUser: function() {
                console.log(this.post.actor.id);
                this.$root.loadUserPage(this.post.actor.id);
            },
            undoPost: function (verb, activityId) {
              let $this = this;
              let responsePromise = new Promise(function() {
                $this.$root.reloadAccountPage();
              }, function (response) {
                $this.logError(response);
              });
              switch(verb) {
                case "delete":
                  responsePromise = this.$root.postDelete(activityId);
                  break;

                case "reblog":
                  responsePromise = this.$root.reblogUndo(activityId);
                  break;

                case "share":
                  responsePromise = this.$root.shareUndo(activityId);
                  break;
              }
            },
            logError: function(response) {
                this.$root.logError("reblogUndo", response);
            }
        },
    }
</script>