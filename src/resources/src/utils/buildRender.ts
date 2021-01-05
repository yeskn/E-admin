import {h,resolveComponent} from 'vue'
function renderComponent(data,content,proxyData){
    if(data.attribute && data.attribute.value){
        data.attribute.modelValue = proxyData[data.attribute.value]

        console.log(data.attribute)
    }
    return h(resolveComponent(data.name), data.attribute, content)
}
export function buildRender(data,proxyData){
    if(Array.isArray(data)){
        let renderArr = []
        data.forEach(item=>{
            renderArr.push(buildRender(item,proxyData))
        })
        return renderComponent({name:'div'},renderArr,proxyData)
    }else if(Array.isArray(data.content)){
        let renderArr = []
        data.content.forEach(item=>{
            renderArr.push(buildRender(item,proxyData))
        })
        return renderComponent(data,renderArr,proxyData)
    }else if(typeof(data.content) == 'object'){
        return renderComponent(data,[buildRender(data.content || [],proxyData)],proxyData)
    }else {
        return renderComponent(data,data.content,proxyData)
    }
}
