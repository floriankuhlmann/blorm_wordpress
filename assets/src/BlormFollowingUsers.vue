<template>
    <!-- you are following -->
    <div class="BlormWidgetContainer">
        <div class="BlormFollowingList" id="BlormFollowingList">
            <blorm-following-users-list
                    v-for="followingUser in followingUsers"
                    v-bind:key="followingUser.id"
                    v-bind:followingUser="followingUser"
            ></blorm-following-users-list>
            <div style="clear: both"></div>
        </div>
    </div>
</template>

<script>
import BlormFollowingUsersList from "./BlormFollowingUsersList.vue";

export default {
    name: 'BlormFollowingOfUsers',
    components: {
        BlormFollowingUsersList
    },
    computed: {
        followingUsers() {
            return this.$store.state.followingUsers;
        }
    },
}
</script>

<style lang="css"></style>
