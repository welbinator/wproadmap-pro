(()=>{"use strict";const e=window.wp.blocks,s=window.React,t=window.wp.blockEditor,l=window.wp.components,o=window.wp.i18n,i=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"display-ideas-block/display-ideas-block","version":"0.1.0","title":"Display Ideas","category":"roadmap","icon":"lightbulb","attributes":{"onlyLoggedInUsers":{"type":"boolean","default":false}},"description":"Displays ideas with filters","example":{},"supports":{"html":false},"textdomain":"display-ideas-block","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css","viewScript":"file:./view.js"}');(0,e.registerBlockType)(i,{Edit:({attributes:e,setAttributes:i})=>(0,s.createElement)("div",{...(0,t.useBlockProps)()},(0,s.createElement)(t.InspectorControls,null,(0,s.createElement)(l.PanelBody,{title:(0,o.__)("Access Control","roadmapwp-pro")},(0,s.createElement)(l.CheckboxControl,{label:(0,o.__)("Allow only logged in users to see this form?","roadmapwp-pro"),checked:e.onlyLoggedInUsers,onChange:e=>i({onlyLoggedInUsers:e})}))),(0,s.createElement)("p",null,(0,o.__)("Display Ideas | This block will display your published ideas","roadmapwp-pro"))),save:function(){const e=t.useBlockProps.save();return(0,s.createElement)("div",{...e},(0,s.createElement)("p",null,"This block displays Ideas with filters"))}})})();