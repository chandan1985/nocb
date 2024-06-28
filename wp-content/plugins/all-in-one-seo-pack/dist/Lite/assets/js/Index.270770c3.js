var L=Object.defineProperty,M=Object.defineProperties;var T=Object.getOwnPropertyDescriptors;var f=Object.getOwnPropertySymbols;var E=Object.prototype.hasOwnProperty,B=Object.prototype.propertyIsEnumerable;var _=(t,s,e)=>s in t?L(t,s,{enumerable:!0,configurable:!0,writable:!0,value:e}):t[s]=e,a=(t,s)=>{for(var e in s||(s={}))E.call(s,e)&&_(t,e,s[e]);if(f)for(var e of f(s))B.call(s,e)&&_(t,e,s[e]);return t},c=(t,s)=>M(t,T(s));/* empty css             */import{g as I,r as U}from"./params.bea1a08d.js";import{N as H,S as z}from"./ToolsSettings.60cea1aa.js";import{Q as w,d as u,f as d,R as N}from"./index.aff2f9f0.js";import{n as l}from"./vueComponentNormalizer.87056a83.js";import{a as O,C as R,G as j}from"./Header.861bbba4.js";import{S,d as F,h as P}from"./index.4ee805df.js";import{C as q,a as G}from"./LicenseKeyBar.0d9de81d.js";import{S as V}from"./Logo.1a5e022a.js";import{S as K}from"./Support.b1f25bbd.js";import{C as W}from"./Tabs.3955e0b8.js";import{S as D}from"./Close.5e7bcb70.js";import{S as Y}from"./Exclamation.356738ce.js";import{U as X}from"./Url.781a1d48.js";import{S as Q}from"./Gear.c974e953.js";import{T as p}from"./Slide.f5d21606.js";var J=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("svg",{staticClass:"aioseo-description",attrs:{viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"}},[e("path",{attrs:{d:"M0 0h24v24H0V0z",fill:"none"}}),e("path",{attrs:{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z",fill:"currentColor"}})])},Z=[];const tt={},g={};var et=l(tt,J,Z,!1,st,null,null,null);function st(t){for(let s in g)this[s]=g[s]}var it=function(){return et.exports}(),ot=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("svg",{staticClass:"aioseo-folder-open",attrs:{viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"}},[e("path",{attrs:{d:"M0 0h24v24H0V0z",fill:"none"}}),e("path",{attrs:{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z",fill:"currentColor"}})])},nt=[];const at={},m={};var rt=l(at,ot,nt,!1,ct,null,null,null);function ct(t){for(let s in m)this[s]=m[s]}var lt=function(){return rt.exports}(),ut=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"aioseo-help",attrs:{id:"aioseo-help-modal"}},[!t.$isPro&&t.settings.showUpgradeBar&&t.pong?e("core-upgrade-bar"):t._e(),t.$isPro&&t.isUnlicensed&&t.pong?e("core-license-key-bar"):t._e(),t.pong?t._e():e("core-api-bar"),e("div",{staticClass:"aioseo-help-header"},[e("div",{staticClass:"logo"},[t.isUnlicensed?e("a",{attrs:{href:t.$links.utmUrl("header-logo"),target:"_blank"}},[e("svg-aioseo-logo",{attrs:{id:"aioseo-help-logo"}})],1):t._e(),t.isUnlicensed?t._e():e("svg-aioseo-logo",{attrs:{id:"aioseo-help-logo"}})],1),e("div",{attrs:{id:"aioseo-help-close",title:t.strings.close},on:{click:function(i){return i.stopPropagation(),t.toggleModal.apply(null,arguments)}}},[e("svg-close")],1)]),e("div",{staticClass:"help-content"},[e("div",{attrs:{id:"aioseo-help-search"}},[e("base-input",{attrs:{type:"text",size:"medium",placeholder:t.strings.search},on:{input:function(i){return t.inputSearch(i)}}})],1),e("div",{attrs:{id:"aioseo-help-result"}},[e("ul",{staticClass:"aioseo-help-docs"},t._l(t.filteredDocs,function(i,n){return e("li",{key:n},[e("span",{staticClass:"icon"},[e("svg-description")],1),e("a",{attrs:{href:t.$links.utmUrl("help-panel-doc","",i.url),rel:"noopener noreferrer",target:"_blank"}},[t._v(t._s(i.title))])])}),0)]),e("div",{attrs:{id:"aioseo-help-categories"}},[e("ul",{staticClass:"aioseo-help-categories-toggle"},t._l(t.helpPanel.categories,function(i,n){return e("li",{key:n,staticClass:"aioseo-help-category",class:{opened:n==="getting-started"}},[e("header",{on:{click:function(o){return o.stopPropagation(),t.toggleSection(o)}}},[e("span",{staticClass:"folder-open"},[e("svg-folder-open")],1),e("span",{staticClass:"title"},[t._v(t._s(i))]),e("span",{staticClass:"dashicons dashicons-arrow-right-alt2"})]),e("ul",{staticClass:"aioseo-help-docs"},[t._l(t.getCategoryDocs(n).slice(0,5),function(o,r){return e("li",{key:r},[e("span",{staticClass:"icon"},[e("svg-description")],1),e("a",{attrs:{href:t.$links.utmUrl("help-panel-doc","",o.url),rel:"noopener noreferrer",target:"_blank"}},[t._v(t._s(o.title))])])}),e("div",{staticClass:"aioseo-help-additional-docs"},t._l(t.getCategoryDocs(n).slice(5,t.getCategoryDocs(n).length),function(o,r){return e("li",{key:r},[e("span",{staticClass:"icon"},[e("svg-description")],1),e("a",{attrs:{href:t.$links.utmUrl("help-panel-doc","",o.url),rel:"noopener noreferrer",target:"_blank"}},[t._v(t._s(o.title))])])}),0),t.getCategoryDocs(n).length>=5?e("base-button",{staticClass:"aioseo-help-docs-viewall gray medium",on:{click:function(o){return o.stopPropagation(),t.toggleDocs(o)}}},[t._v(" "+t._s(t.strings.viewAll)+" "+t._s(i)+" "+t._s(t.strings.docs)+" ")]):t._e()],2)])}),0)]),e("div",{attrs:{id:"aioseo-help-footer"}},[e("div",{staticClass:"aioseo-help-footer-block"},[e("a",{attrs:{href:t.$links.utmUrl("help-panel-all-docs","","https://aioseo.com/docs/"),rel:"noopener noreferrer",target:"_blank"}},[e("svg-description"),e("h3",[t._v(t._s(t.strings.viewDocumentation))]),e("p",[t._v(t._s(t.strings.browseDocumentation))]),e("base-button",{staticClass:"aioseo-help-docs-viewall gray small"},[t._v(" "+t._s(t.strings.viewAllDocumentation)+" ")])],1)]),e("div",{staticClass:"aioseo-help-footer-block"},[e("a",{attrs:{href:!t.$isPro||!t.$aioseo.license.isActive?t.$links.getUpsellUrl("help-panel","get-support","liteUpgrade"):"https://aioseo.com/account/support/",rel:"noopener noreferrer",target:"_blank"}},[e("svg-support"),e("h3",[t._v(t._s(t.strings.getSupport))]),e("p",[t._v(t._s(t.strings.submitTicket))]),t.$isPro&&t.$aioseo.license.isActive?e("base-button",{staticClass:"aioseo-help-docs-support blue small"},[t._v(" "+t._s(t.strings.submitSupportTicket)+" ")]):t._e(),!t.$isPro||!t.$aioseo.license.isActive?e("base-button",{staticClass:"aioseo-help-docs-support green small"},[t._v(" "+t._s(t.strings.upgradeToPro)+" ")]):t._e()],1)])])])],1)},dt=[];const pt={components:{CoreApiBar:q,CoreLicenseKeyBar:G,CoreUpgradeBar:O,SvgAioseoLogo:V,SvgClose:S,SvgDescription:it,SvgFolderOpen:lt,SvgSupport:K},data(){return{searchItem:null,strings:{close:"Close",search:"Search",viewAll:"View All",docs:"Docs",viewDocumentation:"View Documentation",browseDocumentation:this.$t.sprintf("Browse documentation, reference material, and tutorials for %1$s.","AIOSEO"),viewAllDocumentation:"View All Documentation",getSupport:"Get Support",submitTicket:"Submit a ticket and our world class support team will be in touch soon.",submitSupportTicket:"Submit a Support Ticket",upgradeToPro:"Upgrade to Pro"}}},computed:c(a(a({},w(["settings","isUnlicensed"])),u(["showHelpModal","helpPanel","pong"])),{filteredDocs(){return this.searchItem!==""?Object.values(this.helpPanel.docs).filter(t=>this.searchItem!==null?t.title.toLowerCase().includes(this.searchItem.toLowerCase()):null):null}}),methods:{inputSearch:function(t){F(()=>{this.searchItem=t},1e3)},toggleSection:function(t){t.target.parentNode.parentNode.classList.toggle("opened")},toggleDocs:function(t){t.target.previousSibling.classList.toggle("opened"),t.target.style.display="none"},toggleModal(){document.getElementById("aioseo-help-modal").classList.toggle("visible"),document.body.classList.toggle("modal-open")},getCategoryDocs(t){return Object.values(this.helpPanel.docs).filter(s=>s.categories.flat().includes(t)?s:null)}}},v={};var ft=l(pt,ut,dt,!1,_t,null,null,null);function _t(t){for(let s in v)this[s]=v[s]}var gt=function(){return ft.exports}(),mt="images/dannie-detective.f19b97eb.png",vt=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("transition-slide",{staticClass:"aioseo-notification",attrs:{active:t.active}},[e("div",[e("div",{staticClass:"icon"},[e(t.getIcon,{tag:"component",class:t.notification.type})],1),e("div",{staticClass:"body"},[e("div",{staticClass:"title"},[e("div",[t._v(t._s(t.notification.title))]),e("div",{staticClass:"date"},[t._v(" "+t._s(t.getDate)+" ")])]),e("div",{staticClass:"notification-content",domProps:{innerHTML:t._s(t.notification.content)}}),e("div",{staticClass:"actions"},[t.notification.button1_label&&t.notification.button1_action?e("base-button",{attrs:{size:"small",type:"gray",tag:t.getTagType(t.notification.button1_action),href:t.getHref(t.notification.button1_action),target:t.getTarget(t.notification.button1_action),loading:t.button1Loading},on:{click:function(i){return t.processButtonClick(t.notification.button1_action,1)}}},[t._v(" "+t._s(t.notification.button1_label)+" ")]):t._e(),t.notification.button2_label&&t.notification.button2_action?e("base-button",{attrs:{size:"small",type:"gray",tag:t.getTagType(t.notification.button2_action),href:t.getHref(t.notification.button2_action),target:t.getTarget(t.notification.button2_action),loading:t.button2Loading},on:{click:function(i){return t.processButtonClick(t.notification.button2_action,2)}}},[t._v(" "+t._s(t.notification.button2_label)+" ")]):t._e(),t.notification.dismissed?t._e():e("a",{staticClass:"dismiss",attrs:{href:"#"},on:{click:function(i){return i.stopPropagation(),i.preventDefault(),t.processDismissNotification.apply(null,arguments)}}},[t._v(t._s(t.strings.dismiss))])],1)])])])},ht=[];const yt={components:{SvgCircleCheck:P,SvgCircleClose:D,SvgCircleExclamation:Y,SvgGear:Q,TransitionSlide:p},mixins:[X],props:{notification:{type:Object,required:!0}},data(){return{active:!0,strings:{dismiss:"Dismiss"}}},computed:{getIcon(){switch(this.notification.type){case"warning":return"svg-circle-exclamation";case"error":return"svg-circle-close";case"info":return"svg-gear";case"success":default:return"svg-circle-check"}},getDate(){return this.$moment.utc(this.notification.start).tz(this.$moment.tz.guess()).fromNow().replace("a few seconds ago","a few seconds ago").replace("a minute ago","a minute ago").replace("minutes ago","minutes ago").replace("a day ago","a day ago").replace("days ago","days ago").replace("a month ago","a month ago").replace("months ago","months ago").replace("a year ago","a year ago").replace("years ago","years ago")}},methods:c(a({},d(["dismissNotifications","processButtonAction"])),{processDismissNotification(){this.active=!1,this.dismissNotifications([this.notification.slug]),this.$emit("dismissed-notification")}})},h={};var bt=l(yt,vt,ht,!1,Ct,null,null,null);function Ct(t){for(let s in h)this[s]=h[s]}var $t=function(){return bt.exports}(),kt=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("transition-slide",{staticClass:"aioseo-notification",attrs:{active:t.active}},[e("div",[e("div",{staticClass:"icon"},[e("svg-circle-check",{staticClass:"success"})],1),e("div",{staticClass:"body"},[e("div",{staticClass:"title"},[e("div",[t._v(t._s(t.title))])]),e("div",{staticClass:"notification-content",domProps:{innerHTML:t._s(t.content)}}),e("div",{staticClass:"actions"},[t.step===1?[e("base-button",{attrs:{size:"small",type:"blue"},on:{click:function(i){i.stopPropagation(),t.step=2}}},[t._v(" "+t._s(t.strings.yesILoveIt)+" ")]),e("base-button",{attrs:{size:"small",type:"gray"},on:{click:function(i){i.stopPropagation(),t.step=3}}},[t._v(" "+t._s(t.strings.notReally)+" ")])]:t._e(),t.step===2?[e("base-button",{attrs:{tag:"a",href:"https://wordpress.org/support/plugin/all-in-one-seo-pack/reviews/?filter=5#new-post",size:"small",type:"blue",target:"_blank",rel:"noopener noreferrer"},on:{click:function(i){return t.processDismissNotification(!1)}}},[t._v(" "+t._s(t.strings.okYouDeserveIt)+" ")]),e("base-button",{attrs:{size:"small",type:"gray"},on:{click:function(i){return i.stopPropagation(),i.preventDefault(),t.processDismissNotification(!0)}}},[t._v(" "+t._s(t.strings.nopeMaybeLater)+" ")])]:t._e(),t.step===3?[e("base-button",{attrs:{tag:"a",href:t.feedbackUrl,size:"small",type:"blue",target:"_blank",rel:"noopener noreferrer"},on:{click:function(i){return t.processDismissNotification(!1)}}},[t._v(" "+t._s(t.strings.giveFeedback)+" ")]),e("base-button",{attrs:{size:"small",type:"gray"},on:{click:function(i){return i.stopPropagation(),i.preventDefault(),t.processDismissNotification(!1)}}},[t._v(" "+t._s(t.strings.noThanks)+" ")])]:t._e(),t.notification.dismissed?t._e():e("a",{staticClass:"dismiss",attrs:{href:"#"},on:{click:function(i){return i.stopPropagation(),i.preventDefault(),t.processDismissNotification(!1)}}},[t._v(t._s(t.strings.dismiss))])],2)])])])},wt=[];const Nt={components:{SvgCircleCheck:P,TransitionSlide:p},props:{notification:{type:Object,required:!0}},data(){return{step:1,active:!0,strings:{dismiss:"Dismiss",yesILoveIt:"Yes, I love it!",notReally:"Not Really...",okYouDeserveIt:"Ok, you deserve it",nopeMaybeLater:"Nope, maybe later",giveFeedback:"Give feedback",noThanks:"No thanks"}}},computed:c(a({},u(["options"])),{title(){switch(this.step){case 2:return"That's Awesome!";case 3:return"Help us improve";default:return this.$t.sprintf("Are you enjoying %1$s?","AIOSEO")}},content(){switch(this.step){case 2:return"Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?<br><br><strong>~ Syed Balkhi<br>"+this.$t.sprintf("CEO of %1$s","All in One SEO")+"</strong>";case 3:return this.$t.sprintf("We're sorry to hear you aren't enjoying %1$s. We would love a chance to improve. Could you take a minute and let us know what we can do better?","All in One SEO");default:return""}},feedbackUrl(){const t=this.options.general&&this.options.general.licenseKey?this.options.general.licenseKey:"",s=this.$isPro?"pro":"lite";return this.$links.utmUrl("notification-review-notice",this.$aioseo.version,"https://aioseo.com/plugin-feedback/?wpf7528_24="+encodeURIComponent(this.$aioseo.urls.home)+"&wpf7528_26="+t+"&wpf7528_27="+s+"&wpf7528_28="+this.$aioseo.version)}}),methods:c(a({},d(["dismissNotifications","processButtonAction"])),{processDismissNotification(t=!1){this.active=!1,this.dismissNotifications([this.notification.slug+(t?"-delay":"")]),this.$emit("dismissed-notification")}})},y={};var St=l(Nt,kt,wt,!1,Pt,null,null,null);function Pt(t){for(let s in y)this[s]=y[s]}var Dt=function(){return St.exports}(),xt=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("transition-slide",{staticClass:"aioseo-notification",attrs:{active:t.active}},[e("div",[e("div",{staticClass:"icon"},[e("svg-circle-close",{staticClass:"error"})],1),e("div",{staticClass:"body"},[e("div",{staticClass:"title"},[e("div",[t._v(t._s(t.strings.title))])]),e("div",{staticClass:"notification-content",domProps:{innerHTML:t._s(t.content)}}),e("div",{staticClass:"actions"},[e("base-button",{attrs:{size:"small",type:"green",tag:"a",href:t.$links.utmUrl("notification-unlicensed-addons"),target:"_blank"}},[t._v(" "+t._s(t.strings.upgrade)+" ")])],1)])])])},At=[];const Lt={components:{SvgCircleClose:D,TransitionSlide:p},props:{notification:{type:Object,required:!0}},data(){return{active:!0,strings:{title:this.$t.sprintf("%1$s %2$s Not Configured Properly","AIOSEO","Addons"),learnMore:"Learn More",upgrade:"Upgrade"}}},computed:c(a({},u(["options"])),{content(){let t="<ul>";return this.notification.addons.forEach(s=>{t+="<li><strong>AIOSEO - "+s.name+"</strong></li>"}),t+="</ul>",this.notification.message+t}})},b={};var Mt=l(Lt,xt,At,!1,Tt,null,null,null);function Tt(t){for(let s in b)this[s]=b[s]}var Et=function(){return Mt.exports}(),Bt=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"aioseo-notification-cards"},[t.notifications.length?t._l(t.notifications,function(i){return e(i.component?i.component:"core-notification",{key:i.slug,ref:"notification",refInFor:!0,tag:"component",attrs:{notification:i},on:{"dismissed-notification":function(n){return t.$emit("dismissed-notification")}}})}):t._e(),t.notifications.length?t._e():e("div",{key:"no-notifications"},[t._t("no-notifications",function(){return[e("div",{staticClass:"no-notifications"},[e("img",{attrs:{src:t.$getImgUrl(t.dannieDetectiveImg)}}),e("div",{staticClass:"great-scott"},[t._v(" "+t._s(t.strings.greatScott)+" ")]),e("div",{staticClass:"no-new-notifications"},[t._v(" "+t._s(t.strings.noNewNotifications)+" ")]),t.dismissedCount?e("a",{staticClass:"dismiss",attrs:{href:"#"},on:{click:function(i){return i.stopPropagation(),t.$emit("toggle-dismissed")}}},[t._v(" "+t._s(t.strings.seeDismissed)+" ")]):t._e()])]})],2)],2)},It=[];const Ut={components:{CoreNotification:$t,NotificationsReview:Dt,NotificationsUnlicensedAddons:Et},props:{dismissedCount:{type:Number,required:!0},notifications:{type:Array,required:!0}},data(){return{dannieDetectiveImg:mt,strings:{greatScott:"Great Scott! Where'd they all go?",noNewNotifications:"You have no new notifications.",seeDismissed:"See Dismissed Notifications"}}}},C={};var Ht=l(Ut,Bt,It,!1,zt,null,null,null);function zt(t){for(let s in C)this[s]=C[s]}var Ot=function(){return Ht.exports}(),Rt=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{ref:"aioseo-notifications",staticClass:"aioseo-notifications"},[e("transition",{attrs:{name:"notifications-slide"}},[t.showNotifications?e("div",{staticClass:"notification-menu"},[e("div",{staticClass:"notification-header"},[e("span",{staticClass:"new-notifications"},[t._v("("+t._s(t.notificationsCount)+") "+t._s(t.notificationTitle))]),e("div",{staticClass:"dismissed-notifications"},[!t.dismissed&&t.dismissedNotificationsCount?e("a",{attrs:{href:"#"},on:{click:function(i){i.stopPropagation(),i.preventDefault(),t.dismissed=!0}}},[t._v(t._s(t.strings.dismissedNotifications))]):t._e(),t.dismissed&&t.dismissedNotificationsCount?e("a",{attrs:{href:"#"},on:{click:function(i){i.stopPropagation(),i.preventDefault(),t.dismissed=!1}}},[t._v(t._s(t.strings.activeNotifications))]):t._e()]),e("svg-close",{on:{click:t.toggleNotifications}})],1),e("core-notification-cards",{staticClass:"notification-cards",attrs:{notifications:t.filteredNotifications,dismissedCount:t.dismissedNotificationsCount},on:{"toggle-dismissed":function(i){t.dismissed=!t.dismissed}}}),e("div",{staticClass:"notification-footer"},[e("div",{staticClass:"pagination"},[t.totalPages>1?t._l(t.pages,function(i,n){return e("div",{key:n,staticClass:"page-number",class:{active:i.number===1+t.currentPage},on:{click:function(o){t.currentPage=i.number-1}}},[t._v(" "+t._s(i.number)+" ")])}):t._e()],2),t.dismissed?t._e():e("div",{staticClass:"dismiss-all"},[t.notifications.length?e("a",{staticClass:"dismiss",attrs:{href:"#"},on:{click:function(i){return i.stopPropagation(),i.preventDefault(),t.processDismissAllNotifications.apply(null,arguments)}}},[t._v(t._s(t.strings.dismissAll))]):t._e()])])],1):t._e()]),e("transition",{attrs:{name:"notifications-fade"}},[t.showNotifications?e("div",{staticClass:"overlay",on:{click:t.toggleNotifications}}):t._e()])],1)},jt=[];const Ft={components:{CoreNotificationCards:Ot,SvgClose:S},mixins:[H],data(){return{dismissed:!1,maxNotifications:Number.MAX_SAFE_INTEGER,currentPage:0,totalPages:1,strings:{dismissedNotifications:"Dismissed Notifications",dismissAll:"Dismiss All"}}},watch:{showNotifications(t){t?(this.currentPage=0,this.setMaxNotifications(),this.addBodyClass()):this.removeBodyClass()},dismissed(){this.setMaxNotifications()},notifications(){this.setMaxNotifications()}},computed:c(a({},u(["showNotifications"])),{filteredNotifications(){return[...this.notifications].splice(this.currentPage===0?0:this.currentPage*this.maxNotifications,this.maxNotifications)},pages(){const t=[];for(let s=0;s<this.totalPages;s++)t.push({number:s+1});return t}}),methods:c(a(a({},d(["dismissNotifications"])),N(["toggleNotifications"])),{escapeListener(t){t.key==="Escape"&&this.showNotifications&&this.toggleNotifications()},addBodyClass(){document.body.classList.add("aioseo-show-notifications")},removeBodyClass(){document.body.classList.remove("aioseo-show-notifications")},documentClick(t){if(!this.showNotifications)return;const s=t&&t.target?t.target:null,e=document.querySelector("#wp-admin-bar-aioseo-notifications");if(e&&(e===s||e.contains(s)))return;const i=document.querySelector("#toplevel_page_aioseo .wp-first-item"),n=document.querySelector("#toplevel_page_aioseo .wp-first-item .aioseo-menu-notification-indicator");if(i&&i.contains(n)&&(i===s||i.contains(s)))return;const o=this.$refs["aioseo-notifications"];o&&(o===s||o.contains(s))||this.toggleNotifications()},notificationsLinkClick(t){t.preventDefault(),this.toggleNotifications()},processDismissAllNotifications(){const t=[];this.notifications.forEach(s=>{t.push(s.slug)}),this.dismissNotifications(t).then(()=>{this.setMaxNotifications()})},setMaxNotifications(){const t=this.currentPage;this.currentPage=0,this.totalPages=1,this.maxNotifications=Number.MAX_SAFE_INTEGER,this.$nextTick(async()=>{const s=[],e=document.querySelectorAll(".notification-menu .aioseo-notification");e&&e.forEach(n=>{let o=n.offsetHeight;const r=window.getComputedStyle?getComputedStyle(n,null):n.currentStyle,x=parseInt(r.marginTop)||0,A=parseInt(r.marginBottom)||0;o+=x+A,s.push(o)});const i=document.querySelector(".notification-menu .aioseo-notification-cards");if(i){let n=0,o=0;for(let r=0;r<s.length&&(o+=s[r],!(o>i.offsetHeight));r++)n++;this.maxNotifications=n||1,this.totalPages=Math.ceil(s.length/n)}this.currentPage=t>this.totalPages-1?this.totalPages-1:t})}}),mounted(){document.addEventListener("keydown",this.escapeListener),document.addEventListener("click",this.documentClick);const t=document.querySelector("#wp-admin-bar-aioseo-notifications .ab-item");t&&t.addEventListener("click",this.notificationsLinkClick);const s=document.querySelector("#toplevel_page_aioseo .wp-first-item"),e=document.querySelector("#toplevel_page_aioseo .wp-first-item .aioseo-menu-notification-indicator");s&&e&&s.addEventListener("click",this.notificationsLinkClick)}},$={};var qt=l(Ft,Rt,jt,!1,Gt,null,null,null);function Gt(t){for(let s in $)this[s]=$[s]}var Vt=function(){return qt.exports}(),Kt=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",[e("core-notifications"),e("div",{staticClass:"aioseo-main"},[e("core-header",{attrs:{"page-name":t.pageName}}),e("grid-container",[t.showTabs?e("core-main-tabs",{key:t.tabsKey,attrs:{tabs:t.tabs,showSaveButton:t.shouldShowSaveButton}}):t._e(),e("transition",{attrs:{name:"route-fade",mode:"out-in"}},[t._t("default")],2),t.shouldShowSaveButton?e("div",{staticClass:"save-changes"},[e("base-button",{attrs:{type:"blue",size:"medium",loading:t.loading},on:{click:t.processSaveChanges}},[t._v(" "+t._s(t.strings.saveChanges)+" ")])],1):t._e()],1)],1),t.helpPanel.docs&&Object.keys(t.helpPanel.docs).length?e("core-help"):t._e()],1)},Wt=[];const Yt={components:{CoreHeader:R,CoreHelp:gt,CoreMainTabs:W,CoreNotifications:Vt,GridContainer:j},mixins:[z],props:{pageName:{type:String,required:!0},showTabs:{type:Boolean,default(){return!0}},showSaveButton:{type:Boolean,default(){return!0}},excludeTabs:{type:Array,default(){return[]}}},data(){return{tabsKey:0,strings:{saveChanges:"Save Changes"}}},watch:{excludeTabs(){this.tabsKey+=1}},computed:c(a(a({},w(["settings"])),u(["loading","options","showNotifications","helpPanel"])),{tabs(){return this.$router.options.routes.filter(t=>t.name&&t.meta&&t.meta.name).filter(t=>this.$allowed(t.meta.access)).filter(t=>!(t.meta.display==="lite"&&this.$isPro||t.meta.display==="pro"&&!this.$isPro)).filter(t=>!this.excludeTabs.includes(t.name)).map(t=>({slug:t.name,name:t.meta.name,url:{name:t.name},access:t.meta.access,pro:!!t.meta.pro}))},shouldShowSaveButton(){if(this.$route&&this.$route.name){const t=this.$router.options.routes.find(s=>s.name===this.$route.name);if(t&&t.meta&&t.meta.hideSaveButton)return!1}return this.showSaveButton}}),methods:a({},N(["toggleNotifications"])),mounted(){I().notifications&&(this.showNotifications||this.toggleNotifications(),setTimeout(()=>{U("notifications")},500))}},k={};var Xt=l(Yt,Kt,Wt,!1,Qt,null,null,null);function Qt(t){for(let s in k)this[s]=k[s]}var ge=function(){return Xt.exports}();export{ge as C,Ot as a};
