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
            el: '#Blorm_feedmodule',
            created() {
                this.posts[0] = {
                    teaser: false,
                    object: {
                        type: "init"
                    },
                };
                blormapp.core.feedTimeline();
            },
            data: {
                postsx: [],
                posts: [],
                blormusername: null,
                newcomment: blormapp.core.data.initCommentText,
            },
            methods: {}
        });
    });

</script>

<div class="widget-control-actions">
    <!-- App -->
    <div id="Blorm_feedmodule" class="Blorm_Blormfeed">
        <div class="row" id="appFeed">
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


