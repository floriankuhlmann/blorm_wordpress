<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 02.11.18
 * Time: 17:06
 */?>

<script type="application/javascript">
    jQuery(document).ready(function(){

        function findObjectByKey(array, key, value) {
            for (var i = 0; i < array.length; i++) {
                if (array[i][key] === value) {
                    array.splice(i, 1);
                    return array;
                }
            }
            return null;
        }

        var ajaxapi = blogdomain+ajaxurl;

        // Define a new component called button-counter
        Vue.component('blorm-followingbloglist', {
            props: ['followingBlog'],
            methods:  {
                unfollowblog: function(id,name) {
                    result = confirm('Do you really want to unfollow '+name+' ?');
                    if (result) {
                        //alert("okay unfollow");
                        axios.get(ajaxapi+'?action=blorm&todo=unfollow_user&blogId='+id)
                            .then(response => {
                                //blormfollowingbloglist.followingblogs = response.data;

                                found = findObjectByKey(blormfollowingBloglist.followingBlogs,'Userid',id);

                                console.log('unfollow_user');
                                console.log(Object.values(blormfollowingbloglist.followingblogs[0]));
                                //console.dir(found);

                                //console.log(response);
                            })
                            .catch(error => {
                                console.log(error)
                            });
                    } else {
                        alert("not unfollow");
                    }

                }
            },
            template: '' +
            '<div v-on:click="unfollowblog(followingBlog.Userid,followingBlog.Username)" class="FollowingBlogList_Blog">' +
            '<span class="name">{{followingBlog.Username}}</span><br>' +
            '<span class="url">{{followingBlog.Blogurl}}</span>' +
            '</div>\n'
        });

        var blormfollowingBloglist = new Vue({
            el: '#blormfollowingBloglist',
            created() {
                //alert(this.followingBlogs);
                axios.get(ajaxapi+'?action=blorm&todo=getfollowingbloglist')
                    .then(response => {
                        this.followingBlogs = response.data;
                        console.log('blormfollowingBloglist');
                        console.log(response);
                        console.log(this.followingBlogs);
                    })
                    .catch(error => {
                        console.log(error)
                    });

            },
            data: {
                followingBlogs: []
            }
            /*methods: {
                unfollowblog: function() {
                    alert('un_follow_blog id: '+this.selected);
                }
            }*/
        });
    });
</script>


<div class="FollowingBlogList" id="blormfollowingBloglist">
    <blorm-followingbloglist
            v-for="followingBlog in followingBlogs"
            v-bind:key="followingBlog.id"
            v-bind:followingBlog="followingBlog"
    ></blorm-followingbloglist>
    <div style="clear: both"></div>
</div>
