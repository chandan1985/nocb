import{u as R,g as U,n as B}from"./links.50b3c915.js";import"./default-i18n.41786823.js";import{u as H,W as P}from"./Wizard.b6e7ef2e.js";import{C as V,a as F}from"./index.333853dc.js";import{r as o,c as _,e as s,w as n,d as k,b as g,o as u,a as t,g as d,t as r,i as O,F as Y,h as D}from"./vue.runtime.esm-bundler.3acceac0.js";import{a as G}from"./Caret.918abbf1.js";import{_ as q}from"./_plugin-vue_export-helper.109ab23d.js";import{G as j,a as Q}from"./Row.3c0caea3.js";/* empty css                                              */import"./constants.008ef172.js";import{B as J}from"./Checkbox.56c563fd.js";import{C as K}from"./Index.86d6af04.js";import{C as X}from"./ProBadge.2060c7e5.js";import{C as Z}from"./Tooltip.38bcb67e.js";import{W as ee,a as te,b as se}from"./Header.2267e3e0.js";import{W as oe,_ as ne}from"./Steps.968d443f.js";import"./isArrayLikeObject.71906cce.js";import"./addons.d112d026.js";import"./upperFirst.92607be0.js";import"./_stringToArray.4de3b1f3.js";import"./toString.3425ebfb.js";/* empty css                                            */import"./Checkmark.9bcd12eb.js";/* empty css                                              */import"./Logo.35a4df98.js";const ie={setup(){const{strings:a}=H();return{rootStore:R(),setupWizardStore:U(),composableStrings:a}},components:{BaseCheckbox:J,CoreAlert:V,CoreModal:K,CoreProBadge:X,CoreTooltip:Z,GridColumn:j,GridRow:Q,SvgCircleQuestionMark:F,SvgClose:G,WizardBody:ee,WizardCloseAndExit:oe,WizardContainer:te,WizardHeader:se,WizardSteps:ne},mixins:[P],data(){return{loading:!1,stage:"smart-recommendations",showModal:!1,loadingModal:!1,strings:B(this.composableStrings,{setupSiteAnalyzer:this.$t.__("Setup Site Analyzer + Smart Recommendations",this.$td),description:this.$t.sprintf(this.$t.__("Get helpful suggestions from %1$s on how to optimize your website content, so you can rank higher in search results.",this.$td),"AIOSEO"),yourEmailAddress:this.$t.__("Your Email Address",this.$td),yourEmailIsNeeded:this.$t.__("Your email is needed so you can receive SEO recommendations. This email will also be used to connect your site with our SEO API.",this.$td),helpMakeAioseoBetter:this.$t.sprintf(this.$t.__("Help make %1$s better for everyone",this.$td),"AIOSEO"),yesCountMeIn:this.$t.__("Yes, count me in",this.$td),wouldYouLikeToPurchase:this.$t.__("Would you like to purchase and install the following features now?",this.$td),theseFeaturesAreAvailable:this.$t.__("An upgrade is required to unlock the following features.",this.$td),youWontHaveAccess:this.$t.__("You won't have access to this functionality until the extensions have been purchased and installed.",this.$td),illDoItLater:this.$t.__("I'll do it later",this.$td),purchaseAndInstallNow:this.$t.__("Purchase and Install Now",this.$td),bonusText:this.$t.sprintf(this.$t.__("%1$sBonus:%2$s You can upgrade your plan today and %3$ssave %4$s off%5$s (discount auto-applied).",this.$td),"<strong>","</strong>","<strong>",this.$constants.DISCOUNT_PERCENTAGE,"</strong>"),usageTrackingTooltip:this.$t.sprintf(this.$t.__("Complete documentation on usage tracking is available %1$shere%2$s.",this.$td),this.$t.sprintf('<strong><a href="%1$s" target="_blank">',this.$links.getDocUrl("usageTracking")),"</a></strong>")})}},computed:{selectedFeaturesNeedsUpsell(){let a=!1;return this.setupWizardStore.features.forEach(i=>{this.needsUpsell(this.features.find(f=>f.value===i))&&(a=!0)}),a}},methods:{purchase(){this.modalLoading=!0;const a=`&license-redirect=${btoa(this.rootStore.aioseo.urls.aio.wizard)}#/license-key`;window.open("https://aioseo.com/pricing/?features[]="+this.getSelectedUpsellFeatures.map(i=>i.value).join("&features[]=")+a),this.$router.push(this.setupWizardStore.getNextLink)},saveAndContinue(){this.loading=!0,this.setupWizardStore.saveWizard("smartRecommendations").then(()=>{if(!this.selectedFeaturesNeedsUpsell)return this.$router.push(this.setupWizardStore.getNextLink);this.showModal=!0,this.loading=!1})},skipStep(){this.setupWizardStore.saveWizard(),this.$router.push(this.setupWizardStore.getNextLink)},preventUncheck(a){a.preventDefault(),a.stopPropagation()}},mounted(){this.setupWizardStore.smartRecommendations.accountInfo=this.rootStore.aioseo.user.data.data.user_email}},re={class:"aioseo-wizard-smart-recommendations"},ae={class:"header"},le={class:"description"},de={class:"aioseo-settings-row no-border small-padding"},ce={class:"settings-name"},ue={class:"name small-margin"},me={class:"aioseo-description"},_e={key:0,class:"aioseo-settings-row no-border no-margin small-padding"},pe={class:"settings-name"},he={class:"name small-margin"},ge=["innerHTML"],fe={class:"go-back"},ve=t("div",{class:"spacer"},null,-1),ke={class:"aioseo-modal-body"},ye=["innerHTML"],Se={class:"settings-name"},we={class:"name small-margin"},be={class:"aioseo-description-text"},ze=["innerHTML"],Ce={class:"actions"},We=t("div",{class:"spacer"},null,-1),Ae={class:"go-back"};function Te(a,i,f,c,e,m){const y=o("wizard-header"),S=o("wizard-steps"),w=o("base-input"),b=o("svg-circle-question-mark"),z=o("core-tooltip"),C=o("base-toggle"),p=o("router-link"),h=o("base-button"),W=o("wizard-body"),A=o("wizard-close-and-exit"),T=o("wizard-container"),x=o("svg-close"),M=o("core-pro-badge"),v=o("grid-column"),$=o("base-checkbox"),L=o("grid-row"),I=o("core-alert"),N=o("core-modal");return u(),_("div",re,[s(y),s(T,null,{default:n(()=>[s(W,null,{footer:n(()=>[t("div",fe,[s(p,{to:c.setupWizardStore.getPrevLink,class:"no-underline"},{default:n(()=>[d("←")]),_:1},8,["to"]),d("   "),s(p,{to:c.setupWizardStore.getPrevLink},{default:n(()=>[d(r(e.strings.goBack),1)]),_:1},8,["to"])]),ve,s(h,{type:"gray",onClick:m.skipStep},{default:n(()=>[d(r(e.strings.skipThisStep),1)]),_:1},8,["onClick"]),s(h,{type:"blue",loading:e.loading,onClick:m.saveAndContinue},{default:n(()=>[d(r(e.strings.saveAndContinue)+" →",1)]),_:1},8,["loading","onClick"])]),default:n(()=>[s(S),t("div",ae,r(e.strings.setupSiteAnalyzer),1),t("div",le,r(e.strings.description),1),t("div",de,[t("div",ce,[t("div",ue,r(e.strings.yourEmailAddress),1)]),s(w,{size:"medium",modelValue:c.setupWizardStore.smartRecommendations.accountInfo,"onUpdate:modelValue":i[0]||(i[0]=l=>c.setupWizardStore.smartRecommendations.accountInfo=l)},null,8,["modelValue"]),t("div",me,r(e.strings.yourEmailIsNeeded),1)]),a.$isPro?g("",!0):(u(),_("div",_e,[t("div",pe,[t("div",he,[d(r(e.strings.helpMakeAioseoBetter)+" ",1),s(z,null,{tooltip:n(()=>[t("div",{innerHTML:e.strings.usageTrackingTooltip},null,8,ge)]),default:n(()=>[s(b)]),_:1})])]),s(C,{modelValue:c.setupWizardStore.smartRecommendations.usageTracking,"onUpdate:modelValue":i[1]||(i[1]=l=>c.setupWizardStore.smartRecommendations.usageTracking=l)},{default:n(()=>[d(r(e.strings.yesCountMeIn),1)]),_:1},8,["modelValue"])]))]),_:1}),s(A)]),_:1}),e.showModal?(u(),k(N,{key:0,"no-header":"",onClose:i[4]||(i[4]=l=>e.showModal=!1)},{body:n(()=>[t("div",ke,[t("button",{class:"close",onClick:i[3]||(i[3]=O(l=>e.showModal=!1,["stop"]))},[s(x,{onClick:i[2]||(i[2]=l=>e.showModal=!1)})]),t("h3",null,r(e.strings.wouldYouLikeToPurchase),1),t("div",{class:"available-features",innerHTML:e.strings.theseFeaturesAreAvailable},null,8,ye),(u(!0),_(Y,null,D(a.getSelectedUpsellFeatures,(l,E)=>(u(),_("div",{key:E,class:"aioseo-settings-row feature-grid small-padding medium-margin"},[s(L,null,{default:n(()=>[s(v,{xs:"11"},{default:n(()=>[t("div",Se,[t("div",we,[d(r(l.name)+" ",1),a.needsUpsell(l)?(u(),k(M,{key:0})):g("",!0)]),t("div",be,r(l.description),1)])]),_:2},1024),s(v,{xs:"1"},{default:n(()=>[s($,{round:"",class:"no-clicks",type:"green",modelValue:!0,onClick:m.preventUncheck},null,8,["onClick"])]),_:1})]),_:2},1024)]))),128)),t("div",{class:"available-features no-access",innerHTML:e.strings.youWontHaveAccess},null,8,ze),t("div",Ce,[We,t("div",Ae,[s(p,{to:c.setupWizardStore.getNextLink},{default:n(()=>[d(r(e.strings.illDoItLater),1)]),_:1},8,["to"])]),s(h,{type:"green",loading:e.loadingModal,onClick:m.purchase},{default:n(()=>[d(r(e.strings.purchaseAndInstallNow),1)]),_:1},8,["loading","onClick"])]),s(I,{type:"yellow",innerHTML:e.strings.bonusText},null,8,["innerHTML"])])]),_:1})):g("",!0)])}const et=q(ie,[["render",Te]]);export{et as default};
