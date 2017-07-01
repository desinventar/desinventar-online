function error_handler(a,b,c)
{
 window.status = (a +"\n" + b + "\n\n" + c + "\n\n" + error_handler.caller);
 return true;
}
// 2017-07-01 Disable this custom error handler, use the default
//window.onerror = error_handler;
String.prototype.trim=function(){return this.replace(/(^\s+)|\s+$/g,"");}


self.WD = {
/*begin of functions==============================*/
browser:new Object(),
dull:function (){},


addEvent:function(obj, evType, fn)
{ 
	if (obj.addEventListener)
	{  
		obj.addEventListener(evType, fn, false);  return true; 
	}
	else if (obj.attachEvent)
	{   
		var r = obj.attachEvent("on"+evType, fn); 	
		WD.EventCache.add(obj, evType, fn);
		return r;
	}
	else 
	{
		//Mac IE5 sucks here
		return false;
	} 
},

removeEvent:function (obj, evType, fn)
{ 
	if (obj.removeEventListener){  obj.removeEventListener(evType, fn, false);  return true; }
	else if (obj.detachEvent){   var r = obj.detachEvent("on"+evType, fn);    return r; }
	else { return false; } 
},

getObjById:function(id)
{
	return document.getElementById(id);
},


getChildByClass:function(el,tagName,className)
{
	var els = el.getElementsByTagName(tagName);
	className = className.split(" ");
	for( var i=0;i<els.length;i++)
	{
		for(var c=0;c<className.length;c++)
		{
			if( els[i].className.trim() == className[c].trim() ){ return els[i] ;}
		}		
	}
	return null;
},




getChildByName:function(el,tagName,Name)
{
	var els = el.getElementsByTagName(tagName);
	className = className.split(" ");
	for( var i=0;i<els.length;i++)
	{
		if( els[i].Name == Name){ return els[i] ;}
	}
	return null;
},


getNextObjByTagName:function(el,tagName)
{
	while(el && el.nextSibling)
	{
		el = el.nextSibling;
		if(el.nodeName.toLowerCase()==tagName.toLowerCase()){return el;}
	}		
	return null;
},


getNextPreviousByTagName:function(el,tagName)
{
	while(el && el.previousSibling)
	{
		el = el.previousSibling;
		if(el.nodeName.toLowerCase()==tagName.toLowerCase()){return el;}
	}		
	return null;
},



getObjByClass:function (el,tagName,className,level)
{
	level = level || 1000;
	var p= 0;
	var exit = function(el){ return ( (p>level) || (el==null) || (el.parentNode==null) || (el.tagName==null) || (el.className==null) || (el==document.body) );}
		
	while( !exit(el)  )
	{			
			if( (el.tagName.toLowerCase() == tagName) )
			{
				var c = el.className.split(" ");
				for(var i=0;i<c.length;i++)
				{
					if( (c[i]==className) || (className==""))
					{
						return el;
					}
				}
				
			}
			el = el.parentNode;
			p++;
	}
	return null;
},



getObjByName:function (el,tagName,Name,level)
{
	level = level || 1000;
	var p= 0;
	var exit = function(el){ return ( (p>level) || (el==null) || (el.parentNode==null) || (el.tagName==null) || (el.name==null) || (el==document.body) );}
		
	while( !exit(el)  )
	{			
			if( (el.tagName.toLowerCase() == tagName) && (el.name == Name) )
			{;return el;};			
			el = el.parentNode;
			p++;
	}
	return null;
},





getDimension:function(el)
{
 var d = new Object();
 if(el.getBoundingClientRect)
	{       
	   d.x = el.getBoundingClientRect().left + Math.max(document.body.scrollLeft, document.documentElement.scrollLeft);
	   d.y = el.getBoundingClientRect().top + Math.max(document.body.scrollTop, document.documentElement.scrollTop);
	   d.w = el.getBoundingClientRect().right - el.getBoundingClientRect().left;
	   d.h =  el.getBoundingClientRect().bottom - el.getBoundingClientRect().top;
	}
	else if(document.getBoxObjectFor)
	{
       d.x = document.getBoxObjectFor(el).x;
	   d.y =  document.getBoxObjectFor(el).y;
	   d.w = document.getBoxObjectFor(el).width;
	   d.h = document.getBoxObjectFor(el).height;
	}
	else
	{
			
			function offsetBy(el, type)
			{
			  if (this===el) return 0;
			  var v=999, owner=this, border='client'+type;
			  type = 'offset'+type;
			  do { v += owner[type];  } while ((owner=owner.offsetParent) && owner!==el && (v+=owner[border]))
			  return v-999;
			}
			
			d.x = offsetBy.call(el, null, 'Left');
			d.y= offsetBy.call(el, null, 'Top');
			d.w = el.offsetWidth;
			d.h = el.offsetHeight;
	
	}
	return d;
},

isTagName:function (el,tagName)
{
 return (el.nodeName.toLowerCase() == tagName.toLowerCase() );
},

hasClass:function (el,className)
{
 var c = el.className.split(" ");
 for(var i=0;i<c.length;i++)
	{
		if(c[i] == className){return true;};
	}
	return false;
},



getEvent:function (e)
{
	e = window.event ||e;
	e.leftButton=false;
	
	if(e.srcElement==null && e.target!=null)
	{	
		e.srcElement = e.target ;
		e.leftButton = ( e.button==1);		
	}
	else if(e.target==null && e.srcElement!=null)
	{ 
		e.target = e.srcElement;
		e.leftButton = ( e.button==0);
	}
	else if(e.srcElement!=null && e.target!=null)
	{
		//opera sucks and have both e.srcElement & e.target.
	}
	else{return null}

	var scrollLeft = 0;
	var scrollTop  = 0;
	if (document.compatMode && document.compatMode != "BackCompat")
	{
		scrollLeft = document.documentElement.scrollLeft;
		scrollTop  = document.documentElement.scrollTop;
	}
	else
	{
		scrollLeft = document.body.scrollLeft;
		scrollTop  = document.body.scrollTop;
	}
	e.mouseX = e.pageX || (e.clientX + scrollLeft);
	e.mouseY = e.pageY || (e.clientY + scrollTop);
	return e;
},



stopEvent:function(e)
{
	if(e && e.cancelBubble!=null)
	{
		e.cancelBubble = true;
		e.returnValue = false;
	}
	if(e && e.stopPropagation && e.preventDefault)
	{
		e.stopPropagation(); 
		e.preventDefault(); 
	}
	return false;
},


addClass:function(el,className)
{
	var c = el.className.split(" ");
	for(var i=0;i<c.length;i++)
	{
		if(c[i]==className){return;};
	}
	if(c.length>0)
	{
		el.className = (el.className + " " +className).trim();
	}
	else
	{
		el.className = className.trim();
	}
},


removeClass:function(el,className)
{
	var c = el.className.split(" ");
	for(var i=0;i<c.length;i++)
	{
		if(c[i]==className){c[i]="";};
	}
	el.className = c.join(" ").trim();
	
}


/*endof functions==============================*/
}

WD.EventCache = function()
{
	var listEvents = [];
	
	return {
		listEvents : listEvents,
	
		add : function(node, sEventName, fHandler, bCapture){listEvents[listEvents.length] = arguments;},
		
		flush : function(){
			var i, item;
			for(i = listEvents.length - 1; i >= 0; i = i - 1)
			{
				item = listEvents[i];				
				if(item[0].removeEventListener){item[0].removeEventListener(item[1], item[2], item[3]);};			
				if(item[1].substring(0, 2) != "on"){	item[1] = "on" + item[1];};				
				if(item[0].detachEvent){item[0].detachEvent(item[1], item[2]);};				
				item[0][item[1]] = null;
			};
		}
	};
}();


WD.addEvent(window,"unload",WD.EventCache.flush);
WD.browser["ie"] =  (document.all!=null)  && (window.opera==null); 
WD.browser["ie4"]  =  WD.browser["ie"] && (document.getElementById==null); 
WD.browser["ie5"]  =   WD.browser["ie"] && (document.namespaces==null) && (!WD.browser["ie4"]) ; 
WD.browser["ie6"]  =  WD.browser["ie"] && (document.implementation!=null) && (document.implementation.hasFeature!=null) 
WD.browser["ie55"]  =  WD.browser["ie"] && (document.namespaces!=null) && (!WD.browser["ie6"]); 
WD.browser["ns4"]  = !WD.browser["ie"] &&  (document.layers !=null) &&  (window.confirm !=null) && (document.createElement ==null); 
WD.browser["opera"] =  (self.opera!=null); 
WD.browser["gecko"] =  (document.getBoxObjectFor!=null); 
WD.browser["khtml"] = (navigator.vendor =="KDE"); 
WD.browser["konq"] =  ((navigator.vendor == 'KDE')||(document.childNodes)&&(!document.all)&&(!navigator.taintEnabled)); 
WD.browser["safari"] = (document.childNodes)&&(!document.all)&&(!navigator.taintEnabled)&&(!navigator.accentColorName); 
WD.browser["safari1.2"] = (parseInt(0).toFixed==null) && (WD.browser["safari"] && (window.XMLHttpRequest!=null)); 
WD.browser["safari2.0"] = (parseInt(0).toFixed!=null) && WD.browser["safari"] && !WD.browser["safari1.2"] ;
WD.browser["safari1.1"] = WD.browser["safari"] && !WD.browser["safari1.2"]  &&!WD.browser["safari2.0"] ;

for(i in self.WD)
{
 if(self[i]==null)
	{
		self[i] = self.WD[i];//synchronize for faster deelopement
	}
}

