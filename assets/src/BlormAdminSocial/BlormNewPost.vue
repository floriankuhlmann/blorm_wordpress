<template>
    <div class="BlormContentBoxWhite">
        <div class="row">
            <div class="col s12">
                What happened on your platform? Why don't you share it now?
            </div>
        </div>
    </div>
    <div class="BlormWidgetContainer">
        <div id="BlormNewPost" class="BlormWidgetFormNewPost">
            <form @submit.prevent="submit_new_post" enctype="multipart/form-data">
                <fieldset id="BlormWidgetFormNewPostEnabler">
                    <div class="margin-bottom-10">
                        <label for="BlormWidgetFormSelectBlogPost">Select one of your posts</label>
                        <select id="BlormWidgetFormSelectBlogPost" @change="insertPostValues($event)">
                            <option v-for="recentPost in recentPosts" :value="recentPost.id">{{recentPost.headline}}</option>
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
</template>

<script>
    //import TodoItem from "./TodoItem.vue"
    export default {
        components: {},
        name: "BlormNewPost",
        data() {
            return {
                headline: '',
                text: '',
                file: '',
                url: '',
                recentPosts: this.$store.state.recentPosts,
            }
        },
        computed: {},
        methods: {
            submit_new_post: function() {

                //define form elements for gui animation
                let postSelect = jQuery("#BlormWidgetFormSelectBlogPost");
                let postWidget =  document.getElementsByClassName("BlormWidgetFormNewPost")[0];
                let postEnabler = jQuery("#BlormWidgetFormNewPostEnabler");
                let helperImage = jQuery(".helper-text.image");
                let helperHeadline = jQuery(".helper-text.headline");
                let helperComment = jQuery(".helper-text.comment");
                let helperUrl = jQuery(".helper-text.posturl");

                if (!this.headline) {
                    helperHeadline.html( "please insert headline" );
                }
                if (!this.text) {
                    helperComment.html( "please insert a short teaser text" );
                }
                if (postSelect.val() == null) {
                    helperUrl.html( "please select a blogpost to share" );
                }
                if (jQuery('#file').get(0).files.length === 0) {
                    helperImage.html( "please select an image for " );
                }
                if (!this.text || !this.headline || postSelect.val() == null) {
                    return false;
                }

                // fade out the widget
                postWidget.style.animation = "newPostOutAnimation 1s ease 0s 1 normal forwards";
                postEnabler.prop('disabled', true);

                let responsePromise = this.$root.postFileUpload(this.file);
                responsePromise.then(handleUploadResponse, handleUploadError);

                let $this = this;

                // responsePromise success
                function handleUploadResponse(response) {

                    if (response.data.error) {
                        helperImage.html(response.data.error);
                        postSelect.val(0).prop('selected', true);
                        postWidget.style.animation = "newPostInAnimation 1s ease 0s 1 normal forwards";
                        postEnabler.prop('disabled', false);
                        return;
                    }

                    let imageUrl = response.data.url;
                    if (imageUrl == null) {
                        imageUrl = "non";
                    }

                    let createJSONObj = {
                        "@context": "https://www.w3.org/ns/activitystreams",
                        "summary": "userX created a new post on the blog y",
                        "type": "create",
                        "teaser": {
                            "text": $this.text,
                            "headline": $this.headline,
                            "url": "will be set by backend API",
                            "image": imageUrl,
                            "postid": postSelect.val()
                        }
                    };

                    let responseCreatePromise = $this.$root.postCreate(createJSONObj);
                    responseCreatePromise.then(handlePostCreateResponse, handlePostCreateError);

                }
                // responsePromise error
                function handleUploadError(response) {
                    helperImage.html( "file upload is not possible. is file type jpg or png?" );
                }

                function handlePostCreateResponse(response) {
                    // reset the filed data
                    $this.headline = null;
                    $this.text = null;
                    $this.file = null;
                    $this.$root.loadAccountPage();
                    postSelect.val(0).prop('selected', true);
                    postWidget.style.animation = "newPostInAnimation 1s ease 0s 1 normal forwards";
                    postEnabler.prop('disabled', false);
                }

                // responsePromise error
                function handlePostCreateError(response) {
                    helperImage.html( "error creating the new post." );
                }
            },
            insertPostValues: function(event) {
                let id = event.target.value;
                Object.keys(this.recentPosts).forEach(key => {
                    // the name of the current key.
                    if (id === this.recentPosts[key].id ) {
                        this.headline = this.recentPosts[key].headline;
                        this.text = this.recentPosts[key].teasertext;
                    }
                });
            },
            /* Handles a change on the file upload */
            handleFileUpload: function(){
                this.file = this.$refs.file.files[0];
            }
        }

    }
</script>


<style lang="css">
    @keyframes newPostOutAnimation {
        0%   { opacity: 1;     }
        100% { opacity:0.25; }
    }

    @keyframes newPostInAnimation {
        0%   { opacity: 0.25;     }
        100% { opacity: 1; }
    }
</style>
