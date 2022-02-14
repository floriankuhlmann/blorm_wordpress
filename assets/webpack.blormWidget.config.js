const path = require("path");
const webpack = require('webpack');
// working with .css and .scss files: For webpack to correctly process .css and .scss files,
// the order in which you arrange loaders is important.
// first we need to import MiniCssExtractPlugin and autoprefixer in our webpack.config.js file like this
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    //mode: 'production',
    mode: 'development',
    entry: {
        blormwidget: "./src/BlormWidget/main.js",
    },
    output: {
        path: path.resolve(__dirname, "./js"),
        filename: 'blormWidgetBuilder.js',
        library: {
            name: 'blormWidgetBuilder',
            type: 'umd',
        },
    },
    watch: true,
    module: {
        rules: [
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
        ],
    },
    plugins: [
        new MiniCssExtractPlugin(),
    ],
};
