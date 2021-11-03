
<script>
import axios from 'axios';
import { useStore } from 'vuex'

export default {

    setup () {
        const store = useStore()
    },

    mounted() {
        console.log("blorm | feedTimeline user id:");
        console.log(this.$store.state.user.id);
        jQuery("#wpcontent").css("background-color","#000000");
        jQuery("#wpcontent .wrap h1").css("color","#fff");
        this.feedTimeline();
        this.getFollowersOfUser(this.$store.state.user.id);
        this.getFollowingUsers(this.$store.state.user.id);
    },
    data: function() {
        return {
            initCommentText: "Leave a comment. Please remember, be nice!",
            commentdata_text: "",
            commentdata_id: "",
        }
    },
    methods: {
        loadUserPage: function(userId) {
            this.feedUser(userId);
            this.getFollowersOfUser(userId);
            this.getFollowingUsers(userId);
            this.getUserData(userId);
            //this.$store.commit('setUserData', x.data);
            jQuery( "#BlormDashboardWidgetFeed" ).css("background-color","#ffffff");
            jQuery( "#BlormDashboardWidgetUserProfile" ).css("background-color","#ffffff");
            jQuery( "#BlormDashboardWidgetFollowers" ).css("background-color","#ffffff");
            jQuery( "#BlormDashboardWidgetFollowing").css("background-color","#ffffff");
            jQuery( "#BlormDashboardWidgetNewPost" ).css("display","none");
            jQuery( "#BlormDashboardWidgetSearchUser" ).css("display","none");
            jQuery( "#BlormDashboardWidgetFeed .blormImage").attr('src', blormPluginUrl+"/assets/images/blorm_logo_world_FFFF94.png");
        },
        isAccountDataOnDisplay: function() {
            return (this.$store.state.user.blormhandle === this.$store.state.account.blormhandle);
        },
        isResponseStatusOk: function (response) {
                if (response.status === 200 ) {
                    this.$store.commit('isAuthenticated', true);
                    return true;
                }
                this.$store.commit('isAuthenticated', false);
                return false;
        },
        logError: function (logmessage, e) {
            console.log("error: "+logmessage);
            console.log(e)
        },
        processFeedData: function(posts) {
            let postData = posts.map(function (value) {
                var data = {};
                // check for errors in the data
                if (value.object.data.error) {
                    data.error = true;
                    data.activityId = value.id;
                    data.errortype = value.object.data.error;
                    return data;
                }
                if (typeof(value.object.data.data) == "undefined") {
                    data.error = true;
                    data.activityId = value.id;
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
                    return data;
                }
            });
            return postData;
        },
        /**
         * get the timeline
         */
        feedTimeline: function() {

            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";
            let limit = this.$store.state.feedLimit;
            console.log(limit);
            axios.get(
                restapiVars.root+'blormapi/v1/feed/timeline?limit='+limit+'&offset='+this.$store.state.feedOffset,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,
                    }
                }
            ).then(response => {
                    var postData = {};

                    if(!this.isResponseStatusOk(response)) {
                        return
                    }
                console.log("feeddata");
                console.log(response);
                    if (response.data.length > 0) {
                        postData = this.processFeedData(response.data);
                    }

                    if ( postData.length > 0) {
                        this.$store.commit('setFeed', postData);
                        this.$store.commit('setFeedOffset', postData.length + this.$store.state.feedOffset);
                        console.log("feeedOffset");
                        console.log(this.$store.state.feedOffset);
                        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";

                    }
                }
            ).catch(error => {
                console.log(error);
                document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";

            });
        },

        /**
         * get the timeline
         */
        feedUser: function(userId) {

            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";
            axios.get(
                restapiVars.root+'blormapi/v1/feed/user/'+userId,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,}
                }
            ).then(response => {
                    var postData = {};

                    if(!this.isResponseStatusOk(response)) {
                        return
                    }

                    if (response.data.length > 0) {
                        postData = this.processFeedData(response.data);
                    }

                    //if ( postData.length > 0) {
                        this.$store.commit('setFeed', postData);
                        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
                    //}

                }
            ).catch(error => {
                console.log(error)
                document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
            });
        },

        /**
         * postCreate
         * @param createJSONObj
         */
        postCreate: function(createJSONObj) {
            let $this = this;
            return new Promise (
                function(success, error) {
                    let bodyFormData = new FormData();
                    bodyFormData.append('uploadfile', file);
                    axios.post(
                        restapiVars.root+'blormapi/v1/blogpost/create',
                        createJSONObj,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-WP-Nonce': restapiVars.nonce,
                            }
                        }
                    ).then(function (response) {
                        if(!$this.isResponseStatusOk(response)) {
                            return
                        }
                        // on success, call fullfill method, to resolve
                        success(response);
                    }).catch(function (response) {
                        error(response);
                        return false;
                    });
                }
            );
        },

        /**
         * postFileUpload
         * @param file
         * @returns {Promise<any>}
         */
        postFileUpload: function(file) {
           return new Promise (
                function(success, error) {
                    let bodyFormData = new FormData();
                    bodyFormData.append('uploadfile', file);
                    axios.post(
                        restapiVars.root+'blormapi/v1/file/upload',
                        bodyFormData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-WP-Nonce': restapiVars.nonce,
                            }
                        }
                    ).then(function (response) {
                        // on success, call fullfill method, to resolve
                        success(response);
                    }).catch(function (response) {
                        error(response);
                    });
                }
           );
        },

        /**
         * postDelete
         * @param activityId
         */
        postDelete: function (activityId) {
            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/delete/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
                    console.log(response);
                    fullfill(response);
                }).catch(error => {
                    console.log(error)
                    reject(error);
                });
            });
            return promiseObj;
        },

        /**
         * postShare
         * @param verb
         * @param event
         * @param post
         */
        postShare: function(verb, event, post) {
            let promiseObj = new Promise (function(fullfill, reject) {
                var shareJSONObj = {
                    "@context": "https://www.w3.org/ns/activitystreams",
                    "verb": verb,
                    "type": jQuery(event.target).parent().data('objecttype'),
                    "origin_post": {
                        "object_iri": jQuery(event.target).parent().data('objectiri'),
                        "activity_id": jQuery(event.target).parent().data('activityid')
                    }
                };
                axios.post(
                    restapiVars.root+'blormapi/v1/blogpost/share',
                    shareJSONObj,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                        }
                    }
                ).then(response => {
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
            let promiseObj = new Promise (function(fullfill, reject) {
                var shareJSONObj = {
                    "@context": "https://www.w3.org/ns/activitystreams",
                    "verb": verb,
                    "type": jQuery(event.target).parent().data('objecttype'),
                    "origin_post": {
                        "object_iri": jQuery(event.target).parent().data('objectiri'),
                        "activity_id": jQuery(event.target).parent().data('activityid')
                    },
                    "origin_post_data": {
                        "headline": post.object.headline,
                        "text": post.object.text,
                        "image": post.object.image,
                        "url": post.object.url,
                    }
                };
                axios.post(
                    restapiVars.root+'blormapi/v1/blogpost/reblog',
                    shareJSONObj,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                        }
                    }
                ).then(response => {
                    fullfill(response);
                }).catch(error => {
                    console.log(error);
                    reject(error);
                });
            });
            jQuery("#selectblogpost").val(0).prop('selected', true);
            //Returns Promise object
            return promiseObj;
        },

        /**
         * reblogUndo
         * @param activityId
         */
        reblogUndo: function (activityId) {
            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/undo/reblog/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
                    console.log(response);
                    fullfill(response);
                }).catch(error => {
                    console.log(error)
                    reject(error);
                });
            });
            return promiseObj;
        },

        /**
         * shareUndo
         * @param activityId
         */
        shareUndo: function (activityId) {
            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/undo/share/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
                    console.log(response);
                    fullfill(response);
                }).catch(error => {
                    console.log(error)
                    reject(error);
                });
            });
            return promiseObj;
        },

        /**
         * postComment
         * @param commentText
         * @param activityId
         */
        postComment: function (commentText, activityId) {
            // `this` inside methods points to the Vue instance

            if (commentText === "" ||activityId === "") {
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
            let $this = this;
            axios.post(
                restapiVars.root+'blormapi/v1/comment/create',
                shareJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,}
                }
            ).then(function (response) {
                    jQuery( ".BlormFeed_Action--comment" ).css("opacity","1");
                    jQuery( ".BlormFeed_Action--comment button" ).prop('disabled', false);

                    // update the feed
                    $this.feedTimeline();
                }).catch(function (error) {
                    console.log(error);
            });
        },
        getBlormApiData: function (url) {
            let promiseObj = new Promise( function( fullfill, reject) {
                axios.get( url,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                        },
                    }
                ).then(response => {
                    fullfill(response);
                }).catch(error => {
                    reject(error);
                });
            });
            return promiseObj;
        },
        /**
         * getUserData
         * @param userId
         * @returns {Promise<any>}
         */
        getUserData: function (userId) {
            let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/user/data/userid/'+userId);
            let $store = this.$store;
            responsePromise.then(
                function (response) {
                    $store.commit('setUserData', response.data);
                },
                this.handleResponseError
            );
        },
        handleGetUserDataSuccess: function (response) {
            this.$store.commit('setUserData', response.data);
        },
        /**
         * userFollowing
         * @param blormhandle
         * @returns {Promise<any>}
         */
        userFollowing: function (blormhandle) {
            return this.getBlormApiData(restapiVars.root+'blormapi/v1/user/follow/blormhandle/'+blormhandle);
        },
        /**
         * userUnFollowing
         * @param blormhandle
         */
        userUnFollowing: function (blormhandle) {
            return this.getBlormApiData(restapiVars.root+'blormapi/v1/user/unfollow/blormhandle/'+blormhandle);
        },
        /**
         * getFollowingUsers
         */
        getFollowingUsers: function(userId) {
            let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/following/timeline/'+userId);
            responsePromise.then(
                this.handleGetFollowingUsersSuccess,
                this.handleResponseError
            );
        },
        handleGetFollowingUsersSuccess: function (response) {
            this.$store.commit('setFollowingUsers', response.data);
        },
        /**
         * getFollowersOfUser
         */
        getFollowersOfUser: function(userId) {
            let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/followers/user/'+userId);
            responsePromise.then(
                this.handleGetFollowersOfUserSuccess,
                this.handleResponseError
            );
        },
        handleGetFollowersOfUserSuccess: function (response) {
            this.$store.commit('setFollowersOfUser', response.data);
        },
        handleResponseError: function (response) {
            console.log("error:");
            console.log(response);
        },
    },
}
</script>

<style lang="css">
@keyframes feedOutAnimation {
    0%   { opacity: 1;     }
    100% { opacity:0.25; }
}

@keyframes feedInAnimation {
    0%   { opacity: 0.25;     }
    100% { opacity: 1; }
}

</style>