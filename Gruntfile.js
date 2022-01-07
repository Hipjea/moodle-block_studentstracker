"use strict";

const webpackConfig = require('./webpack.config.js');
const webpackProductionConfig = require('./webpack.production.js');
const path = require('path');

var babelRename = function(destPath, srcPath) {
    destPath = srcPath.replace('src', 'build');
    destPath = destPath.replace('.js', '.min.js');
    return destPath;
};

module.exports = function (grunt) {

    // We need to include the core Moodle grunt file too, otherwise we can't run tasks like "amd".
    require("grunt-load-gruntfile")(grunt);
    grunt.loadGruntfile("../../Gruntfile.js");

    // Load all grunt tasks.
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.initConfig({
        watch: {
            // If any .less file changes in directory "less" then run the "less" task.
            files: ["src/**/*.ts", "less/*.less"],
            tasks: ["webpack:dev", "less"]
        },
        eslint: {
            options: {
                quiet: true,
                maxWarnings: -1,
                overrideConfig: {
                    rules: {
                        'no-tabs': 0,
                        'curly': 0,
                        'no-undef': 0,
                        'no-unused-vars': 0,
                        'max-len': 0,
                        'babel/no-unused-expressions': 0,
                        'wrap-iife': 0,
                        'babel/semi': 0,
                        'no-console': 0,
                        'no-eq-null': 0,
                        'no-new-wrappers': 0,
                        'no-return-assign': 0,
                        'no-cond-assign': 0,
                        'no-bitwise': 0,
                        'no-labels': 0,
                        'no-func-assign': 0,
                        'no-unmodified-loop-condition': 0,
                        'valid-typeof': 0,
                        'no-self-compare': 0,
                        'no-fallthrough': 0
                    }
                }
            },
            amd: { 
                src: [path.resolve(__dirname, "amd/src/*.js")] 
            }
        },
        babel: {
            options: {
                sourceMaps: false,
                comments: false,
                plugins: [
                    'transform-es2015-modules-amd-lazy',
                    'system-import-transformer'
                ],
                presets: [
                    ['minify', {
                        // This minification plugin needs to be disabled because it breaks the
                        // source map generation and causes invalid source maps to be output.
                        simplify: false,
                        builtIns: false
                    }],
                    ['@babel/preset-env', {
                        targets: {
                            browsers: [
                                ">0.25%",
                                "last 2 versions",
                                "not ie <= 10",
                                "not op_mini all",
                                "not Opera > 0",
                                "not dead"
                            ]
                        },
                        modules: false,
                        useBuiltIns: false
                    }]
                ]
            },
            dist: {
                files: [{
                    expand: true,
                    src: path.resolve(__dirname, "amd/src/index.js"),
                    rename: babelRename
                }]
            }
        },
        less: {
            // Production config is also available.
            development: {
                options: {
                    // Specifies directories to scan for @import directives when parsing.
                    // Default value is the directory of the source, which is probably what you want.
                    paths: ["less/"],
                    compress: true
                },
                files: {
                    "styles.css": "less/styles.less"
                }
            },
        },
        webpack: {
            prod: webpackProductionConfig,
            dev: webpackConfig,
        }
    });
    // The default task (running "grunt" in console).
    grunt.registerTask("default", ["webpack", "less"]);
    grunt.loadNpmTasks('grunt-webpack');
};
