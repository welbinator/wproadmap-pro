(()=>{"use strict";const e=window.React,{registerBlockType:o}=wp.blocks,{InspectorControls:l,useBlockProps:t}=wp.blockEditor,{PanelBody:s,CheckboxControl:n}=wp.components,{useSelect:a}=wp.data,{__}=wp.i18n;o("roadmapwp-pro/display-ideas",{title:__("Display Ideas","roadmapwp-pro"),category:"common",attributes:{onlyLoggedInUsers:{type:"boolean",default:!1}},edit:({attributes:o,setAttributes:a})=>(console.log("onlyLoggedInUsers attribute value:",o.onlyLoggedInUsers),(0,e.createElement)("div",{...t()},(0,e.createElement)(l,null,(0,e.createElement)(s,{title:"Access Control"},(0,e.createElement)(n,{label:"Allow only logged in users to see this form?",checked:o.onlyLoggedInUsers,onChange:e=>{console.log("Checkbox onChange triggered. New Value:",e),a({onlyLoggedInUsers:e})}}))),(0,e.createElement)("p",null,__("Display Ideas | This block will display your published ideas","roadmapwp-pro")))),save:()=>null})})();