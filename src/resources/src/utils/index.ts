import {isExternal} from "./validate";
import router from '@/router'
import request from '@/utils/axios'

export function findParent(datas: Array<any>, pid: string) {
    let list = [], find
    do {

        find = findTree(datas, pid, 'id')
        if (find) {
            // @ts-ignore
            list.unshift(find)
            pid = find.pid
        }
    } while (find)
    return list
}
export function findArrKey(arr, uid, field) {
    var index = null;
    arr.forEach(function (val, i) {
        if (uid == val[field]) {
            index = i;
            return;
        }
    });
    return index;
}
export function findTree(datas: Array<any>, id: any, field: string) {
    for (let key in datas) {
        if (datas[key][field] == id) {
            return datas[key]
        }
        if (datas[key].children) {
            let item: any = findTree(datas[key].children, id, field)
            if (item) {
                return item
            }
        }
    }
    return null
}
export function deleteArr(arr, value) {
    for (var i = arr.length; i > 0; i--) {
        if (arr[i - 1] == value) {
            arr.splice(i - 1, 1)
        }
    }
}

//数字去重
export function unique(arrs) {
    return arrs.filter((value,index,arr)=>arr.indexOf(value)===index)
}
//刷新
export function refresh() {
    setTimeout(()=>{
        router.push({path: '/refresh', replace: true})
    },10)
}
//跳转
export function link(url: string) {
    if (isExternal(url)) {
        window.open(url)
    } else {
        router.push('/' + url)
    }
}

export function lastName(path:string,filename:string='') {
    if (filename) {
        return filename
    } else {
        var index = path.lastIndexOf('\/')
        return path.substring(index + 1, path.length)
    }
}
export function fileIcon(path:string){
    var index = path.lastIndexOf('\.')
    var ext = path.substring(index + 1, path.length)
    try {
        return require('@/assets/file_icon/' + ext + '.png')
    } catch (e) {
        return ''
    }
}

