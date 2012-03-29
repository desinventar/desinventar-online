
var ui_accordion = new Object();

ui_accordion["mouseout"] = function ()
{
	removeClass(this,"hover");
	return false;
}

ui_accordion["mousedown"] = function ()
{
	this.timer = setTimeout("void(0)",0);
	var k = 0;			
	var mb = this.mb;
	
	
	var dds = mb.parentNode.getElementsByTagName("dd");
	var dts = mb.parentNode.getElementsByTagName("dt");
	
	if (this.parentNode.getAttribute("standalone")!=null)
	{
		var do_expand = (mb.offsetHeight>1)?false:true;
		var action = function()
		{
			clearTimeout(this.timer);
			if (do_expand)
			{//do expand
				
				if (	mb.offsetHeight < (mb.scrollHeight-5) )
				{
					mb.style.height = Math.ceil( mb.offsetHeight +  (mb.scrollHeight-mb.offsetHeight)/7) + "px";
					this.timer = setTimeout(action,0);
				}
				else
				{
					mb.style.height="auto";
					mb.style.overflow ="visible";
					do_expand = null;
					action = null;
					mb = null;				
				}
			}
			else
			{//do collapse;

				if (parseInt(mb.style.height) > 3)
				{
					
					mb.style.height = Math.ceil(  parseInt(mb.style.height) +  (- parseInt(mb.style.height))/2) + "px";
					window.status =  mb.style.height + new Date();
					this.timer = setTimeout(action,0);
				}
				else
				{
					mb.style.height="0";
					mb.style.overflow ="hidden";
					mb.style.display = "none";
					do_expand = null;
					action = null;
					mb = null;				
				}
			}
		}

		if (do_expand)
		{//begin expand
			mb.style.height="1px";
			mb.style.overflow ="auto";
			mb.style.display="block";
		}
		else
		{//begin collapse;

			mb.style.height= mb.offsetHeight + "px";
			mb.style.overflow ="hidden";
			mb.style.display="block";
		}
		clearTimeout(this.timer);
		this.timer = setTimeout(action,0);
		return false;
	}
	
	
	var hasExpendedDD = false;
	for(var i=0;i<dds.length;i++)
	{
		dds[i].ddIndex = i;
		var closed,expand,collapse ;
		var collapsed = false;
		
		if (dds[i] == mb)
		{
			if (dts[mb.ddIndex])
			{
				addClass(dts[mb.ddIndex],"expanding");
			}
		
			var start = new Date();
			expand = function()
			{	
				clearTimeout(this.timer);
				if (mb.style.display.toLowerCase()!="block")
				{
					mb.style.display="block";
					mb.endHeight =  mb.scrollHeight;
					mb.style.overflow ="hidden";			
				}

				var end = new Date();

				if ((mb.offsetHeight < mb.endHeight) && ((end - start)<500))
				{
					mb.style.height = Math.ceil( mb.offsetHeight + (mb.endHeight- mb.offsetHeight)/33 ) + "px";
					this.timer = setTimeout(expand,0);				
				}
				else
				{	
					mb.style.height="auto";
					mb.style.overflow="auto";					
					if (dts[mb.ddIndex])
					{
						removeClass(dts[mb.ddIndex],"expanding");
					}
				}
				
			}
			
			
		}
		else if(dds[i].offsetHeight>0)
		{
			hasExpendedDD = true;
			collapsed = true;
			closed = dds[i];
			closed.style.height = closed.offsetHeight+"px";
			closed.style.overflow ="hidden";
			addClass(closed,"collapsing");
		
			if (dts[closed.ddIndex])
			{
				addClass(dts[closed.ddIndex],"collapsing");
			}
			var start = new Date();
			collapse = function()
			{
				
				clearTimeout(this.timer);
				var ph = parseInt(closed.style.height);
				var end = new Date();
				if ((ph > 2) && ((end - start)<200))
				{
					closed.style.height =Math.floor(ph  + (0-ph)/2)  + "px";
					this.timer = setTimeout(collapse,0);
				}
				else
				{	
					closed.style.height="0";
					closed.style.display="none";
					closed.style.overflow ="visible";
					if (dts[closed.ddIndex])
					{
						removeClass(dts[closed.ddIndex],"collapsing");
						removeClass(closed,"collapsing");
					}

					this.timer = setTimeout(expand,0);
				}
			}
			clearTimeout(this.timer);
			this.timer = setTimeout( collapse,0);
		}
		
	}
	if (collapsed==false)
	{
		if (dts[mb.ddIndex])
		{
			removeClass(dts[mb.ddIndex],"expanding");
		}
	}

	if (hasExpendedDD == false)
	{
		for(var i=0;i<dds.length;i++)
		{
			closed = dds[i];
			closed.style.height = 0;
			closed.style.overflow ="visible";
			removeClass(closed,"default_close");
			closed.style.display="none";	
			
			
		}
	
		this.timer = setTimeout(expand,0);
	}
	return false;
}

ui_accordion["mouseover"] = function (e,dt,doExpand)
{
	var el;
	if (dt)
	{
		el = dt;
	}
	else
	{
		e =  getEvent(e);
		if (e.target != undefined)
		{
			el =  getObjByClass(e.target,"dt","",2) ;
		}
	}

	if (el && el.parentNode && hasClass(el.parentNode,"accordion"))
	{
		var  def = getChildByClass(el.parentNode,"dd","default",1);

		if (!def)
		{
			if (el.parentNode.getElementsByTagName("dd").length>0)
			{
				var dd = el.parentNode.getElementsByTagName("dd")[0];
				addClass(dd ,"default");
				addClass(dd ,"default_close");
			}
			else
			{
				return;
			}
		}

		if (!doExpand)
		{
			addClass(el,"hover");
		}

		if (el.init==null)
		{
			var mb = getNextObjByTagName(el,"dd");
			if (mb==null)
			{
				return;
			}

			el.mb = mb;
			el.init = true;
			el.onmouseout =ui_accordion["mouseout"];
			el.onmousedown =ui_accordion["mousedown"];
			el.expand = ui_accordion["mousedown"];
		}
		
		if (doExpand)
		{
			el.expand();
		}
		if (e)
		{
			return stopEvent(e);
		}
	}
}

var init = addEvent(document,"mouseover",ui_accordion["mouseover"]);
if (init)
{
	document.write("<style media=\"screen\"> dl.accordion dd{height:0;display:none;}; </style>");
}

function accordion_menu_expand(accordion_header_id)
{
	var el  = document.getElementById(accordion_header_id);
	if (!el) {
		return false;
	}
	if (el.tagName.toLowerCase()!="dt")
	{
		return false;
	}
	ui_accordion["mouseover"](null,el,true);
}
