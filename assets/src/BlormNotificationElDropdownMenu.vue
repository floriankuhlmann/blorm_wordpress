<template>
    <el-dropdown-item v-bind:class="readClassObject" v-on:click="updateReadClassObject">
      <b>
        <span v-on:click="showFeedUser()" class="blorm-notification-el-dropdown-menu-link">{{getUserName}}</span>
      </b> {{getVerb}} your post
      <b>
        <span v-on:click="showSinglePost()" class="blorm-notification-el-dropdown-menu-link">{{getObject}}</span>
      </b>
    </el-dropdown-item>
    <hr>
</template>

<script>
    export default {
        data() {
            return {
                readClassObject: {
                    BlormNotificationElDropdownMenuIsRead: false,
                }
            }
        },
        mounted() {
          console.log("readClassObject.active");
          console.log(this.readClassObject);
          console.log(this.notification);
        },
        components: {
        },
        props: ['notification'],
        methods: {
          updateReadClassObject: function () {
            if (this.readClassObject.BlormNotificationElDropdownMenuIsRead === false) {
              this.readClassObject.BlormNotificationElDropdownMenuIsRead = true;
              this.$store.commit('addReadNotification', this.notification.notificationGroupId);

              console.log("updateReadClassObject notificationsRead");
              console.log(this.$store.state.notificationsRead);
            }
          },
          showFeedUser: function () {
            this.$root.loadPage(this.notification.actor.id);
          },
          showSinglePost: function () {
            this.$root.loadSinglePost(this.notification.activityId, this.notification.actor.id);
          },
        },
        computed: {
          getNotification: function() {
            return this.notification;
          },
          getUserName: function () {
            return this.notification.actor.data.blormhandle;
          },
          getObject: function () {
            return this.notification.title;
          },
          getVerb: function() {
            switch (this.notification.verb) {
              case "share":
                return "shared";
              case "follow":
                return "followed";
              case "reblog":
                return "rebloged";
            }
          },
          getClassReadingStatus: function () {
            if (this.notification.isSeen === "false") {
              return "notRead";
            }
            return "isRead";
          },
        },
    };
</script>

<style>
    .el-dropdown-menu__item {
        color: #3c434a;
    }
    .el-dropdown-menu__item:focus, .el-dropdown-menu__item:not(.is-disabled):hover {
        background-color: #eeeadd;
        color: #3c434a;
    }
    .blorm-notification-el-dropdown-menu-link {
      color: #2271b1;
        text-decoration: underline;
    }
    .blorm-notification-el-dropdown-menu-link:hover {
        color:#000;
    }
</style>