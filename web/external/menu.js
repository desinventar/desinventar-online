var disappeardelay=250; 
var enableanchorlink=0;
var hidemenu_onclick=1; 

var ie5=document.all ;
var ns6=document.getElementById&&!document.all ;

function getposOffset(what, offsettype) {
	var totaloffset=(offsettype=="left") ? what.offsetLeft : what.offsetTop;
	var parentEl=what.offsetParent;
	while (parentEl!=null) {
		totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;parentEl=parentEl.offsetParent;
	}
	return totaloffset; 
}

function showhide(obj, e, visible, hidden) {
	if (ie5||ns6) 
		dropmenuobj.style.left=dropmenuobj.style.top=-500;
	if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
		 obj.visibility=visible; else if (e.type=="click") obj.visibility=hidden;
}

function iecompattest() {
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge) {
	var edgeoffset=0;
	if (whichedge=="rightedge") {
		var windowedge=ie5 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15;
		dropmenuobj.contentmeasure=dropmenuobj.offsetWidth;
		if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure) 
			edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth; 
	}
	else {
		var topedge=ie5 && !window.opera? iecompattest().scrollTop : window.pageYOffset;
		var windowedge=ie5 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18;
		dropmenuobj.contentmeasure=dropmenuobj.offsetHeight;
		if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure) { 
			edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight;
			if ((dropmenuobj.y-topedge)<dropmenuobj.contentmeasure); 
				edgeoffset=dropmenuobj.y+obj.offsetHeight-topedge; 
		} 
	} 
	return edgeoffset 
}

function dropdownmenu(obj, e, dropmenuID) {
  if (window.event) {	
    event.cancelBubble=true	
  }	
  else if (e.stopPropagation) {	
    e.stopPropagation()	
  }
  if (typeof dropmenuobj!="undefined") {
    dropmenuobj.style.visibility="hidden";
  } 
  clearhidemenu();
  if (ie5||ns6) { 
    obj.onmouseout=delayhidemenu; 
    dropmenuobj=document.getElementById(dropmenuID);
    if (hidemenu_onclick) 
      dropmenuobj.onclick=function() { dropmenuobj.style.visibility='hidden' }
    dropmenuobj.onmouseover=clearhidemenu; 
    dropmenuobj.onmouseout=ie5 ? function() { dynamichide(event) } : function(event){ dynamichide(event)}
    showhide(dropmenuobj.style, e, "visible", "hidden"); 
    dropmenuobj.x=getposOffset(obj, "left");
    dropmenuobj.y=getposOffset(obj, "top");
    dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px";
    dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px";
  }
  return clickreturnvalue(); 
}
function clickreturnvalue() {
	if ((ie5||ns6) && !enableanchorlink) {
		return false;
	}	else {	return true; }
}
function contains_ns6(a, b) {	
	while (b.parentNode) {
		if ((b = b.parentNode) == a)	{	return true;	}	
	}
	return false;	
}
function dynamichide(e) {
	if (ie5&&!dropmenuobj.contains(e.toElement)) delayhidemenu(); 
	else 
		if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget)) 
			delayhidemenu(); 
}
function delayhidemenu() {
	delayhide=setTimeout("dropmenuobj.style.visibility='hidden'",disappeardelay); 
}
function clearhidemenu() {
	if (typeof delayhide != "undefined") { 
		clearTimeout(delayhide); 
	}
}
/* personalization List menu..
function displayList(elem) {
	lst = 7;
	for (i=1; i <= lst; i++) {
		if (i == elem)
			$("sect"+ i).style.display = 'block';
		else
			$("sect"+ i).style.display = 'none';
	}
}
*/
