

import { createApp } from 'vue'
import { createStore } from 'vuex'
import jquery from 'jquery';

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import BlormCore from './BlormCore.vue'
import BlormFeed from './BlormFeed.vue'
import BlormFollowersOfUser from './BlormFollowersOfUser.vue'
import BlormFollowingUsers from './BlormFollowingUsers.vue'
import BlormNewPost from './BlormNewPost.vue'
import BlormUserProfile from './BlormUserProfile.vue'
import BlormUserSearch from './BlormUserSearch.vue'
import BlormUserName from './BlormUserName.vue'

// Create a new store instance.
const store = createStore({
    state: {
        account: blormapp.account,
        user: blormapp.user,
        followingUsers: {},
        followersOfUser: {},
        recentPosts: blormapp.recentPosts,
        feed: {},
        feedLimit: 25,
        feedOffset: 0,
        isAuthenticated: false,
    },
    mutations: {
        setUserData (state, f) {
          state.user= f;
        },
        setFollowersOfUser (state, f) {
            state.followersOfUser = f;
        },
        setFollowingUsers (state, f) {
            state.followingUsers = f;
        },
        setFeed (state, f) {
            state.feed = f;
        },
        setFeedLimit (state, f) {
            state.feedLimit = f;
        },
        setFeedOffset (state, f) {
            state.feedOffset = f;
        },
        isAuthenticated(state, f) {
            state.isAuthenticated = f;
        }
    }
});

const blormWPApp = createApp(BlormCore);

blormWPApp.use(store);
blormWPApp.use(ElementPlus)
blormWPApp.component('blorm-feed', BlormFeed);
blormWPApp.component('blorm-followers-of-user', BlormFollowersOfUser);
blormWPApp.component('blorm-following-users', BlormFollowingUsers);
blormWPApp.component('blorm-newpost', BlormNewPost);
blormWPApp.component('blorm-userprofile', BlormUserProfile);
blormWPApp.component('blorm-usersearch', BlormUserSearch);
blormWPApp.component('blorm-username', BlormUserName);

blormWPApp.mount('#dashboard-widgets-wrap');
