<?php

?>
<script type="application/javascript">
    jQuery(document).ready(function(){

        /*

        //var myobj = json_decode(this.allBlogs);
                console.log("this.allblogs: "+this.allBlogs);

                this.allBlogs.forEach(function(key, index){
                    console.log(key.id);
                    console.log(key.blogurl);
                    console.log(key.name);
                });
                console.log("allblogs "+this.followingBlogs);

        axios.get(ajaxapi+'?action=blorm&todo=getListOfFollowableBlogs')
                    .then(response => {
                        this.availableBlogs = response.data;
                        console.log('getListOfFollowableBlogs: '+JSON.stringify(this.availableBlogs))
                    })
                    .catch(error => {
                        console.log(error)
                    });

                     var s = JSON.stringify(this.allBlogs);
                        console.log("stringify: "+s);
                        var objects = JSON.parse(s);

                        objects.forEach(function(key, index){
                            console.log(key.id);

                            console.log(key.blogurl);
                            console.log(key.name);
                        });
        */



        function change_bloguser_list_status() {
            console.log(Blorm_usermodule);
        }


        var ajaxapi = blogdomain+ajaxurl;


        /*blormapp.usermodule = new Vue({
            el: '#Blorm_usermodule',
            mounted() {
                axios.get(ajaxapi+'?action=blorm&todo=getListOfFollowingBlogs')
                    .then(response => {
                        //console.log('getListOfFollowingBlogs: '+JSON.stringify(this.ajaxdataFBlogs));
                        this.ajaxData.FollowingBlogs = response.data;
                        // the watch event can only watch state of skalar not of the array
                        this.ajaxStatus = "FollowingBlogs"; // trigger watch event
                        this.ajaxStatusFollowingBlogs = true; // set the state
                        console.log('action=blorm&todo=getListOfFollowingBlogs');
                        console.log(JSON.stringify(response.data));
                        console.log("this.ajaxData.FollowingBlogs");
                        console.log(JSON.stringify(this.ajaxData.FollowingBlogs));

                    })
                    .catch(error => {
                        console.log(error)
                    });

                axios.get(ajaxapi+'?action=blorm&todo=getListOfAllBlogs')
                    .then(response => {
                        //console.log('getListOfAllBlogs: '+JSON.stringify(this.ajaxdataABlogs));
                        this.ajaxData.AllBlogs = response.data;
                        // the watch event can only watch state of skalar not of the array
                        this.ajaxStatus = "AllBlogs"; // trigger watch event
                        this.ajaxStatusAllBlogs = true; // set the state
                        console.log('action=blorm&todo=getListOfAllBlogs');
                        console.log(JSON.stringify(response.data));
                        console.log("this.ajaxData.AllBlogs");
                        console.log(JSON.stringify(this.ajaxData.AllBlogs));
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            watch: {
                ajaxStatus: function() {
                    console.log("ajaxStatus"+this.ajaxStatus)
                    // to prevent confusion and chaos from async data request we check do this
                    //if (this.ajaxData.FollowingBlogs.length != 0) {
                    if (this.ajaxStatusFollowingBlogs) {
                        this.followingBlogs = this.ajaxData.FollowingBlogs;
                        //console.log('created FollowingBlogs: '+JSON.stringify(this.followingBlogs));
                    };
                    //if (this.ajaxData.FollowingBlogs.length != 0 && this.ajaxData.AllBlogs != 0) {
                    if (this.ajaxStatusFollowingBlogs && this.ajaxStatusAllBlogs) {
                        //console.log('created AvailableBlogs: '+JSON.stringify(this.availableBlogs));
                        // loop through all blogs to see if one of them is already on the list of followed blogs
                        this.ajaxData.AllBlogs.forEach(function(akey){
                            // now compare the ids
                            if (!blormapp.usermodule.findObjectByKey(blormapp.usermodule.ajaxData.FollowingBlogs, 'id', akey.id)) {

                                // if a blog ist not already followed push it array
                                blormapp.usermodule.availableBlogs.push(
                                    {
                                        id: akey.id,
                                        blogurl: akey.blogurl,
                                        name: akey.name
                                    });
                            }
                        });
                        //console.log('created AvailableBlogs: '+JSON.stringify(this.availableBlogs));
                    }
                },

            },
            data: {
                selected: 'firstentry',
                ajaxStatus: null,
                ajaxData: {
                    FollowingBlogs: [],
                    AllBlogs: []
                },
                availableBlogs: [],
                followingBlogs: []
            },
            methods: {
                findObjectByKey: function (array, key, value) {
                    for (var i = 0; i < array.length; i++) {
                        if (array[i][key] === value) {
                            return array[i];
                        }
                    }
                    return null;
                },
                findObjectIndexByKey: function (array, key, value) {
                    for (var i = 0; i < array.length; i++) {
                        if (array[i][key] === value) {
                            //return array[i];
                            return i;
                        }
                    }
                    return null;
                },
                followBlog: function() {

                    if (this.selected == "firstentry") {
                        return;
                    }
                    axios.get(ajaxapi+'?action=blorm&todo=follow_user&blogId='+this.selected)
                        .then(response => {
                            var blogtofollow = blormapp.usermodule.findObjectByKey(this.availableBlogs, 'id', this.selected);
                            this.followingBlogs.push(blogtofollow);
                            console.log(blogtofollow);

                            var index = blormapp.usermodule.findObjectIndexByKey(this.availableBlogs, 'id', this.selected);
                            console.log("index"+index);
                            console.log(this.availableBlogs);
                            console.log(this.availableBlogs.splice(index,1));
                            //console.log(this.availableBlogs);
                            this.selected = "firstentry";
                        })
                        .catch(error => {
                            console.log(error)
                        });
                },
                unfollowBlog: function(id,name) {
                    result = confirm('Do you really want to unfollow '+name+' ?');
                    if (result) {
                        axios.get(ajaxapi+'?action=blorm&todo=unfollow_user&blogId='+id)
                            .then(response => {
                                var blogtounfollow = blormapp.usermodule.findObjectByKey(this.followingBlogs, 'id', id);
                                this.availableBlogs.push(blogtounfollow);
                                console.log(blogtounfollow);

                                var index = blormapp.usermodule.findObjectIndexByKey(this.followingBlogs, 'id', id);
                                console.log("index"+index);
                                console.log(this.followingBlogs);
                                console.log(this.followingBlogs.splice(index,1));
                                console.log(this.followingBlogs);
                            })
                            .catch(error => {
                                console.log(error)
                            });
                    }
                }
            }
        });*/
    });
</script>


<!-- App -->
<div id="Blorm_usermodule">
    <div id="Blorm_available_blogs">
        <div id="appFeed" class="blorm-bloglist margin-bottom-10">
            <form @submit.prevent="followBlog">
                <select class="blorm-userlist-select" v-model="selected">
                    <option value="firstentry" disabled selected>Available Blogs to follow</option>
                    <option v-for="availableBlog in availableBlogs" v-bind:value="availableBlog.id">
                        {{ availableBlog.blogurl }} ({{ availableBlog.name }})
                    </option>
                </select>
                <div class="alignright">
                    <?php submit_button( $text = 'Follow blog', $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null );?>
                </div>
            </form>
        </div>
    </div>
<hr>
    <div id="Blorm_following_blogs" class="Blorm_following_blogs">
            <ul>
                <li v-on:click="unfollowBlog(followingBlog.id,followingBlog.name)"
                    class="FollowingBlogList_Blog"
                    v-for="followingBlog in followingBlogs"
                    v-bind:key="followingBlog.id"
                    v-bind:followingBlog="followingBlog">
                    <span class="name">{{followingBlog.name}}</span><br>
                    <span class="url">{{followingBlog.blogurl}}</span>
                </li>
            </ul>
            <div style="clear: both"></div>
    </div>
</div>
<!-- ende App -->