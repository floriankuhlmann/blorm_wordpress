<?php

?>
<script type="application/javascript">
    jQuery(document).ready(function(){

        blormapp.usermodule = new Vue({
            el: '#Blorm_usermodule',
            data: {
                usernamefollow: ''
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
                    console.log(this.usernamefollow);
                    //jQuery("#Blorm_usermodule_follow").val(0).prop('selected', true);
                    $( "#Blorm-usermodule-follow" ).css("opacity","1");
                    //jQuery( "#Blorm-usermodule-follow" ).prop('disabled', false);
                    blormapp.core.userFollowing(this.usernamefollow);
                },
            }
        });
    });
</script>


<!-- App -->
<div id="Blorm_usermodule">
    <div id="Blorm-usermodule-follow">
        <div id="appFeed" class="blorm-bloglist margin-bottom-10">
            <form @submit.prevent="followBlog">
                <div id="title-wrap" class="input-text-wrap margin-bottom-10">
                    <label for="usernamefollow">Insert account name to follow</label>
                    <input v-model="usernamefollow" id="usernamefollow" type="text" class="validate">
                    <span data-error="wrong" class="helper-text headline"></span>
                </div>
                <div class="alignright">
                    <?php submit_button( $text = 'Follow blog', $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null );?>
                </div>
                <br class="clear">
            </form>
        </div>
    </div>
</div>
<!-- ende App -->