<?php

?>
<script type="application/javascript">
    jQuery(document).ready(function(){

        blormapp.usermodule = new Vue({
            el: '#BlormUserSearch',
            data: {
                usernamefollow: ''
            },
            methods: {
                followBlog: function() {

                    responsePromise = blormapp.core.userFollowing(this.usernamefollow);
                    responsePromise.then(this.handleFollowUserSuccess, this.handleFollowUserError);

                },
                handleFollowUserSuccess: function (response) {
                    if (response.data.message == "NoUserData") {
                        //jQuery( ".helper-text.followuser" ).html( "Cant find userhandle '"+this.usernamefollow+"'" );
                        jQuery(".BlormFeedbackBox").css('display','inline');
                        jQuery( ".BlormFeedbackBoxText" ).html( "Cant find userhandle '"+this.usernamefollow+"'" );
                        jQuery( "#usernamefollow" ).val("");
                    } else {
                        jQuery( "#usernamefollow" ).val("");
                        jQuery(".BlormFeedbackBox").css('display','inline');
                        jQuery( ".BlormFeedbackBoxText" ).html( "You are following <br>'"+this.usernamefollow+"' now." );
                        //blormapp.core.getFollowersOfUser();
                        blormapp.blormFollowingListing.getFollowingUsers();
                    }
                },
                handleFollowUserError: function (response) {
                    console.log("error:");
                    console.log(response);
                },
            }
        });
    });
</script>

<div class="BlormWidgetContainer">
    <!-- App -->
    <div id="BlormUserSearch" class="BlormUserSearch">
        <div class="margin-bottom-10">
            <form @submit.prevent="followBlog">
                <div id="title-wrap" class="input-text-wrap margin-bottom-10">
                    <label for="usernamefollow">Insert account name to follow</label>
                    <input v-model="usernamefollow" id="usernamefollow" type="text" class="validate">
                    <span data-error="wrong" class="helper-text followuser"></span>
                </div>
                <div class="alignright">
                    <?php submit_button( $text = 'Follow blog', $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null );?>
                </div>
                <br class="clear">
            </form>
        </div>
    </div>
</div>

<div class="BlormFeedbackBox" onclick="this.style.display = 'none'">
    <span class="BlormFeedbackBoxText"></span>
    <!-- <p><i>click to close this window</i></p>-->
</div>
<!-- ende App -->