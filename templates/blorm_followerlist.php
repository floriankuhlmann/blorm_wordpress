<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 02.11.18
 * Time: 17:06
 */?>

<script type="application/javascript">
    jQuery(document).ready(function(){

        var ajaxapi = blogdomain+ajaxurl;

        // Define a new component
        Vue.component('blorm-followerlist', {
            data: function() {
                    return {
                        usernameunfollow: '',
                    }
            },
            props: ['follower'],
            methods:  {
                confirmUnfollow: function(id,blormhandle) {
                    if (confirm("Do you really want to UNFOLLOW this account?\nPlease click 'OK' to unfollow.")) {
                        // Save it!
                        this.usernameunfollow = blormhandle;
                        responsePromise = blormapp.core.userUnFollowing(blormhandle);
                        responsePromise.then(this.handleUnfollowSuccess, this.handleUnfollowError);

                    } else {
                        console.log("nothing will change");
                    }
                },
                handleUnfollowSuccess: function (response) {

                        jQuery( "#usernamefollow" ).val("");
                        jQuery(".BlormFeedbackBox").css('display','inline');
                        jQuery( ".BlormFeedbackBoxText" ).html( "You unfollowed <br>'"+this.usernameunfollow+"'." );

                        blormapp.core.getFollowersOfUser();

                },
                handleUnfollowError: function (response) {
                    console.log("error:");
                    console.log(response);
                },

            },
            template:   '<div class="BlormFollowListUser">' +
                '<div class="BlormFollowListUserImg">' +
                '<span v-if="follower.Person.photo_url.length">' +
                '<img v-bind:src="follower.Person.photo_url">'+
                '</span>' +
                '</div>' +
                '<div class="name">{{follower.Person.name}}</div>'+
                '</div>\n',
        });

        blormapp.blormFollowerListing = new Vue({
            el: '#BlormFollowersList',
            mounted() {
                console.log("blormfollowinglist mounted");
                this.getFollowers();
            },
            methods: {
                getFollowers: function() {
                    responsePromise = blormapp.core.getFollowers();
                    console.log("responsePromise get followers");
                    responsePromise.then(this.handleGetFollowersUserSuccess, this.handleGetFollowersUserError);
                },
                handleGetFollowersUserSuccess: function (response) {
                    console.log(response);
                    this.followers = response.data;
                },
                handleGetFollowersUserError: function (response) {
                    console.log("error:");
                    console.log(response);
                },
            },
            data: {
                followers: '',
            },
        });
    });
</script>

<div class="BlormWidgetContainer">
    <div class="BlormFollowerList" id="BlormFollowersList">
        <blorm-followerlist
                v-for="follower in followers"
                v-bind:key="follower.id"
                v-bind:follower="follower"
        ></blorm-followerlist>
        <div style="clear: both"></div>
    </div>
</div>
