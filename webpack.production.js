const path = require("path");
const webpack = require("webpack");
const CopyPlugin = require("copy-webpack-plugin");

const config = {
    entry: {
        index: './amd/src/index.js'
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                {
                    from: path.resolve(__dirname, 'amd/src/index.js'),
                    to: path.resolve(__dirname, 'amd/build/index.min.js')
                }
            ]
        })
    ]
};

module.exports = () => {
    config.mode = 'production';
    return config;
};
