<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 20.09.18
 * Time: 13:15
 */ ?>

<script type="application/javascript">

jQuery(document).ready(function() {

    var getUserFeed = function() {
        axios.get(ajaxapi+'?action=blorm&todo=getUserFeed')
        .then(response => {
            blormapp.feedmodule.posts = response.data;
            console.log('blormfeed:');
            console.log(JSON.stringify(response.data));
        })
        .catch(error => {
            console.log(error)
        });
    };

    Vue.component('blorm-feed-comment', {
        props: ['latest_reactions'],
        methods: {
            showdate: function (ISOdate) {
                var date = new Date(ISOdate);
                var timeString = date.toUTCString();
                return timeString;
            }
        },
        template: '<div v-if="latest_reactions.comment">' +
        '<template v-for="commentitem in latest_reactions.comment">' +
        //'<div>comment data: {{commentitem}}' +
        '<div :data-id="commentitem.id" class="Blorm_Blormfeed_Postcomment">' +
        '<b>{{commentitem.user_id}} </b> <span v-html="commentitem.data.text">{{commentitem.data.text}}</span> <br>' +
        '</div>' +
        '<small>{{showdate(commentitem.updated_at)}}</small><br>' +
        //'</div>' +
        '</template>' +
        '</div>'
    });


    // Define a new component called button-counter
    Vue.component('blorm-feed-post', {
        props: ['post','blormusername','newcomment'],
        methods: {
            showdate: function (ISOdate) {
                var date = new Date(ISOdate);
                var timeString = date.toUTCString();
                return timeString;
            },
            getUserFromActor: function(actor) {
                //console.log(actor);
                username = actor.split(":");
                //console.log(username[1]);
                return username[1];
            },
            commentchanged: function(event) {
                blormapp.commentdata_text = $(event.target).html().trim();
                blormapp.commentdata_id = $(event.target).parent().data('id');

                //blormapp.commentdata.text = $(event.target).html().trim();
                //console.log($(event.target).parent().data('id'));
                console.log($(event.target).parent().data('id'));

                console.log(blormapp.commentdata_text);
            },
            postShare: function(event) {
                console.log($(event.target).parent().data('id'));
                //alert('postshare');

                var id = $(event.target).parent().data('id');
                var headline = $(event.target).parent().data('headline');
                var teaser = $(event.target).parent().data('teaser');
                var teaserimage = $(event.target).parent().data('teaserimage');
                var url = $(event.target).parent().data('url');

                if (id) {
                    alert('post share:' + id + headline + teaser + teaserimage + url);
                }

                var bodyFormData = new FormData();
                bodyFormData.set('id',id);
                bodyFormData.set('headline',headline);
                bodyFormData.set('text',teaser);
                bodyFormData.set('url', url);
                bodyFormData.append('filepath', teaserimage)

                // update the feed
                axios.post(ajaxapi+'?action=blorm&todo=post_share',bodyFormData,{withCredentials: "true"})
                    .then(function (response) {
                        console.log(response);

                        // update the feed
                        getUserFeed();

                        jQuery("#selectblogpost").val(0).prop('selected', true);

                        console.log(blormapp.feedmodule.posts);

                    })
                    .catch(function (error) {
                        console.log(error);
                    });



                //alert($(event.target).parent().data('id'));*/
            },
            postReblog: function(event) {
                console.log($(event.target).parent().data('id'));
                //alert('postshare');

                var id = $(event.target).parent().data('id');
                var headline = $(event.target).parent().data('headline');
                var teaser = $(event.target).parent().data('teaser');
                var teaserimage = $(event.target).parent().data('teaserimage');
                var url = $(event.target).parent().data('url');

                if (id) {
                    alert('post reblog:' + id + headline + teaser + teaserimage + url);
                }

                var bodyFormData = new FormData();
                bodyFormData.set('id',id);
                bodyFormData.set('headline',headline);
                bodyFormData.set('text',teaser);
                bodyFormData.set('url', url);
                bodyFormData.append('filepath', teaserimage)
                axios.post(ajaxapi+'?action=blorm&todo=post_reblog',bodyFormData,{withCredentials: "true"})
                    .then(function (response) {
                        console.log(response);

                        // update the feed
                        getUserFeed();

                        jQuery("#selectblogpost").val(0).prop('selected', true);

                        console.log(blormapp.feedmodule.posts);

                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            postComment: function (event) {
                // `this` inside methods points to the Vue instance

                console.log('Lets comment now: '+blormapp.commentdata_text);

                var bodyFormData = new FormData();
                bodyFormData.set('text',blormapp.commentdata_text);
                bodyFormData.set('id',blormapp.commentdata_id);

                console.log("text set to:"+bodyFormData.get('text'));
                console.log("id set to:"+bodyFormData.get('id'));

                jQuery( ".Blorm_Blormfeed_Action--comment" ).css("opacity","0.5");
                jQuery( ".Blorm_Blormfeed_Action--comment button" ).prop('disabled', true);

                axios.post(ajaxapi+'?action=blorm&todo=new_post_comment',bodyFormData,{withCredentials: "true"})
                    .then(function (response) {
                        console.log(response);
                        jQuery( ".Blorm_Blormfeed_Action--comment" ).css("opacity","1");
                        jQuery( ".Blorm_Blormfeed_Action--comment button" ).prop('disabled', false);

                        // update the feed
                        getUserFeed();

                    })
                    .catch(function (error) {
                        console.log(error);y
                    });
            },
            postDelete: function (event) {
                // `this` inside methods points to the Vue instance
                alert('Lets delete !')
                // `event` is the native DOM event
                if (event) {
                    alert(event.target.tagName)
                }
            }
        },
        template: '<div class="Blorm_Blormfeed_Post" :class="post.verb" :data-id="post.id">\n' +
        '                <div class="Blorm_Blormfeed_Edit">' +
        '                    <div class="Blorm_Blormfeed_Edit--Date"><b>{{getUserFromActor(post.actor)}}</b> posted this on {{showdate(post.time)}}</div>' +
        '                       <template v-if="blormusername === getUserFromActor(post.actor)">'  +
        '                           <div class="Blorm_Blormfeed_Edit--Mod">' +
        '                               <!-- <button v-on:click="$emit(\'post_edit\')">edit</button> | -->' +
        '                               <button v-on:click="postDelete">delete</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                <hr class="Blorm_Blormfeed_Border">\n' +
        '                </div>\n' +
        '                <div class="Blorm_Blormfeed_Image">\n' +
        '                    <img :src="post.Teaserimage">\n' +
        '                    <b class="Blorm_Blormfeed_Title">{{ post.Headline }}</b>\n' +
        '                </div>\n' +
        '                <div class="Blorm_Blormfeed_Content">\n' +
        '                    <p>{{ post.Teaser }}</p>\n' +
        '                </div>\n' +
        '                <div class="Blorm_Blormfeed_URL">\n' +
        '                    <a :href="post.URL"><i>read this</i></a>\n' +
        '                </div>\n' +
        '                <div class="Blorm_Blormfeed_Action" :data-id="post.id" :data-headline="post.Headline" :data-teaser="post.Teaser" :data-url="post.URL" :data-teaserimage="post.Teaserimage">\n' +
        '                    <hr>\n' +
        '                    <button v-on:click="postShare">share in timline</button> | <button v-on:click="postReblog">reblog</button>\n' +
        '                </div>\n' +
        '               <blorm-feed-comment' +
        '               v-bind:latest_reactions="post.latest_reactions">\n' +
        '               </blorm-feed-comment>\n' +
        '               <div class="Blorm_Blormfeed_Action--comment" :data-id="post.id">            ' +
        '               <div contenteditable class="Blorm_Blormfeed_Action--comment-textbox" v-html="newcomment" v-on:keyup="commentchanged" v-on:blur="commentchanged" v-on:paste="commentchanged" v-on:delete="commentchanged" v-on:focus="commentchanged">' +
        '               </div>\n' +
        '                   <div class="Blorm_Blormfeed_Action">\n' +
        '                       <button v-on:click="postComment">comment</button>' +
        '                   </div>\n' +
        '               </div>\n' +
        '            </div>\n'
    });

    blormapp.feedmodule = new Vue({
        el: '#Blorm_feedmodule',
        created() {
            getUserFeed();

            axios.get(ajaxapi+'?action=blorm&todo=getUserData')
                    .then(response => {
                       blormapp.user = response.data;
                       this.blormusername = blormapp.user.name;
                    })
                    .catch(error => {
                        console.log(error)
                    })
        },
        data: {
            posts: [],
            blormusername: null,
            newcomment: "Leave a comment. Please remember, be nice!"
        },
        methods: {

        }
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


