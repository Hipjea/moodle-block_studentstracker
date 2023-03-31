const path = require("path");
const webpack = require("webpack");

const config = {
    entry: {
        index: './src/index.ts'
    },
    output: {
        library: 'index',
        libraryTarget: 'umd',
        path: path.resolve(__dirname, 'amd/src'),
        filename: '[name].js',
    },
    module: {
        rules: [
            {
                test: /\.ts(x)?$/,
                use: "ts-loader",
                exclude: /node_modules/,
            },
            {
                test: /\.svg$/,
                loader: 'svg-inline-loader',
            }
        ]
    }
};

module.exports = () => {
    config.mode = 'production';
    return config;
};
