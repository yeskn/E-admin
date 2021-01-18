import {isExternal} from "./validate";
import router from '/@/router'
export function findParent(datas, pid) {
    let list = [],find
    do{
        find = findTree(datas,pid,'id')
        console.log(datas)
        if(find){
            list.unshift(find)
            pid = find.pid
        }
    }while (find)
    return list
}
export function findTree(datas, id,field) {
    for(let key in datas){
        if(datas[key][field] == id){
            return  datas[key]
        }
        if(datas[key].children){
            let item =  findTree(datas[key].children,id,field)
            if(item){
                return item
            }
        }
    }
    return null
}
export function link(url){
    if (isExternal(url)) {
        window.open(url)
    }else{
        if(url.indexOf('/') !== 0){
            url='/'+url
        }
        router.push(url)
    }
}