import {isExternal} from "./validate";
import router from '@/router'
export function findParent(datas:Array<any>, pid:string) {
    let list = [],find
    do{

        find = findTree(datas,pid,'id')
        if(find){
            // @ts-ignore
            list.unshift(find)
            pid = find.pid
        }
    }while (find)
    return list
}
export function findTree(datas:Array<any>, id:any,field:string) {
    for(let key in datas){
        if(datas[key][field] == id){
            return  datas[key]
        }
        if(datas[key].children){
            let item:any =  findTree(datas[key].children,id,field)
            if(item){
                return item
            }
        }
    }
    return null
}
export function refresh() {
    setTimeout(()=>{
        router.push({path:'/refresh',replace:true})
    },10)
}
export function link(url:string){
    if (isExternal(url)) {
        window.open(url)
    }else{
        router.push('/'+url)
    }
}
