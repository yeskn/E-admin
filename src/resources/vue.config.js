// gzip压缩插件
const CompressionWebpackPlugin = require('compression-webpack-plugin')
// 代码打包之后取出console.log压缩代码
const TerserPlugin = require('terser-webpack-plugin')
module.exports = {
	lintOnSave: false,
	publicPath: '/eadmin',
	outputDir: '../assets/public',
	assetsDir: 'static',
	indexPath:'../admin/view/index.vue',
	runtimeCompiler: true,
	productionSourceMap : false,
	filenameHashing: true,
	devServer: {
		disableHostCheck: true,
	},
	chainWebpack: config => {
		// 移除prefetch插件，避免加载多余的资源
		config.plugins.delete('prefetch')
	},
	// webpack的配置
	configureWebpack: config => {

		// 生产环境配置
		if (process.env.NODE_ENV === 'production') {
			// 代码压缩去除console.log
			config.plugins.push(
				new TerserPlugin({
					terserOptions: {
						ecma: undefined,
						warnings: false,
						parse: {},
						compress: {
							drop_console: true,
							drop_debugger: false,
							pure_funcs: ['console.log'] // 移除console
						}
					}
				})
			)
		}

		// 开启gzip压缩
		config.plugins.push(
			new CompressionWebpackPlugin(
				{
					filename: info => {
						return `${info.path}.gz${info.query}`
					},
					algorithm: 'gzip',
					threshold: 10240, // 只有大小大于该值的资源会被处理 10240
					test: new RegExp('\\.(' + ['js'].join('|') + ')$'
					),
					minRatio: 0.8, // 只有压缩率小于这个值的资源才会被处理
					deleteOriginalAssets: false // 删除原文件
				}
			)
		)
	},

};
