"use strict";

const path = require("path");

const babelRename = function (destPath, srcPath) {
  destPath = srcPath.replace("src", "build");
  destPath = destPath.replace(".js", ".min.js");
  return destPath;
};

module.exports = function (grunt) {
  // We need to include the core Moodle grunt file too, otherwise we can't run tasks like "amd".
  require("grunt-load-gruntfile")(grunt);
  grunt.loadGruntfile("../../Gruntfile.js");

  // Load all grunt tasks.
  grunt.loadNpmTasks("grunt-contrib-less");
  grunt.loadNpmTasks("grunt-contrib-watch");
  grunt.initConfig({
    babel: {
      options: {
        sourceMaps: false,
        comments: false,
        plugins: [
          "transform-es2015-modules-amd-lazy",
          "system-import-transformer"
        ],
        presets: [
          [
            "minify",
            {
              // This minification plugin needs to be disabled because it breaks the
              // source map generation and causes invalid source maps to be output.
              simplify: false,
              builtIns: false
            }
          ],
          [
            "@babel/preset-env",
            {
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
            }
          ]
        ]
      },
      dist: {
        files: [
          {
            expand: true,
            src: path.resolve(__dirname, "amd/src/*.js"),
            rename: babelRename
          }
        ]
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
      }
    },
    watch: {
      // If any .less file changes in directory "less" then run the "less" task.
      files: ["amd/src/*.js", "less/*.less"],
      tasks: ["amd", "less"]
    }
  });
  // The default task (running "grunt" in console).
  grunt.registerTask("default", ["amd", "less"]);
};
