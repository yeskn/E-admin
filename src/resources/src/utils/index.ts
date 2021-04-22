import {isExternal} from "./validate";
import router from '@/router'
import request from '@/utils/axios'
import {useRoute} from "vue-router";
import {action} from "@/store";
// @ts-ignore
import md5 from 'js-md5'
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
    return arrs.filter((value, index, arr) => arr.indexOf(value) === index)
}

//刷新
export function refresh() {
    setTimeout(() => {
        router.push({path: '/refresh', replace: true})
    }, 10)
}

//跳转
export function link(url: string) {
    if (isExternal(url)) {
        window.open(url)
    } else {
        router.push('/' + url)
    }
}

export function lastName(path: string, filename: string = '') {
    if (filename) {
        return filename
    } else {
        var index = path.lastIndexOf('\/')
        return path.substring(index + 1, path.length)
    }
}

export function fileIcon(path: string) {
    var index = path.lastIndexOf('\.')
    var ext = path.substring(index + 1, path.length)
    try {
        return require('@/assets/file_icon/' + ext + '.png')
    } catch (e) {
        return ''
    }
}
export function appendCss(url,css,cache) {
    const style = document.createElement('style')
    style.type = 'text/css'
    style.innerHTML = css
    if(cache){
        action.cacheCss(url,css)
    }
    style.setAttribute('data-key', 'eadmin_style_' + md5(url))
    document.getElementsByTagName('head')[0].appendChild(style)
}
export function setObjectValue(obj, path, value) {
    const arr = path.split('.')
    const len = arr.length - 1
    arr.reduce((cur, key, index) => {
        if (!cur.hasOwnProperty(key))
            throw `${key} 不存在!`
        if (index === len) {
            cur[key] = value
        }
        return cur[key]
    }, obj)
}
export function encode(val) {
    return encodeURIComponent(val).
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}
export function forEach(obj, fn) {
    // Don't bother if no value provided
    if (obj === null || typeof obj === 'undefined') {
        return;
    }

    // Force an array if not already something iterable
    if (typeof obj !== 'object') {
        /*eslint no-param-reassign:0*/
        obj = [obj];
    }

    if (Array.isArray(obj)) {
        // Iterate over array values
        for (var i = 0, l = obj.length; i < l; i++) {
            fn.call(null, obj[i], i, obj);
        }
    } else {
        // Iterate over object keys
        for (var key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                fn.call(null, obj[key], key, obj);
            }
        }
    }
}
export function buildURL(url, params) {
    /*eslint no-param-reassign:0*/
    if (!params) {
        return url;
    }
    var serializedParams;
    var parts = [];
    forEach(params,function serialize(val, key) {
        if (val === null || typeof val === 'undefined') {
            return;
        }
        if (Array.isArray(val)) {
            key = key + '[]';
        } else {
            val = [val];
        }
        forEach(val,function parseValue(v) {
            if (toString.call(v) === '[object Date]') {
                v = v.toISOString();
            } else if (v !== null && typeof v === 'object') {
                v = JSON.stringify(v);
            }
            // @ts-ignore
            parts.push(encode(key) + '=' + encode(v));
        });
    })
    serializedParams = parts.join('&');
    if (serializedParams) {
        var hashmarkIndex = url.indexOf('#');
        if (hashmarkIndex !== -1) {
            url = url.slice(0, hashmarkIndex);
        }
        url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
    }
    return url;
}

