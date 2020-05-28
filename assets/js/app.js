jQuery(document).ready(function() {

    blormapp.core = {
        feedTimeline: function() {

            axios.get(restapiVars.root+'blormapi/v1/feed/timeline',
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(response => {
                    blormapp.feedmodule.posts = response.data;

                    console.log('blormfeed:');
                    console.log(JSON.stringify(response.data));

                    blormapp.feedmodule.posts = JSON.parse(JSON.stringify(response.data));
                })
                .catch(error => {
                    console.log(error)
                });
        },
        postShare: function(source, event) {

            var shareJSONObj = {
                "@context": "https://www.w3.org/ns/activitystreams",
                "summary": "userX "+source+"d a post from userY",
                "type": source,
                "origin_post": {
                    "activity_id": $(event.target).parent().data('id')
                }
            };

            // update the feed
            axios.post(
                restapiVars.root+'blormapi/v1/blogpost/share',
                shareJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(function (response) {
                    console.log(response);
                    // update the feed
                    blormapp.core.feedTimeline();

                    // reset interface status
                    jQuery("#selectblogpost").val(0).prop('selected', true);
                    console.log(blormapp.feedmodule.posts);

                }).catch(function (error) {
                    console.log(error);
                });
        },
        postCreate: function(createJSONObj) {

            console.log(JSON.stringify(createJSONObj));


            axios.post(
                restapiVars.root+'blormapi/v1/blogpost/create',
                createJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(function (response) {

                    // update the feed
                    blormapp.core.feedTimeline();
                    console.log(response);
                    return true;

                }).catch(function (error) {
                    console.log(error);
                    return false;
                });
        },
        postFileUpload: function(file) {

            console.log("postFileUpload");
            var promiseObj = new Promise(function(fullfill, reject){
                //Add ajax code here.
                console.log("file");
                console.log(file);
                bodyFormData = new FormData();
                bodyFormData.append('uploadfile', file)
                axios.post(
                    restapiVars.root+'blormapi/v1/file/upload',
                    bodyFormData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-WP-Nonce': restapiVars.nonce,
                        }
                    }).then(function (response) {
                        // on success, call fullfill method, to resolve
                        console.log(response.data);
                        fullfill(response);
                        //return response.data;
                    }).catch(function (response) {
                        console.log(response.data);
                        reject(response);
                        //return response.data;
                });

                // on error, call reject method, to reject
            });
            //Returns Promise object
            return promiseObj;
        },
        userFollowing: function (username) {
            axios.get(restapiVars.root+'blormapi/v1/blog/follow/username/'+username,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(response => {
                    console.log("response:");
                    console.log(response);
                    console.log(JSON.stringify(response.data));

                })
                .catch(error => {
                    console.log("error:");
                    console.log(error)
                });
        }
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
        template: //'<div v-if="latest_reactions.comment">' +
            '<div>' +
            '<template v-for="commentitem in latest_reactions.comment">' +
            '<div :data-id="commentitem.id" class="Blorm_Blormfeed_Postcomment">' +
            '<b>{{commentitem.user_id}} </b> <span v-html="commentitem.data.text">{{commentitem.data.text}}</span> <br>' +
            '</div>' +
            '<small>{{showdate(commentitem.updated_at)}}</small><br>' +
            '</template>' +
            '</div>'
    });


    Vue.component('blorm-feed-post-actions', {
        props: ['post','blormapp.core'],
        methods: {
            postShare: function (source, event) {
                blormapp.core.postShare(source, event);
            }
        },
        template:
            '<div class="Blorm_Blormfeed_Action" :data-id="post.id" :data-headline="post.teaser.headline" :data-teaser="post.teaser.text" :data-url="post.teaser.url" :data-teaserimage="post.image">\n' +
            '<hr>\n' +
            '<button v-on:click="postShare(\'share\', $event)">share in timline</button> | <button v-on:click="postShare(\'reblog\', $event)">reblog</button>\n' +
            '</div>\n'
    });


    // Define a new component called button-counter
    Vue.component('blorm-feed-post', {
        props: ['post','blormusername','newcomment'],
        computed: {
            postHeadline: function() {
                switch (this.post.verb) {
                    case "share":
                        statusline = "<img src='"+templateUrl+"/blorm/assets/icons/circle-sync-backup-1-glyph.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                            "You shared this post on " + this.showdate(this.post.time);
                        break;
                    case "reblog":
                        statusline = "<img src='"+templateUrl+"/blorm/assets/icons/editor-copy-2-duplicate-outline-stroke.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                            "You reblogged this post on " + this.showdate(this.post.time);
                        break;
                    case "create":
                        statusline = "<img src='"+templateUrl+"/blorm/assets/icons/other-arrow-right-other-outline-stroke.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                            "You posted this on " + this.showdate(this.post.time);
                        break;
                    default:
                        statusline = "<span>error sending post</span>";
                        break;
                }
                return statusline;
            },
            postImage: function() {

                if (this.post.teaser.image === "non" || this.post.teaser.image == null ) {
                    return "";
                }

                return '<div class="Blorm_Blormfeed_Image"><img src="'+this.post.teaser.image+'"></div>';

            }
        },
        methods: {
            showdate: function (ISOdate) {
                var date = new Date(ISOdate);
                var timeString = date.toUTCString();
                return timeString;
            },
            getUserFromActor: function() {
                return "username[1]";
            },
            commentchanged: function(event) {
                blormapp.commentdata_text = $(event.target).html().trim();
                blormapp.commentdata_id = $(event.target).parent().data('id');

                console.log($(event.target).parent().data('id'));
                console.log(blormapp.commentdata_text);
            },
            postComment: function (event) {
                // `this` inside methods points to the Vue instance
                console.log('Lets comment now: '+blormapp.commentdata_text);

                var bodyFormData = new FormData();
                bodyFormData.set('text',blormapp.commentdata_text);
                bodyFormData.set('id',blormapp.commentdata_id);

                jQuery( ".Blorm_Blormfeed_Action--comment" ).css("opacity","0.5");
                jQuery( ".Blorm_Blormfeed_Action--comment button" ).prop('disabled', true);

                axios.post(ajaxapi+'?action=blorm&todo=new_post_comment',bodyFormData,{withCredentials: "true"})
                    .then(function (response) {
                        console.log(response);
                        jQuery( ".Blorm_Blormfeed_Action--comment" ).css("opacity","1");
                        jQuery( ".Blorm_Blormfeed_Action--comment button" ).prop('disabled', false);

                        // update the feed
                        blormapp.core.feedTimeline();

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
            '                    <div class="Blorm_Blormfeed_Edit--Date">' +
            '                       <b><span v-html="postHeadline"></span></b>' +
            '                       <template>'  +
            '                           <div class="Blorm_Blormfeed_Edit--Mod">' +
            '                               <button v-on:click="postDelete">delete</button>' +
            '                           </div>\n' +
            '                       </template>\n' +
            '                       <hr class="Blorm_Blormfeed_Border">\n' +
            '                   </div>' +
            '                </div>\n' +
            '                <div class="Blorm_Blormfeed_Title">\n' +
            '                    <h2 class="Blorm_Blormfeed_Title"><a :href="post.teaser.url">{{ post.teaser.headline }}</a></h2>\n' +
            '                </div>\n' +
            '                <span v-html="postImage"></span>' +
            '                <div class="Blorm_Blormfeed_Content">\n' +
            '                    <p>{{ post.teaser.text }}</p>\n' +
            '                </div>\n' +
            '                <div class="Blorm_Blormfeed_URL">\n' +
            '                    <a :href="post.teaser.url"><i>read this</i></a>\n' +
            '                </div>\n' +
            '                <blorm-feed-post-actions' +
            '                   v-bind:post="post">\n'+
            '                </blorm-feed-post-actions>'+
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
            blormapp.core.feedTimeline();
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