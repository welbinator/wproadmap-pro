(()=>{"use strict";const e=window.React,{registerBlockType:t}=wp.blocks,{useSelect:s}=wp.data,{CheckboxControl:a,PanelBody:o}=wp.components,{InspectorControls:l}=wp.blockEditor;t("wp-roadmap-pro/roadmap-tabs-block",{title:"Roadmap Tabs Block",category:"common",attributes:{selectedStatuses:{type:"object",default:{}}},edit:function(t){const{attributes:c,setAttributes:n}=t,r=s((e=>e("core").getEntityRecords("taxonomy","status",{per_page:-1})),[]);return(0,e.createElement)("div",null,(0,e.createElement)(l,null,(0,e.createElement)(o,{title:"Select Statuses"},r&&r.map((t=>(0,e.createElement)(a,{label:t.name,checked:!!c.selectedStatuses[t.slug],onChange:e=>((e,t)=>{const s={...c.selectedStatuses,[e]:t};n({selectedStatuses:s})})(t.slug,e)}))))),(0,e.createElement)("p",null,"Roadmap Tabs Block Preview"))},save:function(){return null}})})();