module.exports = function(grunt) {
  'use strict';

  // Load all tasks
  require('load-grunt-tasks')(grunt);
  // Show elapsed time
  require('time-grunt')(grunt);

  // Force use of Unix newlines
  grunt.util.linefeed = '\n';

  // Find what the current theme's directory is, relative to the WordPress root
  var path = process.cwd().replace(/^[\s\S]+\/wp-content/, "\/wp-content");

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    sass: {
      dist: {
        files: {
          'css/style.css': 'scss/style.scss',
          'css/admin/editor-style.css': 'scss/admin/editor-style.scss',
          'css/admin/featured-media.css': 'scss/admin/featured-media.scss',
          'css/admin/post-admin.css': 'scss/admin/post-admin.scss',
        }
      }
    },

    uglify: {
      target: {
        options: {
          report: 'gzip'
        },
        files: [{
          expand: true,
          cwd: 'js',
          src: [
            'ad_code.js',
            'color-scheme-control.js',
            'customize-preview.js',
            'mj-disqus.js',
            'functions.js',
            'html5.js',
            'keyboard-image-navigation.js',
            'nav.js',
            'skip-link-focus-fix.js',
            'video-embed.js',
            'featured-media.js',
            '!*.min.js'
          ],
          dest: 'js',
          ext: '.min.js'
        }]
      }
    },

    cssmin: {
      // front-end styles
      target: {
        options: {
          report: 'gzip'
        },
        files: [
          {
            'css/style.min.css': 'css/style.css',
            // combine styles to be loaded on the post edit screen
            'css/admin/post-admin.min.css': ['css/admin/post-admin.css', 'css/admin/featured-media.css'],
            'css/admin/editor-style.min.css': 'css/admin/editor-style.css'
          }
        ]
      }
    },

    watch: {
      sass: {
        files: [
          'scss/*.scss',
          'scss/**/*.scss'
        ],
        tasks: [
          'sass',
          'cssmin'
        ]
      }
    },

    pot: {
      options: {
        text_domain: 'mojo',
        dest: 'lang/',
        keywords: [ //WordPress localization functions
          '__:1',
          '_e:1',
          '_x:1,2c',
          'esc_html__:1',
          'esc_html_e:1',
          'esc_html_x:1,2c',
          'esc_attr__:1',
          'esc_attr_e:1',
          'esc_attr_x:1,2c',
          '_ex:1,2c',
          '_n:1,2',
          '_nx:1,2,4c',
          '_n_noop:1,2',
          '_nx_noop:1,2,3c'
        ]
      },
      files: {
        src: '**/*.php',
        expand: true
      }
    },

    po2mo: {
      files: {
        src: 'lang/*.po',
        expand: true
      }
    },

    version: {
      src: [
        'package.json'
      ],
      docs: {
        src: [
          'docs/conf.py'
        ]
      },
      css: {
        options: {
          prefix: 'Version: '
        },
        src: [
          'style.css',
        ]
      },
      readme: {
        options: {
          prefix: '\\*\\*Current version:\\*\\* v'
        },
        src: [
          'readme.md'
        ]
      }
    },

    gittag: {
      release: {
        options: {
          tag: 'v<%= pkg.version %>',
          message: 'tagging v<%= pkg.version %>'
        }
      }
    },

    gitpush: {
      release: {
        options: {
          tags: true,
          branch: 'master'
        }
      }
    },

    gitmerge: {
      release: {
        options: {
          branch: 'develop',
          message: 'Merge branch develop to master'
        }
      }
    },

    gitcheckout: {
      release: {
        options: {
          branch: 'master'
        }
      }
    },

    confirm: {
      release: {
        options: {
          question: 'Are you sure you want to publish a release?',
          input: 'yes,YES,y,Y'
        }
      }
    }
  });

  // Build assets, docs and language files
  grunt.registerTask('build', 'Build assets, docs and language files', [
    'sass',
    'cssmin',
    'uglify',
    'pot',
    'shell:msmerge'
  ]);

  // Increment version numbers and run a full build
  grunt.registerTask('build-release', 'Increment version numbers (based on package.json) and run a full build', [
    'version', 'build'
  ]);

  // Checkout master, merge develop to master, tag and push to remote
  grunt.registerTask('publish', 'Checkout master, merge develop to master, tag and push to remote', [
    'confirm:release',
    'gitcheckout:release',
    'gitmerge:release',
    'gittag:release',
    'gitpush:release'
  ]);
}
