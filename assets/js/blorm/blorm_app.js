

blormapp.user.timeline = {
        posts: {},
    };
blormapp.user.followers = {};

blormapp.core = {
    data: {
            initCommentText: "Leave a comment. Please remember, be nice!",
    },
    processFeedData: function(posts) {
        postData = posts.map(function (value) {
            var data = {};
            // check for errors in the data
            if (value.object.data.error) {
                data.error = true;
                data.errortype = value.object.data.error;
                return data;
            }
            if (typeof(value.object.data.data) == "undefined") {
                data.error = true;
                data.errortype = "data_undefined";
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
                data.isOwner = (blormapp.user.id === value.actor.id);
                data.ownReactions = value.own_reactions;
                data.reactionCounts = value.reaction_counts;
                data.latestReactions = value.latest_reactions;
console.log(data);
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
        return postData;
    },
    /**
     * get the timeline
     */
    feedTimeline: function() {
        axios.get(
            restapiVars.root+'blormapi/v1/feed/timeline',
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(response => {
                var postData = {};
                console.log(response);
                if (response.data.length > 0) {
                    postData = this.processFeedData(response.data);

                }
                if ( postData.length > 0) {
                    blormapp.feedmodule.posts = postData;
                }
            }).catch(error => {
                console.log(error)
            });
        },

    /**
     * get the timeline
     */
    feedUser: function(userId) {
        axios.get(
            restapiVars.root+'blormapi/v1/feed/user/'+userId,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(response => {
            var postData = {};
            console.log(response);
            if (response.data.length > 0) {
                postData = this.processFeedData(response.data);

            }
            if ( postData.length > 0) {
                blormapp.feedmodule.posts = postData;
            }
        }).catch(error => {
            console.log(error)
        });
    },

    /**
     * postCreate
     * @param createJSONObj
     */
    postCreate: function(createJSONObj) {
        axios.post(
            restapiVars.root+'blormapi/v1/blogpost/create',
            createJSONObj,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
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

    /**
     * postFileUpload
     * @param file
     * @returns {Promise<any>}
     */
    postFileUpload: function(file) {
        var promiseObj = new Promise(
            function(fullfill, reject){
                        bodyFormData = new FormData();
                        bodyFormData.append('uploadfile', file)
                        axios.post(
                            restapiVars.root+'blormapi/v1/file/upload',
                            bodyFormData,
                            {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-WP-Nonce': restapiVars.nonce,}
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

    /**
     * postDelete
     * @param activityId
     */
    postDelete: function (activityId) {
        blormapp.core.feedTimeline();
        axios.get(restapiVars.root+'blormapi/v1/blogpost/delete/'+activityId,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(function (response) {
            console.log(response);
            // update the feed
            blormapp.core.feedTimeline();
            return true;

        }).catch(error => {
            console.log(error)
        });
    },

    /**
     * postShare
     * @param verb
     * @param event
     * @param post
     */
    postShare: function(verb, event, post) {


        promiseObj = new Promise(function(fullfill, reject){
            var shareJSONObj = {
                "@context": "https://www.w3.org/ns/activitystreams",
                "verb": verb,
                "type": $(event.target).parent().data('objecttype'),
                "origin_post": {
                    "object_iri": $(event.target).parent().data('objectiri'),
                    "activity_id": $(event.target).parent().data('activityid')
                }
            };
            console.log(post);
            console.log(shareJSONObj);
            axios.post(
                restapiVars.root+'blormapi/v1/blogpost/share',
                shareJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,}
                }).then(response => {
                fullfill(response);
            }).catch(error => {
                console.log(error);
                reject(error);
            });
        });
        //Returns Promise object
        return promiseObj;
    },

    /**
     * postReblog
     * @param verb
     * @param event
     * @param post
     */
    postReblog: function(verb, event, post) {

        var shareJSONObj = {
            "@context": "https://www.w3.org/ns/activitystreams",
            "verb": verb,
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
        console.log(shareJSONObj);
        axios.post(
            restapiVars.root+'blormapi/v1/blogpost/reblog',
            shareJSONObj,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(function (response) {

                // update the feed
                blormapp.core.feedTimeline();
                // reset interface status
                jQuery("#selectblogpost").val(0).prop('selected', true);
        }).catch(function (error) {
            console.log(error);
        });
    },

    /**
     * reblogUndo
     * @param activityId
     */
    reblogUndo: function (activityId) {
        blormapp.core.feedTimeline();
        axios.get(restapiVars.root+'blormapi/v1/blogpost/undo/reblog/'+activityId,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(function (response) {
                console.log(response);
                // update the feed
                blormapp.core.feedTimeline();
                return true;

            }).catch(error => {
                console.log(error)
            });
    },

    /**
     * shareUndo
     * @param activityId
     */
    shareUndo: function (activityId) {
        blormapp.core.feedTimeline();
        axios.get(
            restapiVars.root+'blormapi/v1/blogpost/undo/share/'+activityId,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(function (response) {
                console.log(response);
                // update the feed
                blormapp.core.feedTimeline();
                return true;

            }).catch(error => {
                console.log(error)
            });
    },

    /**
     * postComment
     * @param commentText
     * @param activityId
     */
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

        jQuery( ".BlormFeed_Action--comment" ).css("opacity","0.5");
        jQuery( ".BlormFeed_Action--comment button" ).prop('disabled', true);

        axios.post(
            restapiVars.root+'blormapi/v1/comment/create',
            shareJSONObj,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(function (response) {
                jQuery( ".BlormFeed_Action--comment" ).css("opacity","1");
                jQuery( ".BlormFeed_Action--comment button" ).prop('disabled', false);

                // update the feed
                blormapp.core.feedTimeline();
            })
            .catch(function (error) {
                console.log(error);y
            });
    },

    /**
     * userFollowing
     * @param blormhandle
     * @returns {Promise<any>}
     */
    userFollowing: function (blormhandle) {
        console.log("userFollowing");
        console.log(blormhandle);
        promiseObj = new Promise(function(fullfill, reject){
            axios.get(
                restapiVars.root+'blormapi/v1/user/follow/blormhandle/'+blormhandle,
                {
                    headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                    },
                }).then(response => {
                    fullfill(response);
                }).catch(error => {
                    reject(error);
                });
            });
        //Returns Promise object
        return promiseObj;
    },

    /**
     * userUnFollowing
     * @param blormhandle
     */
    userUnFollowing: function (blormhandle) {

        promiseObj = new Promise(function(fullfill, reject){
            axios.get(
                restapiVars.root+'blormapi/v1/user/unfollow/blormhandle/'+blormhandle,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    },
                }).then(response => {
                fullfill(response);
            }).catch(error => {
                reject(error);
            });
        });

        //Returns Promise object
        return promiseObj;
    },
    /**
     * getFollowersOfUser
     */
    getFollowingUsers: function() {
        promiseObj = new Promise(function(fullfill, reject){
            axios.get(
                restapiVars.root+'blormapi/v1/feed/following/timeline/'+blormapp.user.id,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    },
                }).then(response => {
                fullfill(response);
            }).catch(error => {
                reject(error);
            });
        });
        //console.log(promiseObj);
        //Returns Promise object
        return promiseObj;
        /*axios.get(
            restapiVars.root+'blormapi/v1/feed/followers/timeline/'+blormapp.user.id,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(response => {
                console.log(response.data);
                blormapp.user.followers = response.data;
                //blormapp.blormFollowerListing.followingblogs = response.data;
                return response.data;
            }).catch(error => {
             console.log("error:");
             console.log(error)
            });*/
        },
    /**
     * getFollowersOfUser
     */
    getFollowers: function() {
        promiseObj = new Promise(function(fullfill, reject){
            uri = restapiVars.root+'blormapi/v1/feed/followers/user/'+blormapp.user.id;
            console.log(uri);
            axios.get(uri
                ,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    },
                }).then(response => {
                fullfill(response);
            }).catch(error => {
                reject(error);
            });
        });
        //console.log(promiseObj);
        //Returns Promise object
        return promiseObj;
        /*axios.get(
            restapiVars.root+'blormapi/v1/feed/followers/timeline/'+blormapp.user.id,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,}
            }).then(response => {
                console.log(response.data);
                blormapp.user.followers = response.data;
                //blormapp.blormFollowerListing.followingblogs = response.data;
                return response.data;
            }).catch(error => {
             console.log("error:");
             console.log(error)
            });*/
    },
    /**
     * getUserData
     */
    getUserData: function () {
        console.log("init userdata");
        axios.get(
            restapiVars.root+'blormapi/v1/user/data',
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restapiVars.nonce,
                }
            }).then(function (response) {
                return response.data;
            }).catch(error => {
                    console.log(error)
            });
        }
};
