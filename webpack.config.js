var path = require('path');
const webpack = require('webpack');

module.exports = {
    entry: {
        app: './assets/js/app.js',
        // login: './web/assets/js/login.js',
        // layout: './web/assets/js/layout.js',
    },
    output: {
        path: path.resolve(__dirname, 'public', 'build'),
        filename: '[name].js',
        // "publicPath" is what is used as a path in the generated files. It is a path the Webserver can resolve
        publicPath: '/clanx_dev/build/'
        // We must change this for publishing
        // Make it like this for test the deployment locally
        //publicPath: '/clanx_deploy/build/'
        // Make it like this for the server
        //publicPath: '/public/build/'
    },

    module: {
        rules: [
            // As soon as we use ES6 features, we need babel.
            // {
            //     test: /\.js$/,
            //     exclude: /node_modules/,
            //     use: {
            //         loader: 'babel-loader',
            //         options: {
            //             cacheDirectory: true,
            //         },
            //     }
            // },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
            {
                test: /\.(png|jpg|jpeg|gif|ico|svg)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name]-[hash:6].[ext]'
                    },
                }]
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name]-[hash:6].[ext]'
                    },
                }]
            }
        ]
    },

    plugins: [
        new webpack.ProvidePlugin({
            jQuery: 'jquery', //bummer. bootstrap relies on global jquery variable.
            $: 'jquery',
            "window.jQuery": 'jquery',
            tether: 'tether',
            Tether: 'tether',
            'window.Tether': 'tether',
        })
    ],

};


// var Encore = require('@symfony/webpack-encore');
//
// Encore
//     // the project directory where all compiled assets will be stored
//     .setOutputPath('public/build/')
//
//     // the public path used by the web server to access the previous directory
//     .setPublicPath('/build')
//
//     // will create public/build/app.js and public/build/app.css
//     .addEntry('app', './assets/js/app.js')
//     .addStyleEntry('app_css', './assets/css/app.css')
//
//     // allow sass/scss files to be processed
//     .enableSassLoader()
//
//     // allow legacy applications to use $/jQuery as a global variable
//     .autoProvidejQuery()
//
//     .enableSourceMaps(!Encore.isProduction())
//
//     // empty the outputPath dir before each build
//     .cleanupOutputBeforeBuild()
//
//     // show OS notifications when builds finish/fail
//     .enableBuildNotifications()
//
//
//     // create hashed filenames (e.g. app.abc123.css)
//     // .enableVersioning()
// ;
//
// module.exports = Encore.getWebpackConfig();
