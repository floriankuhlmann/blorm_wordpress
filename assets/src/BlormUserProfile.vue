<template>
    <div class="BlormWidgetContainer">
      <!-- <div id="BlormNotifications" class="BlormNotifications BlormContentBoxWhite">
      <h4><span class="material-icons"></span><span class="BlormContentTextBox">Update</span></h4>
        </div>-->
        <!-- App -->
        <div id="BlormUserProfile" class="BlormUserProfile BlormContentBoxWhite">
            <div>
                <div id="BlormUserProfileNameBox" class="BlormUserProfileNameBox">
                    <b>Name:</b><br> {{userData.name}}<br>
                    <b>Handle:</b><br> {{userData.blormhandle}}
                </div>
                <div id="BlormUserProfileImageBox" class="BlormUserProfileImageBox">
                    <div class="BlormUserProfileUserImage">
                        <img :src="userData.photo_url" :alt="userData.blormhandle">
                    </div>
                </div>
                <div id="BlormUserProfileInfoBox" class="BlormUserProfileInfoBox">
                    <b>Website:</b><br><a :href="userData.website_href">{{userData.website_name}}</a><br>
                    <b>Category:</b><br> {{userData.website_category}}
                </div>
                <div style="clear:both;"></div>
            </div>
            <span v-if="isNotAccountData">
                <div id="BlormUserProfileEdit" class="BlormUserProfileFollow">
                    <ul>
                        <li><a href="#" v-on:click="followUser()">Follow <img :src="getIconUrl" class="blormIcon"></a></li>
                    </ul>
                </div>
            </span>
        </div>
    </div>
</template>

<script>
    import BlormNotificationFeed from "./BlormNotificationFeed.vue";
    export default {
        components: {
            BlormNotificationFeed,
        },
        computed: {
            userData () {
                return this.$store.state.user;
            },
            accountData () {
                return this.$store.state.account;
            },
            getIconUrl () {
                return blormPluginUrl + "/assets/icons/circle-arrow_forward-next-glyph.png";
            },
            isNotAccountData () {
                return (this.$store.state.user.id !== this.$store.state.account.id);
            }
        },
        methods: {
            followUser: function () {
                if (confirm("Do you really want to FOLLOW the account '"+this.$store.state.user.blormhandle+"'?\nPlease click 'OK' to follow.")) {
                    // Save it!
                    let responsePromise = this.$root.userFollowing(this.$store.state.user.blormhandle);
                    responsePromise.then(this.handleUnfollowSuccess, this.handleUnfollowError);
                }
            },
            handleUnfollowSuccess: function (response) {
                this.$root.getFollowingUsers(this.$store.state.user.id);
            },
            handleUnfollowError: function (response) {
                console.log("error:");
                console.log(response);
            },
            unfollowUser: function () {
                alert("unfollow");
            }
        }
    };
</script>

<style lang="css">
.BlormUserProfile .BlormUserProfileFollow {
    padding: 0.5rem 0.5rem 0.5rem 0.5rem;
    border: 1px solid #eee;
    background-color: rgb(252, 252, 252);
    margin-top: 0.5rem;
    text-align: right;
}

.BlormUserProfile .BlormUserProfileFollow ul {
    display: inline;
}

.BlormUserProfile .BlormUserProfileFollow li {
    display: inline;
    padding-left: 0.5rem;
}
</style>
