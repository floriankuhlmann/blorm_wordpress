<template>
    <el-dropdown>
      <span class="el-dropdown-link BlormNotfificationMenu" v-bind:class="blormNotfificationMenuActive">
            <span class="material-icons">{{numberOfNotifications}}</span>&nbsp;<span class="material-icons">list</span>
      </span>
      <template #dropdown>
        <el-dropdown-menu v-if="hasNotifications">
          <blorm-notification-el-dropdown-menu v-for="notification in getNotifications" v-bind:notification="notification">
          </blorm-notification-el-dropdown-menu>
        </el-dropdown-menu>
        <el-dropdown-menu v-else>
            <el-dropdown-item>
              <b>No Updates available</b>
            </el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
</template>

<script>

    import BlormNotificationElDropdownMenu from "./BlormNotificationElDropdownMenu.vue";
    export default {
        components: {
            BlormNotificationElDropdownMenu,
        },
        data() {
          return {
            notifications: [],
          };
        },
        computed: {
          hasNotifications: function() {
            return (this.notifications.length > 0);
          },
          blormNotfificationMenuActive: function() {
            if (this.notifications.length > 0) return "activeRed";
          },
          getNotifications: function () {
            this.notifications.length = 0;
            //this.$root.logMsgToCons("this.store.state.notifications:", this.$store.state.notifications);
            Array.from(this.$store.state.notifications).forEach(function(notification){
              Array.from(notification.activities).forEach(function(activity){
                //this.$root.logMsgToCons("this.store.state.notifications:", activity.object);
                if (activity.object.error !== "ReferenceNotFound") {
                  let tempnot = {};
                  tempnot.activityId = activity.ActivityId;
                  tempnot.notificationGroupId = encodeURIComponent(notification.id);
                  tempnot.verb = notification.verb;
                  tempnot.actor = activity.actor;
                  tempnot.title = activity.object.data.headline;
                  this.notifications.push(tempnot);
                }
              },this);

              if (notification.error === true ) {
                this.$root.logMsgToCons("feed-error: notification is not vaild:", notification);
              }

            },this);
              this.$root.logMsgToCons("this.notifications:", this.notifications);
              return this.notifications;

          },
          numberOfNotifications: function() {

            let length = this.notifications.length;
            if (length < 1) { length = "none"; }
            if (length > 9) { length = "9_plus"; }

            return "filter_"+length;

          },
        },
        methods: {

        }
    };
</script>

<style>
    .BlormNotfificationMenu.activeRed span.material-icons {
        color:red;
    }
    .BlormOptionsMenu {
      left:0;
    }
</style>
