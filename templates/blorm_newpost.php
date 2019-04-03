<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 30.10.18
 * Time: 15:16
 */
?>

<script type="application/javascript">

    jQuery(document).ready(function(){

        blormapp.appInput = new Vue({
            el: '#Blorm_appInput',
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
                    console.log("selectbox: " +jQuery("#selectblogpost").val());

                    if (!this.headline) {
                        console.log("headline: "+this.headline);
                        jQuery( ".helper-text.headline" ).html( "please insert headline" );
                    }
                    if (!this.text) {
                        console.log("text: "+this.headline);
                        jQuery( ".helper-text.comment" ).html( "please insert a short teaser text" );
                    }
                    if (!this.text || !this.headline) {
                        console.log("data error");
                        return false;
                    }


                    jQuery( ".blorm-form-newpost" ).css("opacity","0.5");
                    jQuery( "#blorm-form-newpost-enabler" ).prop('disabled', true);

                    /*var data = {
                        headline: this.headline,
                        text: this.text,
                        url: "http://localhost/~florian/blorm/blorm_wordpress"
                    };*/
                    //this.url = jQuery("#selectblogpost").val();

                    console.log("blormapp.feedmodule"+blormapp.feedmodule);

                    var bodyFormData = new FormData();
                    bodyFormData.set('headline',this.headline);
                    bodyFormData.set('text',this.text);
                    bodyFormData.set('url', jQuery("#selectblogpost").val());
                    bodyFormData.append('file', this.file)
                    axios.post(ajaxapi+'?action=blorm&todo=new_post',bodyFormData,{withCredentials: "true"})
                        .then(function (response) {
                            console.log(response);
                            jQuery( ".blorm-form-newpost" ).css("opacity","1");
                            jQuery( "#blorm-form-newpost-enabler" ).prop('disabled', false);

                            /*blormapp.feedmodule.posts.push(
                                {
                                    headline: this.headline,
                                    text: this.text,
                                    url: this.url,
                                    file: this.file
                                });
*/
                            axios.get(ajaxapi+'?action=blorm&todo=getUserFeed')
                                .then(response => {
                                    blormapp.feedmodule.posts = response.data;
                                    console.log('blormfeed:');
                                    console.log(JSON.stringify(response.data));
                                })
                                .catch(error => {
                                    console.log(error)
                                })
                            blormapp.appInput.headline = null;
                            blormapp.appInput.text = null;
                            blormapp.appInput.file = null;
                            jQuery("#selectblogpost").val(0).prop('selected', true);

                            console.log(blormapp.feedmodule.posts);

                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                /*
                    Handles a change on the file upload
                  */
                handleFileUpload(){
                    console.log(JSON.stringify(this.$refs.file.files[0]));
                    this.file = this.$refs.file.files[0];
                }
            }
        });
    });
</script>


<div class="widget-control-actions">
    <div id="Blorm_appInput" class="blorm-form-newpost">
        <form @submit.prevent="submit_new_post" enctype="multipart/form-data">
            <fieldset id="blorm-form-newpost-enabler">
            <div class="margin-bottom-10">
                <label for="selectblogpost">Select a Blogpost to share</label>
                <select id="selectblogpost">
                    <option value="0" disabled selected>Available Blogposts</option>
                <?php
                $recent_posts = wp_get_recent_posts( $args );
                foreach( $recent_posts as $recent ){
                echo '<option value="' . get_permalink($recent["ID"]) . '">' .   $recent["post_title"].'</option>';
                }
                wp_reset_query();
                ?>
                </select>
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
            </div>
            <div class="file-field input-field">
                <div class="btn-small">
                    <span>Preview Image</span>
                    <input type="file" name="file"  id="file" ref="file" v-on:change="handleFileUpload()" accept="image/png, image/jpeg">
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
