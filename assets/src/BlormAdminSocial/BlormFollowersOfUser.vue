<template>
    <div class="BlormWidgetContainer">
        <div class="BlormFollowerList" id="BlormFollowersList">
            <blorm-followers-of-user-list
                    v-for="follower in followers"
                    v-bind:key="follower.id"
                    v-bind:follower="follower"
            ></blorm-followers-of-user-list>
            <div style="clear: both"></div>
        </div>
    </div>
</template>

<script>
import BlormFollowersOfUserList from "./BlormFollowersOfUserList.vue";

export default {
    name: 'BlormFollowersOfUser',
    components: {
        BlormFollowersOfUserList
    },
    computed: {
        followers () {
            return this.$store.state.followersOfUser;
        }
    },
};
</script>



