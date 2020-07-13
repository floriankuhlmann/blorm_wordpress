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

        // Define a new component called button-counter
    Vue.component('blorm-followingbloglist', {
        props: ['followingblog'],
        methods:  {
        confirmUnfollow: function(id,blormhandle) {
        if (confirm("Do you really want to UNFOLLOW this account?\nPlease click 'OK' to unfollow.")) {
            // Save it!
            console.log('unfollow:'+id);
            blormapp.core.userUnFollowing(blormhandle);

        } else {
            console.log("nothing will change");
        }
            }
        },
        template:   '<div v-on:click="confirmUnfollow(followingblog.id,followingblog.blormhandle)" class="FollowingBlogList_Blog">' +
                    '<div class="FollowingBlogList_Blog_Img">' +
                    '<img v-bind:src="followingblog.photo_url">'+
                    '</div>' +
                    '<div class="name">{{followingblog.name}}</div>'+
                    '</div>\n',
        });

        blormapp.blormFollowerListing = new Vue({
            el: '#blormfollowerlisting',
            data: function() {
                return {
                    followingblogs: blormapp.core.getFollowersOfUser(),
                }
            },
        });
    });
</script>


<div class="FollowingBlogList" id="blormfollowerlisting">
    <blorm-followingbloglist
            v-for="followingblog in followingblogs"
            v-bind:key="followingblog.id"
            v-bind:followingblog="followingblog"
    ></blorm-followingbloglist>
    <div style="clear: both"></div>
</div>
