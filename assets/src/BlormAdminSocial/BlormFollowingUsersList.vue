<template>
    <div class="BlormFollowListUser">
        <div v-on:click="loadUserPage" class="BlormFollowListUserImg">
            <span v-if="followingUser.photoUrl.length">
                <img v-bind:src="followingUser.photoUrl">
            </span>
        </div>
        <div class="name">{{followingUser.blormHandle}}</div>
        <span v-if="isAccountDataOnDisplay">
            <div v-on:click="confirmUnfollow"><img :src="getIconUrl" class="blormIcon"></div>
        </span>
    </div>
</template>

<script>
    export default {
        props: ['followingUser'],
        computed: {
            getIconUrl () {
                return blormPluginUrl + "/assets/icons/circle-close-delete-remove-glyph.png";
            },
            isAccountDataOnDisplay: function () {
                return (this.$store.state.user.blormHandle === this.$store.state.account.blormHandle)
            },
        },
        methods:  {
            loadUserPage: function () {
                this.$root.loadUserPage(this.followingUser);
            },
            confirmUnfollow: function() {
                if (confirm("Do you really want to UNFOLLOW the account '"+this.followingUser.blormHandle+"'?\nPlease click 'OK' to unfollow.")) {
                    // Save it!
                    let responsePromise = this.$root.userUnFollowing(this.followingUser.blormHandle);
                    responsePromise.then(this.handleUnfollowSuccess, this.handleUnfollowError);
                }
            },
            handleUnfollowSuccess: function (response) {
                jQuery("#usernamefollow").val("");
                jQuery(".BlormFeedbackBox").css('display','inline');
                jQuery(".BlormFeedbackBoxText").html( "You unfollowed <br>'"+this.followingUser.blormHandle+"'." );
                this.$root.getFollowingUsers(this.$store.state.user.id);
            },
            handleUnfollowError: function (response) {
                console.log("error:");
                console.log(response);
            },
        },
    }
</script>

<style type="text/css">

    .BlormFollowListUser {
        width: 5rem;
        margin: 1px 5px 5px 1px;
        padding: 5px 5px 0px 5px;
        float: left;
        word-wrap: break-word;
        word-break: break-all;
        background-color: #f6f6f6;
        color:#555;
        overflow:hidden;
        border-bottom: #f6f6f6 5px solid;
    }

    .BlormFollowListUserImg {
        width: 5rem;
        height: 5rem;
        background-color: #f6f6f6;
        color:#555;
        overflow:hidden;
        border-bottom: #f6f6f6 5px solid;

    }

    .BlormFollowListUserImg img {
        width:100%;
        height: auto;
    }

    .BlormFollowListUserImg .url {
        font-size: xx-small;
    }

    .BlormFollowListUser .BlormFollowListUserImg {
        cursor: pointer;
    }

    .BlormFollowListUser .blormIcon {
        cursor: pointer;
    }
</style>