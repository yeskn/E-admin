//连接通讯
import {ElMessage} from "element-plus";
export default {
    uid:0,
    webSocket:null,
    //心跳定时器
    pongHealthTimer: null,
    //监听回调
    listens:[],
    connect: function () {
        this.webSocket = new WebSocket("ws://192.168.199.220:15555/?username=admin&password=b2e472a882c223386ab0fa4c35421467");
        this.webSocket.onmessage = e=>{
            const receive = JSON.parse(e.data)
            const action = receive.action
            const data = receive.data
            if(action === 'login'){
                this.uid = data.info.id
                this.pongHealthTimer = setInterval(()=>{
                    this.send('ping')
                },30000)
            }
            this.listens.forEach(callback=>{
                callback(action,data)
            })
        }
        this.webSocket.onclose = (e) => {
            clearInterval(this.pongHealthTimer)
            ElMessage.error('客服通讯连接失败')
            setTimeout(() => {
                this.connect()
            }, 3000)
        }
    },
    //发送
    send: function (action, data = []) {
        this.webSocket.send(JSON.stringify({
            action: action,
            data: data
        }))
    },
    //监听
    onMessage:function (callback) {
        this.listens.push(callback)
    }
}


