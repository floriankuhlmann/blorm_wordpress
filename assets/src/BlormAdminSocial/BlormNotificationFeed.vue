<template>
    <el-dropdown>
      <span class="el-dropdown-link BlormNotfificationMenu" v-bind:class="blormNotfificationMenuActive">
            <span class="material-icons">{{numberOfNotifications}}</span>&nbsp;<span class="material-icons">list</span>
      </span>
      <template #dropdown>
        <el-dropdown-menu v-if="hasNotifications">
          <blorm-notification-el-dropdown-menu
              v-for="notification in getNotifications"
              v-bind:notification="notification"
          ></blorm-notification-el-dropdown-menu>
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
      data: function () {
        return {
          notifications: [],
        };
      },
      computed: {
          hasNotifications: function() {

            return (this.$store.state.notifications.length > 0);

          },
          getNotifications: function () {

            return this.$store.state.notifications;

          },
          blormNotfificationMenuActive: function() {

            let cssClass = "inactiveGrey";

            Array.from(this.$store.state.notifications).forEach(function(notification){
              if (notification.is_seen === false) {
                console.log("activeRed");
                  cssClass = "activeRed";
              }
            },this); // activities loop end

            return cssClass;

          },
          numberOfNotifications: function() {

            let length = this.$store.state.notifications.length;
            if (length < 1) { length = "none"; }
            if (length > 9) { length = "9_plus"; }
            if (length === "undefined") { length = "none"}

            return "filter_"+length;

          },
        },
        methods: {}
    };
</script>

<style>
    .BlormNotfificationMenu.activeRed span.material-icons {
        color:red;
    }

    .BlormNotfificationMenu.inactiveGrey span.material-icons {
      color:#aaa;
    }

    .BlormOptionsMenu {
      left:0;
    }
</style>
