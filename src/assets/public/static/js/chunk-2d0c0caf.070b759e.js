(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0c0caf"],{"42fa":function(e,a,t){"use strict";t.r(a);var n=t("f2bf"),c=Object(n["withScopeId"])("data-v-5fd49606"),u=c((function(e,a,t,c,u,l){var o=Object(n["resolveComponent"])("a-switch");return Object(n["openBlock"])(),Object(n["createBlock"])(o,{onChange:e.changeHandel,checked:e.value,"onUpdate:checked":a[1]||(a[1]=function(a){return e.value=a})},null,8,["onChange","checked"])})),l=(t("a9e3"),t("78b1")),o=Object(n["defineComponent"])({name:"EadminSwitch",props:{modelValue:[String,Number,Boolean],url:String,params:Object,field:String},emits:["update:modelValue"],setup:function(e,a){var t=Object(n["ref"])(!1);function c(n){if(e.url){var c;n=n?a.attrs.activeValue:a.attrs.inactiveValue,c=n==a.attrs.activeValue?a.attrs.inactiveValue:a.attrs.activeValue;var u=e.params;u[e.field]=n,Object(l["a"])({url:e.url,method:"put",data:u}).then((function(e){a.emit("update:modelValue",n)})).catch((function(e){t.value=c,a.emit("update:modelValue",c)}))}else a.emit("update:modelValue",n)}return Object(n["watch"])((function(){return e.modelValue}),(function(e){t.value=e})),e.modelValue==a.attrs.activeValue&&(t.value=!0),{changeHandel:c,value:t}}});o.render=u,o.__scopeId="data-v-5fd49606";a["default"]=o}}]);