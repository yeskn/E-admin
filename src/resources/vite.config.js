const path = require('path')
export default function () {
    return {
        runtimeCompiler:true,

        // 项目启动的根路径
        // 入口
        entry: 'index.html',
        // 出口
        outDir: './../public',
        // 打包后的跟路径
        base:'/',
        // 输出的静态资源的文件夹名称
        assetsDir:'assets',
        // 是否开启ssr服务断渲染
        ssr: false,
        // 重命名路径  path.resolve(__dirname, './src')
        alias: {
            'vue': "vue/dist/vue.esm-bundler.js",
            '/@/': path.resolve(__dirname, './src')
        },
        // 是否自动开启浏览器
        open: false,
        // 开启控制台输出日志
        silent: false,
        // 那个包不需要打包
        optimizeDeps:[],

    }
}
