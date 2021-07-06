<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 30.10.18
 * Time: 15:16
 */
?>
<!-- include summernote css/js -->
<script type="application/javascript">

    jQuery(document).ready(function(){

        blormapp.appInput = new Vue({
            el: '#BlormNewPost',
            data: {
                headline: '',
                text: '',
                file: '',
                url: ''
            },
            methods: {
                submit_new_post: function() {

                    console.log("transfer data now");
                    // https://alligator.io/vuejs/vue-form-handling/
                    // https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch

                    if (!this.headline) {
                        console.log("headline: "+this.headline);
                        jQuery( ".helper-text.headline" ).html( "please insert headline" );
                    }
                    if (!this.text) {
                        console.log("text: "+this.headline);
                        jQuery( ".helper-text.comment" ).html( "please insert a short teaser text" );
                    }
                    if (jQuery("#BlormWidgetFormSelectBlogPost").val() == null) {
                        console.log("blogpost: "+ jQuery("#BlormWidgetFormSelectBlogPost").val());
                        jQuery( ".helper-text.posturl" ).html( "please select a blogpost to share" );
                    }
                    if ($('#file').get(0).files.length === 0) {
                        jQuery( ".helper-text.image" ).html( "please select an image for " );
                    }
                    if (!this.text || !this.headline || jQuery("#BlormWidgetFormSelectBlogPost").val() == null) {
                        console.log("data error");
                        return false;
                    }

                    var headline = this.headline;
                    var text = this.text;
                    var posturl = "";
                    var postid = jQuery("#BlormWidgetFormSelectBlogPost").val();

                    jQuery( ".BlormWidgetFormNewPost" ).css("opacity","0.5");
                    jQuery( "#BlormWidgetFormNewPostEnabler" ).prop('disabled', true);

                    responsePromise = blormapp.core.postFileUpload(this.file);
                    responsePromise.then(handleUploadResponse, handleUploadError);

                    // responsePromise success
                    function handleUploadResponse(response) {

                        if (response.data.error) {
                            jQuery(".helper-text.image" ).html(response.data.error);
                            jQuery("#BlormWidgetFormSelectBlogPost").val(0).prop('selected', true);
                            jQuery(".BlormWidgetFormNewPost" ).css("opacity","1");
                            jQuery("#BlormWidgetFormNewPostEnabler" ).prop('disabled', false);

                            return;
                        }

                        var imageUrl = "non";

                        if (response.data != null) {
                            imageUrl = response.data.url;
                        }

                        var createJSONObj = {
                            "@context": "https://www.w3.org/ns/activitystreams",
                            "summary": "userX created a new post on the blog y",
                            "type": "create",
                            "teaser": {
                                "text": text,
                                "headline": headline,
                                "url": posturl,
                                "image": imageUrl,
                                "postid": postid
                            }
                        };

                        blormapp.core.postCreate(createJSONObj);
                        // reset the filed data
                        blormapp.appInput.headline = null;
                        blormapp.appInput.text = null;
                        blormapp.appInput.file = null;

                        jQuery("#BlormWidgetFormSelectBlogPost").val(0).prop('selected', true);
                        jQuery( ".BlormWidgetFormNewPost" ).css("opacity","1");
                        jQuery( "#BlormWidgetFormNewPostEnabler" ).prop('disabled', false);


                    }
                    // responsePromise error
                    function handleUploadError(response) {
                        jQuery( ".helper-text.image" ).html( "file upload is not possible. is file type jpg or png?" );
                    }

                },
                insertPostValues: function() {
                    id = $("#BlormWidgetFormSelectBlogPost option:selected").val();
                    Object.keys(blormapp.recentPosts).forEach(key => {
                        // the name of the current key.
                        if (id == blormapp.recentPosts[key].id ) {
                            this.headline = blormapp.recentPosts[key].headline;
                            this.text = blormapp.recentPosts[key].teasertext;
                        }
                    });

                },
                /*
                    Handles a change on the file upload
                */
                handleFileUpload: function(){
                    this.file = this.$refs.file.files[0];
                    console.log("handleFileUpload:");
                }
            }
        });
    });
</script>


<div class="BlormWidgetContainer">
    <div id="BlormNewPost" class="BlormWidgetFormNewPost">
        <form @submit.prevent="submit_new_post" enctype="multipart/form-data">
            <fieldset id="BlormWidgetFormNewPostEnabler">
                <div class="margin-bottom-10">
                    <?php
                        $recent_posts = wp_get_recent_posts();
                        $recent_posts_with_meta_create = wp_get_recent_posts(array('meta_key' => 'blorm_create'));
                        $recent_posts_with_meta_reblog = wp_get_recent_posts(array('meta_key' => 'blorm_reblog_activity_id'));
                        $recent_posts_with_meta = array_merge($recent_posts_with_meta_create, $recent_posts_with_meta_reblog);
                        wp_reset_query();
                        $i = 0;
                        $AJSONPost = [];
                        foreach ($recent_posts as $recent_post) {

                            //echo  "<code>".$recent_post["post_content"]."</code>";

                            foreach ($recent_posts_with_meta as $rpm) {
                                if ($recent_post["ID"] == $rpm["ID"]) {
                                    unset($recent_posts[$i]);
                                    continue;
                                }
                            }
                            $i++;
                        }


                    ?>
                    <label for="BlormWidgetFormSelectBlogPost">Select one of your posts</label>
                    <select id="BlormWidgetFormSelectBlogPost" @change="insertPostValues()">
                        <option value="0" disabled selected>Available Blogposts</option>
                        <?php

                        foreach( $recent_posts as $recent_post ){
                            echo '<option value="' . $recent_post["ID"] . '">' . $recent_post["post_title"].'</option>';
                        }

                        ?>
                    </select>
                    <span class="helper-text posturl" data-error="wrong"></span>
                </div>
                <div class="input-text-wrap margin-bottom-10" id="title-wrap">
                    <label for="headline">Headline</label>
                    <input v-model="headline" id="headline" type="text" class="validate">
                    <span class="helper-text headline" data-error="wrong"></span>

                </div>
                <div class="textarea-wrap margin-bottom-10" id="description-wrap">
                    <label for="teasertext" >Your teasertext</label>
                    <textarea v-model="text" id="teasertext" class="mceEditor" rows="3" cols="15" autocomplete="off"></textarea>
                    <span class="helper-text comment" data-error="wrong"></span>
                    <?php

                    ?>
                </div>
                <div class="file-field input-field">
                    <div class="btn-small">
                        <span>Preview Image</span>
                        <input type="file" name="file"  id="file" ref="file" @change="handleFileUpload()" accept="image/png, image/jpeg">
                        <div class="helper-text image" data-error="wrong"></div>
                    </div>
                </div>
                <div class="alignright">
                    <button class="button-primary widget-control-save" type="submit" name="action">Submit
                    </button>
                    <br class="clear">
                </div>
            </fieldset>
        </form>
    </div>
    <br class="clear">
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){

        blormapp.recentPosts = <?php
            $z=0;
            foreach ($recent_posts as $recent_post) {
                $SanitizedPost = [];

                $SanitizedPost["id"] = $recent_post["ID"];
                $SanitizedPost["headline"] = $recent_post["post_title"];
                $SanitizedPost["post_date_gmt"] = $recent_post["post_date_gmt"];
                $SanitizedPost["teasertext"] = "";
                $blocks = parse_blocks( $recent_post["post_content"] );
                if (isset($blocks[0])){
                    $SanitizedPost["teasertext"] = str_replace("\n","",filter_var($blocks[0]['innerHTML'], FILTER_SANITIZE_STRING));
                }

                $SanitizedPost["image"] = "nothumb";
                if (get_the_post_thumbnail($recent_post["ID"])) {
                    $SanitizedPost["image"] = get_the_post_thumbnail($recent_post["ID"]);
                };

                $AJSONPost[$z] = $SanitizedPost;
                $z++;
            }

            echo json_encode($AJSONPost, JSON_PRETTY_PRINT);
        ?>;
    });
</script>
