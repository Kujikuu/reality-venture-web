import{c as z,r as l,f as q,j as t,u as Q,b as X,i as J,H as K,h as Z,G as ee,L as ne,d as te}from"./app-DyagaB9z.js";import{S as D}from"./star-DP260srv.js";import{C as ae}from"./clock-D_ZTtHMv.js";/**
 * @license lucide-react v0.563.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const re=[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M12 16v-4",key:"1dtifu"}],["path",{d:"M12 8h.01",key:"e9boi3"}]],le=z("info",re);/**
 * @license lucide-react v0.563.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const se=[["path",{d:"M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719",key:"1sd12s"}]],ie=z("message-circle",se);var N=function(e,a){return N=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(r,n){r.__proto__=n}||function(r,n){for(var s in n)Object.prototype.hasOwnProperty.call(n,s)&&(r[s]=n[s])},N(e,a)};function f(e,a){if(typeof a!="function"&&a!==null)throw new TypeError("Class extends value "+String(a)+" is not a constructor or null");N(e,a);function r(){this.constructor=e}e.prototype=a===null?Object.create(a):(r.prototype=a.prototype,new r)}var y=function(){return y=Object.assign||function(a){for(var r,n=1,s=arguments.length;n<s;n++){r=arguments[n];for(var d in r)Object.prototype.hasOwnProperty.call(r,d)&&(a[d]=r[d])}return a},y.apply(this,arguments)};function oe(e,a){a===void 0&&(a={});var r=a.insertAt;if(!(typeof document>"u")){var n=document.head||document.getElementsByTagName("head")[0],s=document.createElement("style");s.type="text/css",r==="top"&&n.firstChild?n.insertBefore(s,n.firstChild):n.appendChild(s),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(document.createTextNode(e))}}var ce=`/*
  code is extracted from Calendly's embed stylesheet: https://assets.calendly.com/assets/external/widget.css
*/

.calendly-inline-widget,
.calendly-inline-widget *,
.calendly-badge-widget,
.calendly-badge-widget *,
.calendly-overlay,
.calendly-overlay * {
  font-size: 16px;
  line-height: 1.2em;
}

.calendly-inline-widget {
  min-width: 320px;
  height: 630px;
}

.calendly-inline-widget iframe,
.calendly-badge-widget iframe,
.calendly-overlay iframe {
  display: inline;
  width: 100%;
  height: 100%;
}

.calendly-popup-content {
  position: relative;
}

.calendly-popup-content.calendly-mobile {
  -webkit-overflow-scrolling: touch;
  overflow-y: auto;
}

.calendly-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  z-index: 9999;
  background-color: #a5a5a5;
  background-color: rgba(31, 31, 31, 0.4);
}

.calendly-overlay .calendly-close-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.calendly-overlay .calendly-popup {
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translateY(-50%) translateX(-50%);
  transform: translateY(-50%) translateX(-50%);
  width: 80%;
  min-width: 900px;
  max-width: 1000px;
  height: 90%;
  max-height: 680px;
}

@media (max-width: 975px) {
  .calendly-overlay .calendly-popup {
    position: fixed;
    top: 50px;
    left: 0;
    right: 0;
    bottom: 0;
    -webkit-transform: none;
    transform: none;
    width: 100%;
    height: auto;
    min-width: 0;
    max-height: none;
  }
}

.calendly-overlay .calendly-popup .calendly-popup-content {
  height: 100%;
}

.calendly-overlay .calendly-popup-close {
  position: absolute;
  top: 25px;
  right: 25px;
  color: #fff;
  width: 19px;
  height: 19px;
  cursor: pointer;
  background: url(https://assets.calendly.com/assets/external/close-icon.svg)
    no-repeat;
  background-size: contain;
}

@media (max-width: 975px) {
  .calendly-overlay .calendly-popup-close {
    top: 15px;
    right: 15px;
  }
}

.calendly-badge-widget {
  position: fixed;
  right: 20px;
  bottom: 15px;
  z-index: 9998;
}

.calendly-badge-widget .calendly-badge-content {
  display: table-cell;
  width: auto;
  height: 45px;
  padding: 0 30px;
  border-radius: 25px;
  box-shadow: rgba(0, 0, 0, 0.25) 0 2px 5px;
  font-family: sans-serif;
  text-align: center;
  vertical-align: middle;
  font-weight: bold;
  font-size: 14px;
  color: #fff;
  cursor: pointer;
}

.calendly-badge-widget .calendly-badge-content.calendly-white {
  color: #666a73;
}

.calendly-badge-widget .calendly-badge-content span {
  display: block;
  font-size: 12px;
}

.calendly-spinner {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  -webkit-transform: translateY(-50%);
  transform: translateY(-50%);
  text-align: center;
  z-index: -1;
}

.calendly-spinner > div {
  display: inline-block;
  width: 18px;
  height: 18px;
  background-color: #e1e1e1;
  border-radius: 50%;
  vertical-align: middle;
  -webkit-animation: calendly-bouncedelay 1.4s infinite ease-in-out;
  animation: calendly-bouncedelay 1.4s infinite ease-in-out;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
}

.calendly-spinner .calendly-bounce1 {
  -webkit-animation-delay: -0.32s;
  animation-delay: -0.32s;
}

.calendly-spinner .calendly-bounce2 {
  -webkit-animation-delay: -0.16s;
  animation-delay: -0.16s;
}

@-webkit-keyframes calendly-bouncedelay {
  0%,
  80%,
  100% {
    -webkit-transform: scale(0);
    transform: scale(0);
  }

  40% {
    -webkit-transform: scale(1);
    transform: scale(1);
  }
}

@keyframes calendly-bouncedelay {
  0%,
  80%,
  100% {
    -webkit-transform: scale(0);
    transform: scale(0);
  }

  40% {
    -webkit-transform: scale(1);
    transform: scale(1);
  }
}
`;oe(ce);function w(e){return e.charAt(0)==="#"?e.slice(1):e}function de(e){return e!=null&&e.primaryColor&&(e.primaryColor=w(e.primaryColor)),e!=null&&e.textColor&&(e.textColor=w(e.textColor)),e!=null&&e.backgroundColor&&(e.backgroundColor=w(e.backgroundColor)),e}var A;(function(e){e.PROFILE_PAGE_VIEWED="calendly.profile_page_viewed",e.EVENT_TYPE_VIEWED="calendly.event_type_viewed",e.DATE_AND_TIME_SELECTED="calendly.date_and_time_selected",e.EVENT_SCHEDULED="calendly.event_scheduled",e.PAGE_HEIGHT="calendly.page_height"})(A||(A={}));var B=function(e){var a=e.url,r=e.prefill,n=r===void 0?{}:r,s=e.pageSettings,d=s===void 0?{}:s,u=e.utm,c=u===void 0?{}:u,m=e.embedType,o=de(d),p=o.backgroundColor,h=o.hideEventTypeDetails,i=o.hideLandingPageDetails,x=o.primaryColor,g=o.textColor,F=o.hideGdprBanner,C=n.customAnswers,b=n.date,_=n.email,j=n.firstName,E=n.guests,k=n.lastName,S=n.location,L=n.name,T=c.utmCampaign,I=c.utmContent,P=c.utmMedium,O=c.utmSource,R=c.utmTerm,U=c.salesforce_uuid,v=a.indexOf("?"),M=v>-1,$=a.slice(v+1),H=M?a.slice(0,v):a,Y=[M?$:null,p?"background_color=".concat(p):null,h?"hide_event_type_details=1":null,i?"hide_landing_page_details=1":null,x?"primary_color=".concat(x):null,g?"text_color=".concat(g):null,F?"hide_gdpr_banner=1":null,L?"name=".concat(encodeURIComponent(L)):null,S?"location=".concat(encodeURIComponent(S)):null,j?"first_name=".concat(encodeURIComponent(j)):null,k?"last_name=".concat(encodeURIComponent(k)):null,E?"guests=".concat(E.map(encodeURIComponent).join(",")):null,_?"email=".concat(encodeURIComponent(_)):null,b&&b instanceof Date?"date=".concat(me(b)):null,T?"utm_campaign=".concat(encodeURIComponent(T)):null,I?"utm_content=".concat(encodeURIComponent(I)):null,P?"utm_medium=".concat(encodeURIComponent(P)):null,O?"utm_source=".concat(encodeURIComponent(O)):null,R?"utm_term=".concat(encodeURIComponent(R)):null,U?"salesforce_uuid=".concat(encodeURIComponent(U)):null,m?"embed_type=".concat(m):null,"embed_domain=1"].concat(C?ue(C):[]).filter(function(V){return V!==null}).join("&");return"".concat(H,"?").concat(Y)},me=function(e){var a=e.getMonth()+1,r=e.getDate(),n=e.getFullYear();return[n,a<10?"0".concat(a):a,r<10?"0".concat(r):r].join("-")},pe=/^a\d{1,2}$/,ue=function(e){var a=Object.keys(e).filter(function(r){return r.match(pe)});return a.length?a.map(function(r){return"".concat(r,"=").concat(encodeURIComponent(e[r]))}):[]},G=(function(e){f(a,e);function a(){return e!==null&&e.apply(this,arguments)||this}return a.prototype.render=function(){return l.createElement("div",{className:"calendly-spinner"},l.createElement("div",{className:"calendly-bounce1"}),l.createElement("div",{className:"calendly-bounce2"}),l.createElement("div",{className:"calendly-bounce3"}))},a})(l.Component),he="calendly-inline-widget",ge=(function(e){f(a,e);function a(r){var n=e.call(this,r)||this;return n.state={isLoading:!0},n.onLoad=n.onLoad.bind(n),n}return a.prototype.onLoad=function(){this.setState({isLoading:!1})},a.prototype.render=function(){var r=B({url:this.props.url,pageSettings:this.props.pageSettings,prefill:this.props.prefill,utm:this.props.utm,embedType:"Inline"}),n=this.props.LoadingSpinner||G;return l.createElement("div",{className:this.props.className||he,style:this.props.styles||{}},this.state.isLoading&&l.createElement(n,null),l.createElement("iframe",{width:"100%",height:"100%",frameBorder:"0",title:this.props.iframeTitle||"Calendly Scheduling Page",onLoad:this.onLoad,src:r}))},a})(l.Component),ye=(function(e){f(a,e);function a(r){var n=e.call(this,r)||this;return n.state={isLoading:!0},n.onLoad=n.onLoad.bind(n),n}return a.prototype.onLoad=function(){this.setState({isLoading:!1})},a.prototype.render=function(){var r=B({url:this.props.url,pageSettings:this.props.pageSettings,prefill:this.props.prefill,utm:this.props.utm,embedType:"Inline"}),n=this.props.LoadingSpinner||G;return l.createElement(l.Fragment,null,this.state.isLoading&&l.createElement(n,null),l.createElement("iframe",{width:"100%",height:"100%",frameBorder:"0",title:this.props.iframeTitle||"Calendly Scheduling Page",onLoad:this.onLoad,src:r}))},a})(l.Component),W=(function(e){if(!e.open)return null;if(!e.rootElement)throw new Error("[react-calendly]: PopupModal rootElement property cannot be undefined");return q.createPortal(l.createElement("div",{className:"calendly-overlay"},l.createElement("div",{onClick:e.onModalClose,className:"calendly-close-overlay"}),l.createElement("div",{className:"calendly-popup"},l.createElement("div",{className:"calendly-popup-content"},l.createElement(ye,y({},e)))),l.createElement("button",{className:"calendly-popup-close",onClick:e.onModalClose,"aria-label":"Close modal",style:{display:"block",border:"none",padding:0}})),e.rootElement)});(function(e){f(a,e);function a(r){var n=e.call(this,r)||this;return n.state={isOpen:!1},n.onClick=n.onClick.bind(n),n.onClose=n.onClose.bind(n),n}return a.prototype.onClick=function(r){r.preventDefault(),this.setState({isOpen:!0})},a.prototype.onClose=function(r){r.stopPropagation(),this.setState({isOpen:!1})},a.prototype.render=function(){return l.createElement(l.Fragment,null,l.createElement("button",{onClick:this.onClick,style:this.props.styles||{},className:this.props.className||""},this.props.text),l.createElement(W,y({},this.props,{open:this.state.isOpen,onModalClose:this.onClose,rootElement:this.props.rootElement})))},a})(l.Component);(function(e){f(a,e);function a(r){var n=e.call(this,r)||this;return n.state={isOpen:!1},n.onClick=n.onClick.bind(n),n.onClose=n.onClose.bind(n),n}return a.prototype.onClick=function(){this.setState({isOpen:!0})},a.prototype.onClose=function(r){r.stopPropagation(),this.setState({isOpen:!1})},a.prototype.render=function(){return l.createElement("div",{className:"calendly-badge-widget",onClick:this.onClick},l.createElement("div",{className:"calendly-badge-content",style:{background:this.props.color||"#00a2ff",color:this.props.textColor||"#ffffff"}},this.props.text||"Schedule time with me",this.props.branding&&l.createElement("span",null,"powered by Calendly")),l.createElement(W,y({},this.props,{open:this.state.isOpen,onModalClose:this.onClose,rootElement:this.props.rootElement})))},a})(l.Component);function fe({url:e,onBooked:a,prefillName:r,prefillEmail:n}){return l.useEffect(()=>{const s=d=>{var u,c,m,o;if(((u=d.data)==null?void 0:u.event)==="calendly.event_scheduled"){const p=(o=(m=(c=d.data)==null?void 0:c.payload)==null?void 0:m.event)==null?void 0:o.uri;if(p){const h=p.split("/"),i=h[h.length-1];a(i)}}};return window.addEventListener("message",s),()=>window.removeEventListener("message",s)},[a]),t.jsx("div",{className:"calendly-widget-container",children:t.jsx(ge,{url:e,styles:{minWidth:"100%",height:"630px"},prefill:{name:r,email:n}})})}const xe={en:{en:"English",ar:"الإنجليزية"},ar:{en:"Arabic",ar:"العربية"},fr:{en:"French",ar:"الفرنسية"},es:{en:"Spanish",ar:"الإسبانية"},de:{en:"German",ar:"الألمانية"},zh:{en:"Chinese",ar:"الصينية"},ja:{en:"Japanese",ar:"اليابانية"},ko:{en:"Korean",ar:"الكورية"},hi:{en:"Hindi",ar:"الهندية"},ur:{en:"Urdu",ar:"الأردية"},tr:{en:"Turkish",ar:"التركية"},pt:{en:"Portuguese",ar:"البرتغالية"},ru:{en:"Russian",ar:"الروسية"},it:{en:"Italian",ar:"الإيطالية"}},be=(e,a)=>{const r=xe[e.toLowerCase()];return r?a==="ar"?r.ar:r.en:e};function Ce({consultant:e,reviews:a}){var m,o,p,h;const{t:r}=Q("consultants"),{auth:n}=X().props,s=J.language,d=s==="ar"&&e.bio_ar?e.bio_ar:e.bio_en,u=((m=n.user)==null?void 0:m.role)==="client";n.user;const c=i=>{te.visit(`/bookings/${i}/pay`)};return t.jsxs(t.Fragment,{children:[t.jsx(K,{title:e.name}),t.jsx("div",{className:"bg-gray-50 min-h-screen",children:t.jsx("div",{className:"max-w-[1440px] mx-auto px-6 lg:px-12 py-10",children:t.jsxs("div",{className:"grid grid-cols-1 lg:grid-cols-12 gap-8",children:[t.jsxs("div",{className:"lg:col-span-7 space-y-8",children:[t.jsx("div",{className:"bg-white border border-gray-200 rounded-2xl p-8",children:t.jsxs("div",{className:"flex items-start gap-5",children:[e.avatar_url?t.jsx("img",{src:e.avatar_url,alt:e.name,className:"w-20 h-20 rounded-full object-cover ring-3 ring-primary/20 shrink-0"}):t.jsx("div",{className:"w-20 h-20 rounded-full bg-primary/10 ring-3 ring-primary/20 flex items-center justify-center text-primary font-bold text-2xl shrink-0",children:e.name.charAt(0)}),t.jsxs("div",{children:[t.jsx("h1",{className:"text-2xl font-bold text-gray-900",children:e.name}),t.jsxs("div",{className:"flex items-center gap-3 mt-2 flex-wrap",children:[t.jsxs("div",{className:"flex items-center gap-1",children:[t.jsx(D,{className:"w-4 h-4 text-secondary fill-secondary"}),t.jsx("span",{className:"text-sm font-semibold",children:e.average_rating}),t.jsxs("span",{className:"text-xs text-gray-400",children:["(",e.total_reviews,")"]})]}),t.jsx("span",{className:"text-gray-300",children:"|"}),t.jsxs("div",{className:"flex items-center gap-1 text-sm text-gray-500",children:[t.jsx(ae,{className:"w-3.5 h-3.5"}),e.years_experience," ",r("show.experience")]}),t.jsx("span",{className:"text-gray-300",children:"|"}),t.jsxs("div",{className:"flex items-center gap-1 text-sm text-gray-500",children:[t.jsx(Z,{className:"w-3.5 h-3.5"}),e.timezone]})]}),t.jsx("div",{className:"flex flex-wrap gap-1.5 mt-3",children:e.specializations.map(i=>t.jsx("span",{className:"px-2.5 py-1 bg-primary/5 text-primary text-xs font-medium rounded-full",children:s==="ar"&&i.name_ar?i.name_ar:i.name_en},i.id))})]})]})}),t.jsxs("div",{className:"bg-white border border-gray-200 rounded-2xl p-8",children:[t.jsx("h2",{className:"text-lg font-bold text-gray-900 mb-4",children:r("show.about")}),t.jsx("p",{className:"text-gray-600 leading-relaxed whitespace-pre-line",children:d}),e.languages&&e.languages.length>0&&t.jsx("div",{className:"mt-6 pt-6 border-t border-gray-100",children:t.jsxs("div",{className:"flex items-center gap-2 text-sm text-gray-500",children:[t.jsx(ee,{className:"w-4 h-4"}),t.jsxs("span",{className:"font-medium",children:[r("show.languages"),":"]}),e.languages.map(i=>be(i,s)).join(", ")]})}),t.jsxs("div",{className:"mt-4 flex items-center gap-2 text-sm text-gray-500",children:[t.jsx(ie,{className:"w-4 h-4"}),t.jsxs("span",{className:"font-medium",children:[r("show.responseTime"),":"]}),e.response_time_hours,"h"]})]}),t.jsxs("div",{className:"bg-white border border-gray-200 rounded-2xl p-8",children:[t.jsxs("h2",{className:"text-lg font-bold text-gray-900 mb-6",children:[r("show.reviews")," (",e.total_reviews,")"]}),a.length===0?t.jsx("p",{className:"text-gray-400 text-sm",children:r("show.noReviews")}):t.jsx("div",{className:"space-y-6",children:a.map(i=>t.jsxs("div",{className:"border-b border-gray-100 pb-5 last:border-0 last:pb-0",children:[t.jsxs("div",{className:"flex items-center justify-between mb-2",children:[t.jsxs("div",{className:"flex items-center gap-2",children:[t.jsx("span",{className:"font-semibold text-sm text-gray-900",children:i.reviewer_name}),t.jsx("div",{className:"flex items-center gap-0.5",children:Array.from({length:5}).map((x,g)=>t.jsx(D,{className:`w-3.5 h-3.5 ${g<i.rating?"text-secondary fill-secondary":"text-gray-200"}`},g))})]}),t.jsx("span",{className:"text-xs text-gray-400",children:new Date(i.created_at).toLocaleDateString()})]}),i.comment&&t.jsx("p",{className:"text-sm text-gray-600",children:i.comment})]},i.id))})]})]}),t.jsx("div",{className:"lg:col-span-5",children:t.jsx("div",{className:"sticky top-24",children:t.jsxs("div",{className:"bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden",children:[t.jsx("div",{className:"p-6 border-b border-gray-100",children:t.jsxs("div",{className:"flex items-center justify-between",children:[t.jsx("h3",{className:"font-bold text-gray-900",children:r("show.bookingCard")}),t.jsxs("div",{className:"text-end",children:[t.jsx("span",{className:"text-2xl font-bold text-secondary",children:e.hourly_rate}),t.jsx("span",{className:"text-sm text-gray-400",children:" SAR/hr"})]})]})}),t.jsx("div",{className:"p-6",children:u&&e.calendly_event_type_url?t.jsx(fe,{url:e.calendly_event_type_url,onBooked:c,prefillName:(o=n.user)==null?void 0:o.name,prefillEmail:(p=n.user)==null?void 0:p.email}):((h=n.user)==null?void 0:h.role)==="consultant"?t.jsxs("div",{className:"flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700",children:[t.jsx(le,{className:"w-5 h-5 shrink-0"}),r("show.consultantNotice")]}):t.jsxs("div",{className:"text-center py-8",children:[t.jsx("p",{className:"text-gray-500 mb-4",children:r("show.loginToBook")}),t.jsx(ne,{href:`/login?intended=/consultants/${e.slug}`,className:"inline-flex h-12 px-8 items-center justify-center bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors",children:"Login"})]})})]})})})]})})})]})}export{Ce as default};
