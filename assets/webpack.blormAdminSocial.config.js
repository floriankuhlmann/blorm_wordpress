const path = require("path");
const { VueLoaderPlugin } = require("vue-loader");
const { VueStyleLoader } = require("vue-style-loader");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const Components = require('unplugin-vue-components/webpack')
const { ElementPlusResolver } = require('unplugin-vue-components/resolvers')
const webpack = require('webpack');

module.exports = (env = {}) => ({
    mode: 'development',
    entry: path.resolve(__dirname, "./src/BlormAdminSocial/BlormMain.js"),
    output: {
        path: path.resolve(__dirname, "./js"),
        publicPath: "/wp-content/plugins/blorm/assets/js/",
        filename: 'blormAdminSocialApp.js',
    },
    watch: true,
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.s[c]ss$/i,
                use: [{
                    loader: 'style-loader', // inject CSS to page
                }, {
                    loader: 'css-loader', // translates CSS into CommonJS modules
                }, {
                    loader: 'sass-loader' // compiles Sass to CSS
                }]
            },
            {
                test: /\.m?js/,
                resolve: {
                    fullySpecified: false,
                },
            },
            {
                exclude: /node_modules/,
                test: /\.mjs$/,
                type: 'javascript/auto'
            },
            // this will apply to both plain `.css` files
            // AND `<style>` blocks in `.vue` files
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            }],
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    plugins: [
        Components({
            resolvers: [ElementPlusResolver()],
        }),
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin({
            filename: "[name].css"
        }),
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: true,
        })
    ]
});
