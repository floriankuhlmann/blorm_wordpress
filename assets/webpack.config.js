const path = require("path");
const { VueLoaderPlugin } = require("vue-loader");
const { VueStyleLoader } = require("vue-style-loader");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const webpack = require('webpack');

module.exports = (env = {}) => ({
    mode: 'development',
    entry: path.resolve(__dirname, "./src/BlormMain.js"),
    output: {
        path: path.resolve(__dirname, "./js"),
        publicPath: "/wp-content/plugins/blorm/assets/js/",
        filename: 'blorm_app.js',
    },
    watch: true,
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
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
