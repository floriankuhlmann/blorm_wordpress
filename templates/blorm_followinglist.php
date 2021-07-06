<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 02.11.18
 * Time: 17:06
 */?>

<script type="application/javascript">
        document.addEventListener("DOMContentLoaded", function() {
        var ajaxapi = blogdomain+ajaxurl;

        // Define a new component called button-counter
        Vue.component('blorm-followinglist', {
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

                        blormapp.blormFollowingListing.getFollowingUser();

                },
                handleUnfollowError: function (response) {
                    console.log("error:");
                    console.log(response);
                },

            },
            template:   '<div v-on:click="confirmUnfollow(follower.Person.id,follower.Person.blormhandle)" class="BlormFollowListUser">' +
                '<div class="BlormFollowListUserImg">' +
                '<span v-if="follower.Person.photo_url.length">' +
                '<img v-bind:src="follower.Person.photo_url">'+
                '</span>' +
                '</div>' +
                '<div class="name">{{follower.Person.name}}</div>'+
                '</div>\n',
        });

        blormapp.blormFollowingListing = new Vue({
            el: '#BlormFollowingList',
            mounted() {
                console.log("blormfollowinglist mounted");
                this.getFollowingUsers();
            },
            methods: {
                getFollowingUsers: function() {
                    responsePromise = blormapp.core.getFollowingUsers();
                    console.log("responsePromise");
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
    <div class="BlormFollowingList" id="BlormFollowingList">
        <blorm-followinglist
                v-for="follower in followers"
                v-bind:key="follower.id"
                v-bind:follower="follower"
        ></blorm-followinglist>
        <div style="clear: both"></div>
    </div>
</div>
