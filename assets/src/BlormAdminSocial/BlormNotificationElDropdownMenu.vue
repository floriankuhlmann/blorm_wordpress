<template>
      <el-dropdown-item v-on:click="updateReadClassObject">
        <span :class="{ blormNotificationElDropdownMenuIsRead: blormNotificationElDropdownMenuIsRead }">
        <i>
          <span v-on:click="showFeedUser()" class="blorm-notification-el-dropdown-menu-link">{{getUserName}}</span>
        </i>{{getVerb}}
        <i>
          <span v-on:click="showSinglePost()" class="blorm-notification-el-dropdown-menu-link">{{getObject}}</span>
        </i>
        </span>
      </el-dropdown-item>
    <hr>
</template>

<script>
    export default {
        data() {
            return {
              blormNotificationElDropdownMenuIsRead: false,
            }
        },
        mounted: function() {
          if (this.notification.is_seen === true) {
            this.blormNotificationElDropdownMenuIsRead = true;
          }
        },
        components: {},
        props: ['notification'],
        methods: {
          updateReadClassObject: function () {
            console.log(this.blormNotificationElDropdownMenuIsRead);
            if (this.blormNotificationElDropdownMenuIsRead === false) {
              this.blormNotificationElDropdownMenuIsRead = true;
              this.$store.commit('addSeenNotification', this.notification.notificationGroupId);
              console.log(this.$store.state.notificationsRead);
              console.log(this.blormNotificationElDropdownMenuIsRead);
            }
          },
          showFeedUser: function () {
            this.$root.loadUserPage(this.notification.actor.id);
          },
          showSinglePost: function () {
            this.$store.commit('addReadNotification', this.notification.notificationGroupId);
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
                return "shared your post&nbsp;";
              case "follow":
                return "is following you now";
              case "reblog":
                return "rebloged your post&nbsp;";
            }
          },
          getClassReadingStatus: function () {
            if (this.blormNotificationElDropdownMenuIsRead === "true") {
              return "isRead";
            }
            return "isNotRead";
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
    .blormNotificationElDropdownMenuIsRead {
        color: #aaa;
    }

    .blormNotificationElDropdownMenuIsRead .blorm-notification-el-dropdown-menu-link {
      color:  #aaa;
      text-decoration: underline;
    }
</style>