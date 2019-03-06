const path = require('path');
const webpack = require('webpack');
const Uglifyjs = require('uglifyjs-webpack-plugin');
const ExtractText = require('extract-text-webpack-plugin');
const extractSass = new ExtractText({
    filename: '[name].bundle.css',
    disable: false
});

const config = {
    entry: {
        style: './app/styles/style.scss',
        login: './app/styles/login.scss',
        bpmnstyle: './app/styles/bpmnstyle.scss',
        bpmn: './app/bpmn/bpmn.js',
        index: './app/index.ts'
    },
    output: {
        path: path.join(__dirname, 'dist'),
        filename: '[name].bundle.js',
        publicPath: '/dist/'
    },
    resolve: {
        extensions: ['.ts', '.js']
    },
    plugins: [
        extractSass,
        new webpack.ProvidePlugin({
            '$': 'jquery',
            'jQuery': 'jquery',
            'jquery': 'jquery',
            'window.jQuery': 'jquery',
            'window.$': 'jquery'})

    ],
    devServer: {
        host: '0.0.0.0'
    },
    optimization: {
        minimize: false,
        minimizer: [
            new Uglifyjs({
                sourceMap: true
            })
        ]
    },
    module: {
        rules: [{
            loader: 'html-es6-template-loader',
            test: /\.html$/,
            exclude(filePath) {
                return filePath === path.join(__dirname, 'app', 'index.html')
            },
            query: {
                transpile: true
            }
        },{
            test: /\.js$/,
            loader: 'babel-loader',
            options: {
                presets: [
                    'es2015'
                ]
            },
            exclude: /node_modules/
        },{
            test: /\.ts/,
            loader: 'awesome-typescript-loader'
        },{
            test: /\.s[ac]ss$/,
            loader: extractSass.extract({
                use: [{
                    loader: 'css-loader',
                    options: {
                        minimize: false
                    }
                },{
                    loader: 'sass-loader'
                }],
                fallback: 'style-loader'
            })
        },{
            test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+)?$/,
            use:[{
                loader: 'url-loader',
                options: {
                    name: '[name].[ext]'
                }
            }]
        }]
    }
};

if (process.env.NODE_ENV === 'development') {
    config.watch = true;
    config.devtool = 'source-map';
} else if (process.env.NODE_ENV === 'hot') {
    config.devtool = 'source-map';
    config.devServer = {
        hot: true
    };
    config.plugins.push(new webpack.HotModuleReplacementPlugin());
}

module.exports = config;