<template>
    <div class="BlormFeedPostHeadline">
        <div style="margin-bottom: 0.5rem;">
            <i style="color:grey">{{renderDate}}</i>
        </div>
        <div>
            <img :src="renderIcon" style="height: 1rem; margin-bottom:-0.25rem; margin-right: 0.25rem;">
            <template v-if="post.isAccountOwner">
                <b>Thank you for {{renderAction}}</b>
            </template>
            <template v-else>
                <b><span v-on:click="feedUser()" class="BlormFeedHeadlineUser">{{renderUser}}</span> {{renderAction}}</b>
            </template>
        </div>
    </div>
    <span v-if="post.isAccountOwner">
        <template v-if="post.object.verb === 'create'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="undoPost('delete',post.activityId)">delete</button>
            </div>
        </template>
        <template v-if="post.object.verb === 'reblog'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="undoPost('reblogundo',post.activityId)">undo</button>
            </div>
        </template>
        <template v-if="post.object.verb === 'share'">
            <div class="BlormFeedEdit--Mod">
                <button v-on:click="undoPost('shareundo',post.activityId)">undo</button>
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
                      if (this.post.isAccountOwner) {
                        action = "sharing:";
                      } else {
                        action = "shared:";
                      }
                      break;
                    case "reblog":
                      if (this.post.isAccountOwner) {
                        action = "rebloging:";
                      } else {
                        action = "rebloged:";
                      }
                      break;
                    case "create":
                      if (this.post.isAccountOwner) {
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
                if (this.post.isAccountOwner) {
                    return "you";
                }
                return this.post.actor.name;
            },
            isAccount: function() {
              return this.$root.isAccountDataOnDisplay();
            },
        },
        methods: {
            feedUser: function() {
              let loadPromise = this.$root.loadUserData(this.post.actor.id);
              loadPromise.then(
                  response => {
                    this.$root.loadUserPage(this.$store.state.user);
                  },
                  error => {
                    alert(error);
                    this.$root.loadAccountPage();
                  }
              );
            },
            undoPost: function (verb, activityId) {
              let $this = this;
              //let responsePromise = new Promise(function (a){}, function (b){});
              let responsePromise = {};
              switch(verb) {
                case "delete":
                  responsePromise = this.$root.postDelete(activityId);
                  break;

                case "reblogundo":
                  responsePromise = this.$root.reblogUndo(activityId);
                  break;

                case "shareundo":
                  responsePromise = this.$root.shareUndo(activityId);
                  break;
              }
              responsePromise.then(
                  response => {
                      $this.$root.loadAccountPage();
                  },
                  error => {
                    $this.logError(response);
                  }
              );

            },
            logError: function(response) {
                this.$root.logError("reblogUndo", response);
            }
        },
    }
</script>