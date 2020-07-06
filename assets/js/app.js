jQuery(document).ready(function() {

    blormapp.core = {
        data: {
            initCommentText: "Leave a comment. Please remember, be nice!"
        },
        feedTimeline: function() {

            axios.get(
                restapiVars.root+'blormapi/v1/feed/timeline',
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(response => {
                    var postData = {};
                    console.log(JSON.stringify(response));

                    /*if (response.data.length == 0) {
                        console.log("response.data 0");
                        var data = {};
                        data.teaser = false;
                        data.error = false;
                        data.object = {
                            type: "init",
                        };

                        blormapp.feedmodule.postsx[0] = data;
                        //return;
                    //};*/
                    console.log("response.data");
                    console.log(response.data);
                    postData = response.data.map(function (value) {
                        var data = {};
                        // check for errors in the data
                        if (value.object.data.error) {
                            data.error = true;
                            return data;
                        }
                        // if we have an referenced object
                        if (value.object) {
                            data.error = false;
                            data.teaser = true;
                            data.activityId = value.id;
                            data.object = {
                                iri: value.object.id,
                                type: "teaser",
                                verb: value.verb,
                                time: value.time,
                                headline: value.object.data.data.headline,
                                text: value.object.data.data.text,
                                image: value.object.data.data.image,
                                url: value.object.data.data.url,
                            };
                            data.actor = {
                                id: value.actor.id,
                                name: value.actor.data.data.name,
                                userName: value.actor.data.data.username,
                                photoUrl: value.actor.data.data.photoUrl,
                                website: value.actor.data.data.website,
                            };
                            data.ownReactions = value.own_reactions;
                            data.reactionCounts = value.reaction_counts;
                            data.latestReactions = value.latest_reactions;

                            return data;
                        }
                        if (value.teaser) {
                            data.error = false;
                            data.teaser = true;
                            data.activityId = value.id;
                            data.object = {
                                iri: value.object.id,
                                type: "teaser",
                                verb: value.verb,
                                time: value.time,
                                headline: value.teaser.headline,
                                text: value.teaser.text,
                                image: value.teaser.image,
                                url: value.teaser.url,
                            };
                            data.actor = {
                                id: value.actor.id,
                                userName: ":-)",
                                photoUrl: "",
                                website: "",
                            };
                            data.ownReactions = value.own_reactions;
                            data.reactionCounts = value.reaction_counts;
                            data.latestReactions = value.latest_reactions;

                            return data;
                        }

                    });
                    if ( postData.length > 0) {
                        blormapp.feedmodule.posts = postData;
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        },
        postShare: function(verb, event, post) {

            var shareJSONObj = {
                "@context": "https://www.w3.org/ns/activitystreams",
                "verb": post.object.verb,
                "type": $(event.target).parent().data('objecttype'),
                "origin_post": {
                    "object_iri": $(event.target).parent().data('objectiri'),
                    "activity_id": $(event.target).parent().data('activityid')
                },
                "origin_post_data": {
                    "headline": post.object.headline,
                    "text": post.object.text,
                    "image": post.object.image,
                    "url": post.object.url,
                }
            };
            console.log(post);
            axios.post(
                restapiVars.root+'blormapi/v1/blogpost/share',
                shareJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(function (response) {

                    // update the feed
                    blormapp.core.feedTimeline();

                    // reset interface status
                    jQuery("#selectblogpost").val(0).prop('selected', true);
                }).catch(function (error) {
                    console.log(error);
                });
        },
        postCreate: function(createJSONObj) {
            axios.post(
                restapiVars.root+'blormapi/v1/blogpost/create',
                createJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(function (response) {

                    if (response.data.message == "api.error: teaser with given ID already exists") {
                        alert("Sorry, not possible. You already shared this post.");
                        return;
                    }

                    // update the feed
                    blormapp.core.feedTimeline();

                    return true;

                }).catch(function (error) {
                    console.log(error);

                    return false;
                });
        },
        postFileUpload: function(file) {
            var promiseObj = new Promise(function(fullfill, reject){
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
                        fullfill(response);
                    }).catch(function (response) {
                        reject(response);
                        //return response.data;
                });
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
        },
        postDelete: function (activityId) {
            blormapp.core.feedTimeline();
            axios.get(restapiVars.root+'blormapi/v1/blogpost/delete/'+activityId,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(function (response) {
                    console.log(response);
                    // update the feed
                    blormapp.core.feedTimeline();
                    return true;

                })
                .catch(error => {
                    console.log(error)
                });

        },
        postComment: function (commentText, activityId) {
            // `this` inside methods points to the Vue instance

            if (commentText == "" ||activityId == "") {
                return;
            }

            var shareJSONObj = {
                "@context": "https://www.w3.org/ns/activitystreams",
                "parent_post": {
                    "activity_id": activityId,
                },
                "comment": {
                    "text": commentText,
                }
            };

            jQuery( ".Blorm_Blormfeed_Action--comment" ).css("opacity","0.5");
            jQuery( ".Blorm_Blormfeed_Action--comment button" ).prop('disabled', true);

            axios.post(
                restapiVars.root+'blormapi/v1/comment/create',
                shareJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }).then(function (response) {
                    jQuery( ".Blorm_Blormfeed_Action--comment" ).css("opacity","1");
                    jQuery( ".Blorm_Blormfeed_Action--comment button" ).prop('disabled', false);

                    // update the feed
                    blormapp.core.feedTimeline();
                })
                .catch(function (error) {
                    console.log(error);y
                });
        },
    };

    Vue.component('blorm-feed-comment', {
        props: ['latest_reactions'],
        methods: {
            showdate: function (ISOdate) {
                var date = new Date(ISOdate   );
                //var timeString = date.toUTCString();

                return moment(date.getTime() + 60*60*1000*2).fromNow();
            },
            getUrl: function (url) {
                return "http://"+url;
            }
        },
        template: //'<div v-if="latest_reactions.comment">' +
            '<div class="Blorm_Blormfeed_Postcomments>">' +
            '<template v-for="commentitem in latest_reactions.comment">' +
            '<div :data-id="commentitem.id" class="Blorm_Blormfeed_Postcomment">' +
            '<div class="Blorm_Blormfeed_Postcomment_User"><img :src="commentitem.user.data.data.photo_url" style="width:100%; height: auto;"></div>' +
            '<div class="Blorm_Blormfeed_Postcomment_Content">' +
            '<div class="Blorm_Blormfeed_Postcomment_Content_Text">' +
            '<b><a :href="getUrl(commentitem.user.data.data.website)">{{commentitem.user.data.data.name}}</a></b> ' +
            '<span v-html="commentitem.data.text"> {{commentitem.data.text}}</span></div>' +
            '<div class="Blorm_Blormfeed_Postcomment_Content_Date"><small>{{showdate(commentitem.updated_at)}}</small><br></div>' +
            '</div>' +
            '</div>' +
            '</template>' +
            '</div>'
    });

    Vue.component('blorm-feed-post-actions', {
        props: ['post','blormapp.core'],
        methods: {
            postShare: function (verb, event) {
                blormapp.core.postShare(verb, event, this.post);
            }
        },
        template:
            '<div class="Blorm_Blormfeed_Action" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">\n' +
            '<hr>\n' +
            '<button v-on:click="postShare(\'share\', $event)">share in timline</button> | <button v-on:click="postShare(\'reblog\', $event)">reblog</button>\n' +
            '</div>\n'
    });

    Vue.component('blorm-feed-post', {
        props: ['post','blormusername','newcomment'],
        computed: {
            postHeadline: function() {
                switch (this.post.object.verb) {
                    case "share":
                        statusline = "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                            "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+"/blorm/assets/icons/circle-sync-backup-1-glyph.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                            "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> shared this</b></div>";
                        break;
                    case "reblog":
                        statusline = "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                            "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+"/blorm/assets/icons/editor-copy-2-duplicate-outline-stroke.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                            "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> reblogged this</b></div>";
                        break;
                    case "create":
                        statusline = "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                            "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+"/blorm/assets/icons/other-arrow-right-other-outline-stroke.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                            "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> posted this</b></div>";
                        break;
                    default:
                        statusline = "<span>Welcome to BLORM</span>";
                        break;
                }

                return statusline;
            },
            postImage: function() {
                if (this.post.object.image === "non" || this.post.object.image == null ) {
                    return "";
                }

                return '<div class="Blorm_Blormfeed_Image"><img src="'+this.post.object.image+'"></div>';
            }
        },
        methods: {
            showdate: function (ISOdate) {
                var date = new Date(ISOdate   );
                //var timeString = date.toUTCString();

                return moment(date.getTime() + 60*60*1000*2).fromNow();
            },
            getUserFromActor: function() {
                return "username[1]";
            },
            commentchanged: function(event) {
                if ($(event.target).html() == blormapp.core.data.initCommentText ) {
                    console.log($(event.target).html());
                    $(event.target).html("");
                }
                blormapp.commentdata_text = $(event.target).html().trim();
                blormapp.commentdata_id = $(event.target).parent().data('id');
            },
            postComment: function (event) {
                blormapp.core.postComment(
                    blormapp.commentdata_text,
                    blormapp.commentdata_id
                );
            },
            postDelete: function (event) {
                blormapp.core.postDelete(
                    $(event.target).data('activityid')
                );
                blormapp.core.feedTimeline();
            }
        },
        template: '<span v-if="post.error === false">' +
            '           <span v-if="post.teaser === true">' +
            '           <div class="Blorm_Blormfeed_Post" :class="post.object.verb" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">\n' +
            '                <div class="Blorm_Blormfeed_Edit">' +
            '                    <div class="Blorm_Blormfeed_Edit--Date">' +
            '                       <span v-html="postHeadline"></span>' +
            '                       <template>'  +
            '                           <div class="Blorm_Blormfeed_Edit--Mod">' +
            '                               <button v-on:click="postDelete" :data-activityid="post.activityId">delete</button>' +
            '                           </div>\n' +
            '                       </template>\n' +
            '                       <hr class="Blorm_Blormfeed_Border">\n' +
            '                   </div>' +
            '                </div>\n' +
            '                <div class="Blorm_Blormfeed_Title">\n' +
            '                    <h2 class="Blorm_Blormfeed_Title"><a :href="post.object.url">{{ post.object.headline }}</a></h2>\n' +
            '                </div>\n' +
            '                <span v-html="postImage"></span>' +
            '                <div class="Blorm_Blormfeed_Content">\n' +
            '                    <p>{{ post.object.text }}</p>\n' +
            '                </div>\n' +
            '                <div class="Blorm_Blormfeed_URL">\n' +
            '                    <a :href="post.object.url"><i>read this</i></a>\n' +
            '                </div>\n' +
            '                <blorm-feed-post-actions' +
            '                   v-bind:post="post">\n'+
            '                </blorm-feed-post-actions>'+
            '               <blorm-feed-comment' +
            '               v-bind:latest_reactions="post.latestReactions">\n' +
            '               </blorm-feed-comment>\n' +
            '               <div class="Blorm_Blormfeed_Action--comment" :data-id="post.activityId">            ' +
            '               <div contenteditable class="Blorm_Blormfeed_Action--comment-textbox" v-html="newcomment" v-on:keyup="commentchanged" v-on:blur="commentchanged" v-on:paste="commentchanged" v-on:delete="commentchanged" v-on:focus="commentchanged">' +
            '               </div>\n' +
            '                   <div class="Blorm_Blormfeed_Action">\n' +
            '                       <button v-on:click="postComment">comment</button>' +
            '                   </div>\n' +
            '               </div>\n' +
            '            </div>' +
            '           </span>' +
            '           <span v-else>' +
            '           <div class="Blorm_Blormfeed_Post">' +
            '               There is no post in your timeline.<br>Why dont you share something or follow someone?' +
            '           </div>' +
            '           </span>' +
            '</span>'

    });
});