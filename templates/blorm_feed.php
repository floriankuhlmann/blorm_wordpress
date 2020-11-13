<?php


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


