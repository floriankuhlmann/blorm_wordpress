<template>
    <div class="BlormFollowersOfUserListUser">
        <div v-on:click="loadUserPage"  class="BlormFollowersOfUserListUserImg">
            <span v-if="follower.Person.photo_url.length">
                <img v-bind:src="follower.Person.photo_url">
            </span>
        </div>
        <div class="name">{{follower.Person.name}}</div>
    </div>
</template>

<script>
    export default {
        props: ['follower'],
        methods:  {
            loadUserPage: function () {
                console.log(this.follower.Person.id);
                this.$root.loadUserPage(this.follower.Person.id);
            },
        },
    }
</script>

<style type="text/css">
    .BlormFollowersOfUserListUser {
        width: 5rem;
        margin: 1px 5px 5px 1px;
        padding: 5px 5px 0px 5px;
        float: left;
        word-wrap: break-word;
        word-break: break-all;
        background-color: #f6f6f6;
        color:#555;
        overflow:hidden;
        border-bottom: #f6f6f6 5px solid;
    }

    .BlormFollowersOfUserListUserImg {
        width: 5rem;
        height: 5rem;
        background-color: #f6f6f6;
        color:#555;
        overflow:hidden;
        border-bottom: #f6f6f6 5px solid;

    }

    .BlormFollowersOfUserListUserImg img {
        width:100%;
        height: auto;
    }

    .BlormFollowersOfUserListUserImg .url {
        font-size: xx-small;
    }

    .BlormFollowersOfUserListUser .BlormFollowersOfUserListUserImg {
        cursor: pointer;
    }
</style>