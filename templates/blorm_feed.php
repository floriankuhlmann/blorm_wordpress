<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 20.09.18
 * Time: 13:15
 */ ?>

<script type="application/javascript">


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


