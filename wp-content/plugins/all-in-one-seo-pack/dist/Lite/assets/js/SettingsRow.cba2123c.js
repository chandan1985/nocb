import{G as _,a as g}from"./Row.3c0caea3.js";import{r as l,o as t,d as f,w as a,f as o,e as c,a as r,n as s,g as p,t as y,c as u,b as d}from"./vue.runtime.esm-bundler.3acceac0.js";import{_ as S}from"./_plugin-vue_export-helper.109ab23d.js";const B={components:{GridColumn:_,GridRow:g},props:{align:Boolean,alignSmall:Boolean,name:String,required:Boolean,noHorizontalMargin:{type:Boolean,default:!1},noVerticalMargin:{type:Boolean,default:!1},noBorder:{type:Boolean,default:!1},leftSize:{type:String,default(){return"3"}},rightSize:{type:String,default(){return"9"}}}},h={key:0,class:"required-field"},v={key:0,class:"aioseo-description"},w={class:"settings-content"};function z(n,C,e,V,k,x){const i=l("grid-column"),m=l("grid-row");return t(),f(m,{class:s(["aioseo-settings-row",{"no-horizontal-margin":e.noHorizontalMargin,"no-vertical-margin":e.noVerticalMargin,"no-border":e.noBorder}])},{default:a(()=>[o(n.$slots,"header"),c(i,{md:e.leftSize},{default:a(()=>[r("div",{class:s(["settings-name",{"no-name":!e.name}])},[r("div",{class:s(["name",[{align:e.align},{"align-small":e.alignSmall}]])},[o(n.$slots,"name",{},()=>[p(y(e.name)+" ",1),e.required?(t(),u("span",h," * ")):d("",!0)])],2),n.$slots.description?(t(),u("div",v,[o(n.$slots,"description")])):d("",!0)],2)]),_:3},8,["md"]),c(i,{md:e.rightSize},{default:a(()=>[r("div",w,[o(n.$slots,"content")])]),_:3},8,["md"])]),_:3},8,["class"])}const q=S(B,[["render",z]]);export{q as C};
