"use strict"

const path = require('path');

module.exports = function(config) {
    const paths = {
        src: {
            nodeModules: path.resolve(__dirname, 'node_modules'),
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

    paths.src.angular = {
        js: path.resolve(paths.src.nodeModules, 'angular/angular.min.js'),
        map: path.resolve(paths.src.nodeModules, 'angular/angular.min.js.map'),
        route: {
            js: path.resolve(paths.src.nodeModules, 'angular-route/angular-route.min.js'),
            map: path.resolve(paths.src.nodeModules, 'angular-route/angular-route.min.js.map')
        },
        sanitize: {
            js: path.resolve(paths.src.nodeModules, 'angular-sanitize/angular-sanitize.min.js'),
            map: path.resolve(paths.src.nodeModules, 'angular-sanitize/angular-sanitize.min.js.map')
        },
        animate: {
            js: path.resolve(paths.src.nodeModules, 'angular-animate/angular-animate.min.js'),
            map: path.resolve(paths.src.nodeModules, 'angular-animate/angular-animate.min.js.map')
        },
        filter: {
            js: path.resolve(paths.src.nodeModules, 'angular-filter/dist/angular-filter.min.js')
        },
        uiBootstrap: {
            js: path.resolve(paths.src.nodeModules, 'angular-ui-bootstrap/dist/ui-bootstrap.js'),
            tpls: path.resolve(paths.src.nodeModules, 'angular-ui-bootstrap/dist/ui-bootstrap-tpls.js')
        }
    }


    paths.src.bootstrap = {
        css: path.resolve(paths.src.nodeModules, 'bootstrap/dist/css/bootstrap.min.css'),
        map: path.resolve(paths.src.nodeModules, 'bootstrap/dist/css/bootstrap.min.css.map')
    }

    return paths
}