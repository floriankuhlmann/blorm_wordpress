<?php

global $blormUserAccountData;
if ($blormUserAccountData->error != null) {
	$blormUserName = $blormUserAccountData->user->name;
}


/**
 * Created by PhpStorm.
 * User: florian
 * Date: 20.09.18
 * Time: 13:15
 */ ?>

<script type="application/javascript">
    jQuery(document).ready(function() {
        blormapp.feedmodule = new Vue({
            el: '#BlormFeed',

            created() {},
            data: {
                posts: blormapp.core.feedTimeline(),
                blormusername: null,
                newcomment: blormapp.core.data.initCommentText,
            },
            methods: {}
        });
    });
</script>

<div class="BlormWidgetContainer">
    <div class="BlormFeedHeader">
        <div class="row">
            <div class="col s12">
                <!-- <form>
                    <label for="BlormWidgetFormSelectBlogPost">What happened on your Page?</label>
                    <select id="BlormWidgetFormSelectBlogPost"><option value="0" disabled="disabled" selected="selected">Available Blogposts</option> <option value="1">Hello world!</option></select>
                </form>-->What happened on your platform? Why don't you share it now?
                <img src="<?php echo plugins_url( 'blorm/assets/icons/circle-arrow_forward-next-glyph.png' );?>" class="blormIcon">
            </div>
        </div>
    </div>
    <!-- App -->
    <div id="BlormFeed" class="Blormfeed">
        <div class="row">
            <div class="col s12">
                <blorm-feed-post
                        v-for="post in posts"
                        v-bind:key="post.id"
                        v-bind:post="post"
                        v-bind:blormusername="blormusername"
                        v-bind:newcomment="newcomment"
                ></blorm-feed-post>
            </div>
        </div>
    </div>
</div>


