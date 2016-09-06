"use strict"

const path = require('path');

module.exports = function(config) {
    const paths = {
        src: {
            nodeModules: path.resolve(__dirname, config.src, 'node_modules'),
            app: path.resolve(__dirname, config.src, 'app'),
            scripts: path.resolve(__dirname, config.src, 'app', 'scripts'),
            styles: path.resolve(__dirname, config.src, 'app', 'styles'),
            partials: path.resolve(__dirname, config.src, 'app', 'partials'),
            assets: path.resolve(__dirname, config.src, 'app', 'assets'),
            backend: path.resolve(__dirname, config.src, 'backend'),
            root: path.resolve(__dirname, config.src)
        },
        build: {
            dist: path.resolve(__dirname, config.dist),
            backend: path.resolve(__dirname, config.dist, 'backend'),
            app: {
                root: path.resolve(__dirname, config.dist, 'app'),
                vendor: path.resolve(__dirname, config.dist, 'app', 'vendor'),
                assets: path.resolve(__dirname, config.dist, 'app', 'assets'),
                scripts: path.resolve(__dirname, config.dist, 'app', 'scripts'),
                fonts: path.resolve(__dirname, config.dist, 'app', 'fonts'),
                styles: path.resolve(__dirname, config.dist, 'app', 'styles'),
                partials: path.resolve(__dirname, config.dist, 'app', 'partials')
            }
        }
    }

    paths.src.jsurl = path.resolve(paths.src.nodeModules, 'jsurl/lib/jsurl.js')

    paths.src.bootstrap = {
        js: path.resolve(paths.src.nodeModules, 'bootstrap/dist/js/prism.js'),
        css: path.resolve(paths.src.nodeModules, 'bootstrap/dist/css/bootstrap.min.css'),
        map: path.resolve(paths.src.nodeModules, 'bootstrap/dist/css/bootstrap.min.css.map')
    }

    return paths
}