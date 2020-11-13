<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 10.10.18
 * Time: 17:46
 */
?>

<script type="application/javascript">
    jQuery(document).ready(function(){

        var ajaxapi = blogdomain+ajaxurl;


        // Define a new component called button-counter
        Vue.component('BlormBlogList', {
            props: ['user'],
            //template: '<li><b><a href="{{user.id}}" class="followuser" :data-userid="user.id">{{user.name}} | {{user.blogurl}</a></b></li>\n'
            template: '<option value="1">hallo</option>\n'
        });

        var blormuserlist = new Vue({
            el: '#blormbloglist',
            created() {
                axios.get(ajaxapi+'?action=blorm&todo=getbloglist')
                    .then(response => {
                        this.users = response.data;
                        console.log('bloblormlist');
                        console.log(response);
                    })
                    .catch(error => {
                        console.log(error)
                    });

            },
            data: {
                selected: 'firstentry',
                users: []
            },
            methods: {
                submit_follow_blog: function() {

                    if (this.selected == "firstentry") {
                        return;
                    }

                    console.log('submit_follow_blog id: '+this.selected);
                    axios.get(ajaxapi+'?action=blorm&todo=follow_user&blogId='+this.selected)
                        .then(response => {
                            //this.users = response.data;
                            console.log(response);
                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            }
        });
    });
</script>

<!-- App -->
<div class="BlormWidgetContainer">
    <div id="BlormBlogList" class="BlormBlogList margin-bottom-10">
        <form @submit.prevent="submit_follow_blog">


            <select class="blorm-userlist-select" v-model="selected">
                <option value="firstentry" disabled selected>Available Blogs to follow</option>
                <option v-for="user in users" v-bind:value="user.id">
                    {{ user.blogurl }} ({{ user.name }})
                </option>
            </select>
            <div class="alignright">
                <?php submit_button( $text = 'Follow blog', $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null );?>
            </div>
        </form>
    </div>
</div>