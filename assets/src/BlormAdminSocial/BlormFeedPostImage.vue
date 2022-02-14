<template>
    <div class="BlormFeedImage">
    <img :src="getPostImage">
    </div>
</template>

<script>
    export default {
        name: 'BlormFeedPostImage',
        props: ['postImage'],
        computed: {
            getPostImage: function() {
                if (this.postImage === "non" || this.postImage == null ) {
                    return "";
                }
                return this.postImage;
            }
        }
    };
</script>