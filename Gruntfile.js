module.exports = function( grunt ) {
	// Project configuration.
	grunt.initConfig( {
		// Package
		pkg: grunt.file.readJSON( 'package.json' ),

		// JSHint
		jshint: {
			all: [
				'Gruntfile.js',
				'composer.json',
				'package.json'
			]
		},

		// PHP Code Sniffer
		phpcs: {
			application: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!vendor/**',
					'!wp-content/**'
				]
			},
			options: {
				standard: 'phpcs.ruleset.xml'
			}
		},

		// PHPLint
		phplint: {
			options: {
				phpArgs: {
					'-lf': null
				}
			},
			all: [ 'src/**/*.php' ]
		},

		// PHP Mess Detector
		phpmd: {
			application: {
				dir: 'src'
			},
			options: {
				exclude: 'node_modules',
				reportFormat: 'xml',
				rulesets: 'phpmd.ruleset.xml'
			}
		},
		
		// PHPUnit
		phpunit: {
			application: {},
		},

		// Shell
		shell: {
			options: {
				stdout: true,
				stderr: true
			},
			initWp: {
				command: [
					'mkdir /Users/remco/Websites/wp-e-commerce.dev',
					'cd /Users/remco/Websites/wp-e-commerce.dev',
					'wp core download --path=/Users/remco/Websites/wp-e-commerce.dev --locale=nl_NL',
					'wp core config --dbname=.dev_wp --dbuser=root --dbpass=test1234 --locale=nl_NL',
					'wp db create',
					'wp core install --url=http://wp-e-commerce.dev/ --title=wp-e-commerce.dev --admin_user=remcotolsma --admin_password=remcotolsma --admin_email=info@remcotolsma.nl',
				].join( '&&' )
			}
		},
	} );

	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-phplint' );
	grunt.loadNpmTasks( 'grunt-phpmd' );
	grunt.loadNpmTasks( 'grunt-shell' );

	// Default task(s).
	grunt.registerTask( 'default', [ 'jshint', 'phplint', 'phpmd', 'phpcs' ] );
};
