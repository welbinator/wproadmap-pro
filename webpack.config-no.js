const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'roadmap-block': path.resolve(__dirname, 'src/roadmap-block.js'),
        'roadmap-tabs-block': path.resolve(__dirname, 'src/roadmap-tabs-block.js'),
        'new-idea-form-block': path.resolve(__dirname, 'src/new-idea-form-block.js'),
        'display-ideas-block': path.resolve(__dirname, 'src/display-ideas-block.js')
        // 'single-idea-block': path.resolve(__dirname, 'src/single-idea-block.js')
    },
};
