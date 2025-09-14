const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
	...defaultConfig,
	entry: {
		sidebar: './src/sidebar.js',
		// admin: './src/admin.js',
		// public: './src/public.js',
	},
	output: {
		filename: '[name].js',
		path: __dirname + '/build',
	},
};
