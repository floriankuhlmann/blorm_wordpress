<script>
    import { createStore } from 'vuex'

    // root state object.
    // each Vuex instance is just a single state tree.
    const state = {
        account: blormapp.account,
        user: blormapp.user,
        followers: {},
        followings: {},
    };

    const mutations = {
        setAccount (state, f) {
            state.account = f;
        }
    };

    // A Vuex instance is created by combining the state, mutations, actions,
    // and getters.
    export default createStore({
        state,
        mutations
    })

</script>
