<template>
    <div class="BlormWidgetContainer">
        <div id="BlormUserSearch" class="BlormUserSearch">
            <div class="margin-bottom-10">
                <form @submit.prevent="followBlog">
                    <div id="title-wrap" class="input-text-wrap margin-bottom-10">
                        <label for="usernamefollow">Insert account name to follow</label>
                        <input v-model="usernamefollow" id="usernamefollow" type="text" class="validate">
                        <span data-error="wrong" class="helper-text followuser"></span>
                    </div>
                    <div class="alignright">
                        <input type="submit" value="Submit" text="Follow blog">
                    </div>
                    <br class="clear">
                </form>
            </div>
        </div>
    </div>
    <div class="BlormFeedbackBox" onclick="this.style.display = 'none'">
        <span class="BlormFeedbackBoxText"></span>
    </div>
</template>

<script>
    module.exports = {
        name: '#BlormUserSearch',
        data() {
            return {
                usernamefollow: ''
            };
        },
        methods: {
            followBlog: function() {

                responsePromise = this.$root.userFollowing(this.usernamefollow);
                responsePromise.then(this.handleFollowUserSuccess, this.handleFollowUserError);

            },
            handleFollowUserSuccess: function (response) {
                if (response.data.message == "NoUserData") {
                    jQuery(".BlormFeedbackBox").css('display','inline');
                    jQuery(".BlormFeedbackBoxText" ).html( "Cant find userhandle '"+this.usernamefollow+"'" );
                    jQuery("#usernamefollow" ).val("");
                } else {
                    jQuery("#usernamefollow" ).val("");
                    jQuery(".BlormFeedbackBox").css('display','inline');
                    jQuery(".BlormFeedbackBoxText" ).html( "You are following <br>'"+this.usernamefollow+"' now." );
                    this.$root.getFollowingUsers(this.$store.state.user.id);
                }
            },
            handleFollowUserError: function (response) {
                console.log("error:");
                console.log(response);
            },
        }
    }
</script>

