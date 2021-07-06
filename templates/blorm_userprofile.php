<?php

?>
<script type="application/javascript">
    jQuery(document).ready(function(){

        blormapp.usermodule = new Vue({
            el: '#BlormUserProfile',
            data: {
                userdata: blormapp.user,
            },
            methods: {

            }
        });
    });
</script>

<div class="BlormWidgetContainer">
    <!-- App -->
    <div id="BlormUserProfile" class="BlormUserProfile">
        <div class="BlormUserProfileBox">
            <div id="BlormUserProfileNameBox" class="BlormUserProfileNameBox">
                <b>Name:</b><br> {{userdata.name}}<br>
                <b>Handle:</b><br> {{userdata.blormhandle}}
            </div>
            <div id="BlormUserProfileImageBox" class="BlormUserProfileImageBox">
                <div class="BlormUserProfileUserImage">
                    <img :src="userdata.photo_url" :alt="userdata.blormhandle">
                </div>
            </div>
            <div id="BlormUserProfileInfoBox" class="BlormUserProfileInfoBox">
                <b>Website:</b><br> {{userdata.website_name}}<br>
                <b>Category:</b><br> {{userdata.website_category}}
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div id="BlormUserProfileEdit" class="BlormUserProfileInfoEdit">
        <b><a href="#">Follow</a></b>
        <b><a href="#">Unfollow</a></b>
    </div>
</div>
<!-- ende App -->