//-------------------------NO CHANGES BEYOND THIS LINE (FOR YOUR OWN SAFETY..)------------
var aimObj;
var clickedCat = false;
var clickedCatId = false;
var actualEpisode=false;
var activeEpisode;
var episodeOffset=0;
var latestid=false;
var clickedEpisode = false;
var heights = new Array();
var lastheight;
var totalheight=0;
var numberofepisodes=false;
var param=0;
var sliding=false;
var edge=true;
var slideSpeed=5;
var aimpos=0;
var actualpos=0;
var direction=false;
var scrollingContent;
var timed=false;
var timerID;
var startTime;
var obj;
var moving;
var gravimg = themedir+'/images/avatar.jpg';
var commenttimed = false;
var diggtext='';
var digghead='';

function sendata(form) {
	var valid=true;
	if((form.comauthor.value == '')&&(form.namemail_required.value)) {
		fadeIn(0,'author_error');
		valid = false;
	} 
	if (((form.email.value == '')||(form.email.value.indexOf("@") == -1)||(form.email.value.indexOf(".") == -1)||(form.email.value.length < 6))&&(form.namemail_required.value)) {
		fadeIn(0,'mail_error');
		valid = false;
	} 
	if (!valid) return false;
	
	clearTimeout(commenttimed);
	document.getElementById("submit").disabled = true;
	document.getElementById("submit").value = "Commenting..";
	
	try {
 		xmlhttp = window.XMLHttpRequest?new XMLHttpRequest():
	  	new ActiveXObject("Microsoft.XMLHTTP"); }
 	catch (e) {}
	commentdata='id='+form.comment_post_ID.value+'&auth='+encodeURIComponent(form.comauthor.value)+'&mail='+encodeURIComponent(form.email.value)+'&url='+encodeURIComponent(form.url.value)+'&content='+encodeURIComponent(form.comment.value);
	xmlhttp.onreadystatechange = ScreenRead; 
	xmlhttp.open("POST", themedir+'/comments-ajax.php?'+commentdata, true);
	xmlhttp.send(null);
	return false;
}
function ScreenRead() {
	if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
	document.getElementById('submit').value = "Post Comment";
	firstobj=document.getElementById("comment_preview");
	commentingobj=document.getElementById("commenting");
	strep=wpdetexturize(xmlhttp.responseText);
	softpoint=strep.indexOf("<c0>");
	midpoint=strep.indexOf("<c1>");
	highpoint=strep.indexOf("<c2>");
	hardpoint=strep.indexOf("<c3>");
	commentingobj.getElementsByTagName('H2')[0].innerHTML = strep.slice(softpoint+4,midpoint);	
	document.getElementById("Episode"+actualEpisode).getElementsByTagName('A')[0].innerHTML=strep.slice(midpoint+4,highpoint);		
	firstobj.getElementsByTagName('DIV')[1].innerHTML = strep.slice(highpoint+4,hardpoint);		
	document.getElementById("leave_comment").innerHTML = strep.slice(hardpoint+4);				
	firstobj.getElementsByTagName('DIV')[0].setAttribute("id","comment-"+strep.slice(0,softpoint));
	firstobj.getElementsByTagName('IMG')[0].removeAttribute("onload");
	firstobj.getElementsByTagName('IMG')[0].removeAttribute("class");
	firstobj.getElementsByTagName('IMG')[0].removeAttribute("id");
	firstobj.removeAttribute("id");
	var afterdiv = document.getElementById("leave_comment");
	var cleardiv = document.createElement("div");
	cleardiv.setAttribute("class","clear");
	commentingobj.insertBefore(cleardiv,afterdiv);
	var previewdiv=document.createElement("div");
	previewdiv.style.display= "none";
	previewdiv.setAttribute("id","comment_preview");
	previewdiv.innerHTML = "<div class=\"comment_author\"><img src=\""+themedir+"/images/avatar.jpg\"></div><div class=\"comment\"></div><div class=\"clear\"></div>"
	commentingobj.insertBefore(previewdiv,afterdiv);
	initLivePreview();
	}
}
function getdata(What) {
	try {
 		xmlhttp = window.XMLHttpRequest?new XMLHttpRequest():
	  	new ActiveXObject("Microsoft.XMLHTTP"); }
 	catch (e) {}
 
	xmlhttp.onreadystatechange = ScreenWrite; 
	xmlhttp.open("GET", What, true);
	xmlhttp.send(null);
	return false;
}
function ScreenWrite() { 
	if ((xmlhttp.readyState == 1)&&(aimObj == document.getElementById('list_container'))) {
			aimObj.innerHTML = '';
			setfade(0,'linkup');
			document.getElementById('linkdown').className="inactive";
	} 
	
	if ( (xmlhttp.readyState == 4) && (xmlhttp.status == 200) ) {
		if (aimObj==document.getElementById('mediacontent')) {
			strep=wpdetexturize(xmlhttp.responseText)
			
			from=strep.indexOf('plink:');
			plinker=strep.slice(from+6);
			strep=strep.slice(0,from);
			document.getElementById('linker').setAttribute("href",plinker);
			
			
			from=strep.indexOf("<cut>")+5;
			if (from<12)
				from=strep.indexOf("</object>")+9;
			if (from<12)
				from=strep.indexOf("</embed>")+8;
			if (from<12) 
				from=strep.indexOf("</h1>")+5;
			mediastr=strep.slice(0,from);
			jstring=false;
			if ((mediastr.indexOf("<script type")>0)&&(wdtube=='1')) {
				jfrom=mediastr.indexOf("<script type");
				jto=mediastr.indexOf("</script>")+9;
				jstring=mediastr.slice(jfrom+45,jto-9);
				mediastr=mediastr.slice(0,jfrom)+mediastr.slice(jto);
			}
			video='';
			if (mediastr.indexOf("[video:")>0){
				if (mpl>0){
					if (mediastr.indexOf("image:")>0){
						image = mediastr.slice(mediastr.indexOf("image:")+6,mediastr.indexOf(";",mediastr.indexOf("image:")));
						if (image.indexOf("]")>0) 
							image=image.slice(0,image.indexOf("]"));
					} else { image='false';}
					if (mediastr.indexOf("h:")>0){
						pheight = mediastr.slice(mediastr.indexOf("h:")+2,mediastr.indexOf(";",mediastr.indexOf("h:")));
						if (pheight.indexOf("]")>0) 
							pheight=pheight.slice(0,pheight.indexOf("]"));
					} else { pheight='330';}
					if (mediastr.indexOf("w:")>0){
						pwidth = mediastr.slice(mediastr.indexOf("w:")+2,mediastr.indexOf(";",mediastr.indexOf("w:")));
						if (pwidth.indexOf("]")>0) 
							pwidth=pwidth.slice(0,pwidth.indexOf("]"));
					} else { pwidth='440';}
					if (mediastr.indexOf(";auto")>0){
						pauto=true;
					} else {pauto=false;}
					video=mediastr.slice(mediastr.indexOf("video:")+6,mediastr.indexOf(";",mediastr.indexOf("video:")));
					if (video.indexOf("]")>0) 
						video=video.slice(0,video.indexOf("]"));
					mediastr=mediastr.slice(0,mediastr.indexOf("[video:"))+'<div id="theplayer"></div>';
				} else {
					mediastr=mediastr.slice(0,mediastr.indexOf("[video:"))+'<br /><br />Please check if the "mediaplayer.swf" file exists in the Video theme directory.<br /><br /> For further support please visit the <a href="http://www.quommunication.com/forum/viewforum.php?id=7" title="Quommunication Forum" target="_blank">Quommunication Forum</a>.'
				}
			} 
			if (diggbuttons=="1") {
				poststr='<h2>Now Watching</h2><p><span id="digg_span">'+digghead+plinker+diggtext+'</span>'+strep.slice(from)+'</p>';
			} else {
				poststr='<h2>Now Watching</h2><p>'+strep.slice(from)+'</p>';
			}
			aimObj.innerHTML = mediastr;
			if ((jstring)&&(wdtube=='1'))
				eval(jstring);
			if (video!='') {
				launchNsiteplayer()
			}
			document.getElementById('postcontent').innerHTML = poststr;
			GetComments();
		} else 
			aimObj.innerHTML = xmlhttp.responseText; 
		
	
		if (aimObj == document.getElementById('list_container')){		
			var episodes = document.getElementById('list_container').getElementsByTagName('LI');

			totalheight=0;
			numberofepisodes=0;
			for(var no=0;no<episodes.length;no++){
				if (episodes[no].id == 'next'){
					episodes[no].onclick = regenerateList;
				} else if (episodes[no].id == 'prev'){
					episodes[no].onclick = regenerateList;
				} else {
					episodes[no].onclick = selectEpisode;
				};
				episodes[no].onmouseover = enterEpisode;
				episodes[no].onmouseout = leaveEpisode; 								
				heights[no]=episodes[no].offsetHeight/1;
				lastheight=heights[no];
				totalheight+=heights[no];
				numberofepisodes=no;
				activeEpisode=episodes[no];
								
				if ((episodes[no].id.replace(/Episode/, "")==actualEpisode)||(episodes[no].id.replace(/Episode/, "")==latestid)) { 
					episodes[no].className="current totalfaded";
					clickedEpisode=episodes[no];
				}
				
				if (no<=numepisodeshown-1)	
					setTimeout("fadeIn(0,'"+episodes[no].id+"')",no*80);
				else fadeIn(100,episodes[no].id);
			}

			contheight=0;
			for(var k=0;((k<=numepisodeshown-1)&&(k!=numberofepisodes+1)&&(contheight+heights[k]<=maxlistheight));k++) 
				contheight+=heights[k];
			document.getElementById('list_container').style.height = contheight+'px';

			if (totalheight>contheight) {
				document.getElementById('linkup').className="totalfaded";
			} else document.getElementById('linkup').className="inactive totalfaded";
			setTimeout("fadeIn(0,'linkup')",k*120);

			document.getElementById('contentlist').style.top = '0px';
			edge=true;
			aimpos=0;
			aimObj='none';
			
			if (!actualEpisode) {
				aimObj=document.getElementById('mediacontent');
				actualEpisode=latestid;
				latestid=false;
				getdata(themedir + '/episode.php?id='+ actualEpisode);
			}
		}
		if (aimObj == document.getElementById('commenting'))
			initLivePreview();
	}
}
//---------------------------------SLIDING------------------------------------------------
function slideContent(containerId) {
	sliding=true;
	slideSpeed=15;
	if (Math.abs(aimpos-actualpos)<20) slideSpeed=4;
	if (Math.abs(aimpos-actualpos)<5) slideSpeed=1;
	if(direction == 'up') {
		actualpos = actualpos/1 - slideSpeed/1;
		if (actualpos<aimpos) {
			actualpos=aimpos;
			sliding=false;
		}
	}
	else if(direction == 'down') {
		actualpos = actualpos/1 + slideSpeed/1;
		if (actualpos>aimpos) {
			actualpos=aimpos;
			sliding=false;
		}
	}

	if(actualpos/1>=0) {
		scrollingContent.style.top = '0px';
		if (timed) clearTimeout(timed);
		sliding=false;
		return;
	}
		
	scrollingContent.style.top = actualpos/1 + 'px';	
	if (actualpos!=aimpos)
	timed = setTimeout('slideContent("' + containerId + '")',30);
	else sliding=false;
}
function initSlidingContent(containerId,dir) {
	scrollingContainer = document.getElementById(containerId);
	scrollingContent = scrollingContainer.getElementsByTagName('UL')[0];
	direction=dir;
	
	scrollingContainer.style.position = 'relative';
	scrollingContainer.style.overflow = 'hidden';
	scrollingContent.style.position = 'relative';

	actualpos = scrollingContent.style.top.replace(/[^\-0-9]/g,'')/1;
	
	if((direction == 'up')&&(aimpos/1+scrollingContent.offsetHeight/1>scrollingContainer.offsetHeight)){
		if (sliding) {				
			aimpos -= heights[param];
			param+=1;
		}
		else {
			endheight=0;
			for(var i=0;((endheight<=scrollingContainer.offsetHeight/1-actualpos/1)&&(i!=numberofepisodes+1));i++){
				endheight+=heights[i];
			}
			param=i;
			aimpos=(scrollingContainer.offsetHeight/1-endheight);
			if (edge) {
				document.getElementById('linkdown').className="";
				edge=false;
			}
		}
		if (timed) clearTimeout(timed);
	}

	if((direction == 'down')&&(aimpos/1<0)){
		if (sliding) {
			aimpos += heights[param];
			param-=1;
		}
		else {
			endheight=totalheight;
			for(var i=0;(endheight/1 >= Math.abs(actualpos)/1);i++){
				endheight-=heights[numberofepisodes-i];
			}
			param=numberofepisodes-i;
			aimpos += (Math.abs(aimpos)-endheight)/1;
			if (edge) {
				document.getElementById('linkup').className="";
				edge=false;
			}
		}
		if (timed) clearTimeout(timed);
	}
	
	if (actualpos!=aimpos) {
		slideContent(containerId);
		if (param-1==numberofepisodes) {
			document.getElementById('linkup').className="inactive";
			edge=true;
		}
		else if (param==-1) {
			document.getElementById('linkdown').className="inactive";
			edge=true;
		}
	}
}
//---------------------------------CATEGORY PROCESSING------------------------------------
function selectCat() {
	if (document.getElementById('show_tags'))
		document.getElementById('show_tags').style.display="none";
	aimObj = document.getElementById('list_container');
	getdata(themedir + '/list.php?id='+ this.id.replace(/cat/,""));
	if(clickedCat && clickedCat!=this) 
		clickedCat.className=''; 
	this.className='current';
	clickedCat = this;
	cat = this.id.replace(/cat/,"");
	episodeOffset=0;
	tagged='';
}
function pulldownCat(cv) {
	if (document.getElementById('show_tags'))
		document.getElementById('show_tags').style.display="none";
	aimObj = document.getElementById('list_container');
	getdata(themedir + '/list.php?id='+ cv.replace(/cat/,""));
	cat = cv.replace(/cat/,"");
	episodeOffset = 0;
	tagged='';
}
//---------------------------------EPISODE PROCESSING-------------------------------------
function selectEpisode() {
	aimObj=document.getElementById('mediacontent');
	actualEpisode=this.id.replace(/Episode/,"");
	getdata(themedir + '/episode.php?id='+ actualEpisode);
	if(clickedEpisode && clickedEpisode!=this) 
		clickedEpisode.className=""; 
	this.className="current";
	clickedEpisode = this;
}
function regenerateList() {
	aimObj = document.getElementById('list_container');
	if (this.id == 'next') {
		episodeOffset += 1;
	} else {
		episodeOffset -= 1;
	}
	if (tagged=='') {
		getdata(themedir + '/list.php?id='+cat+'&pos='+episodeOffset);
	} else {
		getdata(themedir + '/list.php?tag='+tagged+'&pos='+episodeOffset);
	}
}
function enterEpisode(e) {
	if (!e) var e = window.event;
	var IntoE = e.srcElement || e.target;
	var FromE = e.relatedTarget || e.fromElement;
	if (FromE.nodeName == '#text') FromE = FromE.parentNode;
	if (FromE.nodeName == 'DIV'){
		activeEpisode = this;
		shifter('right', activeEpisode.id, -1);
	} else if (( FromE.nodeName == IntoE.nodeName)||((FromE.nodeName =='A') && (IntoE.nodeName=='LI'))||((FromE.nodeName =='LI') && (IntoE.nodeName=='A'))||((FromE.nodeName =='UL') && (IntoE.nodeName=='A'))||((FromE.nodeName =='UL') && (IntoE.nodeName=='B'))||((FromE.nodeName =='BODY') && (IntoE.nodeName=='B'))) {
		activeEpisode = this;
		shifter('right', activeEpisode.id, -1);
	}
	
	if (e.stopPropagation) {e.stopPropagation()}
	else {window.event.cancelBubble = true};
}
function leaveEpisode(e) {
	if (!e) var e = window.event;
	var FromE =  e.srcElement || e.target;
	var IntoE = e.relatedTarget || e.toElement;
	if (IntoE.nodeName == 'DIV'){
		shifter('left', activeEpisode.id, 4);
	} else if (( IntoE.nodeName == FromE.nodeName)||((IntoE.nodeName =='LI') && (FromE.nodeName=='A'))||((IntoE.nodeName =='A') && (FromE.nodeName=='LI'))||((IntoE.nodeName =='UL') && (FromE.nodeName=='B'))||((IntoE.nodeName =='UL') && (FromE.nodeName=='A'))||((IntoE.nodeName =='B') && (FromE.nodeName=='BODY'))) {
		shifter('left', activeEpisode.id, 4);
	}
	if (e.stopPropagation) {e.stopPropagation()}
	else {window.event.cancelBubble = true};
}
function shifter(dir,id,delta) {
	document.getElementById(id).getElementsByTagName('A')[0].style.paddingLeft = 10+delta/1+"px";
	document.getElementById(id).getElementsByTagName('A')[0].style.paddingRight = 10-delta+"px";
	if (dir=='right') 
		delta++
	else delta--;
	if ((delta<5)&&(delta>-1))
	setTimeout ("shifter('"+dir+"','"+id+"','"+delta+"')",18);
}
//---------------------------------INITIALIZE-SINGLEPAGE----------------------------------
function initpost(title) {
	posttext = document.getElementById('hidden_content').innerHTML;
	posttext = posttext.slice(0,posttext.indexOf("<cut>"));
	document.getElementById('mediacontent').innerHTML= "<h1>"+title+"</h1>"+posttext;
}
function initList (id, offset) {
	var episodes = document.getElementById('list_container').getElementsByTagName('LI');

	totalheight=0;
	numberofepisodes=0;
	episodeOffset = offset;
	for(var no=0;no<episodes.length;no++){
		if (episodes[no].id == 'next'){
			episodes[no].onclick = regenerateList;
		} else if (episodes[no].id == 'prev'){
			episodes[no].onclick = regenerateList;
		} else {
			episodes[no].onclick = selectEpisode;
		};
		episodes[no].onmouseover = enterEpisode;
		episodes[no].onmouseout = leaveEpisode; 								
		heights[no]=episodes[no].offsetHeight/1;
		lastheight=heights[no];
		totalheight+=heights[no];
		numberofepisodes=no;
		activeEpisode=episodes[no];
	
		if (param == 0)
			setTimeout("fadeIn(0,'"+episodes[no].id+"')",no*80);
		else fadeIn(100,episodes[no].id);
	
		if (episodes[no].id.replace(/Episode/, "")==id)  {
			actualEpisodeheight = totalheight;
			param = no;
		} else {
			actualEpisodeheight = 0;
		}
	}
	contheight=0;
	for(var k=0;((k<=numepisodeshown-1)&&(k!=numberofepisodes+1)&&(contheight+heights[k]<=maxlistheight));k++) 
		contheight+=heights[k];
	document.getElementById('list_container').style.height = contheight+'px';
	
	if (numberofepisodes>numepisodeshown-1) {
		document.getElementById('linkup').className="totalfaded";
	} else document.getElementById('linkup').className="inactive totalfaded";
	setTimeout("fadeIn(0,'linkup')",k*120);

	if (actualEpisodeheight > contheight) {
		scrollingContainer = document.getElementById('list_container');
		scrollingContent = scrollingContainer.getElementsByTagName('UL')[0];
		scrollingContainer.style.position = 'relative';
		scrollingContainer.style.overflow = 'hidden';
		scrollingContent.style.position = 'relative';
		document.getElementById('linkdown').className="";
		aimpos= contheight-actualEpisodeheight;
		direction = 'up';
		if (numberofepisodes == param) {
			document.getElementById('linkup').className="inactive";
		} else {
			edge = false;
		}
		slideContent('list_container');
	} else {
		document.getElementById('contentlist').style.top = '0px';
		edge=true;
		aimpos=0;	
	}
	
	aimObj='none';
	actualEpisode=id;
	clickedEpisode = document.getElementById('Episode'+id);

	if (dropmenu == "0"){
		var categs = document.getElementById('menu').getElementsByTagName('LI');
		for(var no=0;no<categs.length;no++) {
			categs[no].onclick = selectCat;
			if (categs[no].className=='current') {
				clickedCat = categs[no];
				cat = categs[no].id.replace(/cat/,"");
			}
		}
	}
}
function PrepareDigg() {
	diggtext = document.getElementById('digg_span').innerHTML;
	diggtext=diggtext.toLowerCase();
	diggtext = diggtext.slice(diggtext.indexOf("<iframe"));
	digghead = diggtext.slice(0,diggtext.indexOf("?u=")+3);
	diggtext = diggtext.slice(diggtext.indexOf("\"",diggtext.indexOf("?u=")+3));
}
function prepareNsiteplayer(mediastr) {
	if (mediastr.indexOf("image:")>0){
		image = mediastr.slice(mediastr.indexOf("image:")+6,mediastr.indexOf(";",mediastr.indexOf("image:")));
		if (image.indexOf("]")>0) 
			image=image.slice(0,image.indexOf("]"));
	} else { image='false';}
	if (mediastr.indexOf("h:")>0){
		pheight = mediastr.slice(mediastr.indexOf("h:")+2,mediastr.indexOf(";",mediastr.indexOf("h:")));
		if (pheight.indexOf("]")>0) 
			pheight=pheight.slice(0,pheight.indexOf("]"));
	} else { pheight='330';}
	if (mediastr.indexOf("w:")>0){
		pwidth = mediastr.slice(mediastr.indexOf("w:")+2,mediastr.indexOf(";",mediastr.indexOf("w:")));
		if (pwidth.indexOf("]")>0) 
			pwidth=pwidth.slice(0,pwidth.indexOf("]"));
	} else { pwidth='440';}
	if (mediastr.indexOf(";auto")>0){
			pauto=true;
	} else {pauto=false;}
	video=mediastr.slice(mediastr.indexOf("video:")+6,mediastr.indexOf(";",mediastr.indexOf("video:")));
	if (video.indexOf("]")>0) 
		video=video.slice(0,video.indexOf("]"));
}
function launchNsiteplayer() {
	video=video.toLowerCase();
	if (video.indexOf(".wmv")>0) {
		var cnt = document.getElementById("theplayer");
		var src = themedir+'/scripts/wmvplayer.xaml';
		if (pauto) pauto='true';
		var cfg = {
    		file:video,
			height:pheight,
			width:pwidth,
			autostart:pauto
			};
		var ply = new jeroenwijering.Player(cnt,src,cfg);
	} else {
		var so = new SWFObject(themedir+"/mediaplayer.swf", "Themovie", pwidth, pheight, "8", "#336699");
		so.addParam('allowscriptaccess','sameDomain');
		so.addParam('allowfullscreen','true');
		so.addVariable('width',pwidth);
		so.addVariable('height',pheight);
		so.addVariable('file',video);
		if (image) so.addVariable('image',image);
		so.addVariable('autostart',pauto);
		so.addVariable('wmode','transparent');
		so.addVariable('lightcolor','0xcccccc');
		so.write("theplayer");
	}
}
//---------------------------------FADE -------------------------------------------------
function fadeIn(opacity, objid) {
	o = document.getElementById(objid);
		if (opacity <= 100){
			if (o.style.filter != null){
				o.style.filter = "alpha(opacity="+opacity+")";
			} else if (o.style.opacity != null){
				o.style.opacity = (opacity/100);
			} else if (o.style.MozOpacity != null){
				o.style.MozOpacity = (opacity/100)-.001;
			}
			opacity += 10;
			window.setTimeout("fadeIn("+opacity+", '"+objid+"')", 35);
		}
}
function setfade(opacity, objid) {
	o = document.getElementById(objid);
	if (o.style.filter != null){
		o.style.filter = "alpha(opacity="+opacity+")";
	} else if (o.style.opacity != null){
		o.style.opacity = (opacity/100);
	} else if (o.style.MozOpacity != null){
		o.style.MozOpacity = (opacity/100)-.001;
	}
}
//---------------------------------LIVE COMMENT PREVIEW-----------------------------------
function slidedown(objname) {
	if(moving)
		return;
	if(document.getElementById(objname).style.display != "none") 
		return;
	moving = true;
	obj = document.getElementById(objname);
	startTime = (new Date()).getTime();
	obj.style.height = "1px";
	obj.style.display = "block";
	timerID = setInterval('slidetick(\'' + objname + '\');',5);
}
function slideup(objname) {
	if(moving)
		return;
	moving = true;
	if (commenttimed) clearTimeout(commenttimed);
	obj = document.getElementById(objname);
	startTime = (new Date()).getTime();
	timerID = setInterval('slidetack(\'' + objname + '\');',5);
}
function slidetick(objname) {
	var elapsed = (new Date()).getTime() - startTime;
	if (elapsed > 500){
		clearInterval(timerID);
		obj.style.height = "";
		moving=false;
		timerID=false;
		obj=false;
		StartTime=false;
		return;
	}
	else {
		var d =Math.round(elapsed / 500 * 90);
		obj.style.height = d + "px";
	}
	return;
}
function slidetack(objname) {
	var elapsed = (new Date()).getTime() - startTime;
	if (elapsed > 500){
		clearInterval(timerID);
		obj.style.height = "0px";
		obj.style.display = "none";
		moving=false;
		timerID=false;
		obj=false;
		StartTime=false;
		return;
	}
	else {
		var d =Math.round((500-elapsed) / 500 * 90);
		obj.style.height = d + "px";
	}
	return;
}
function wptexturize(text) {
// DISABLED - to avoid infinite loop, memory-filling, bug when typing "<<"
/*
	text = ' '+text+' ';
	var next 	= true;
	var output 	= '';
	var prev 	= 0;
	var length 	= text.length;
	var test = text;
	var closeFound = false;
	while ( prev < length  && next==true) {
		var index = text.indexOf('<', prev);
		if ( index > -1 ) {
			if ( index == prev ) {
				index = text.indexOf('>', prev);
				if( index != -1 ) {
					closeFound = true;
				}
			}
			index++;
		} else {
			index = length;
		}
		var s = text.substring(prev, index);
		prev = index;
		if ( s.substr(0,1) != '<' && next == true ) {
			s = s.replace(/---/g, '&#8212;');
			s = s.replace(/--/g, '&#8211;');
			s = s.replace(/\.{3}/g, '&#8230;');
			s = s.replace(/``/g, '&#8220;');
			s = s.replace(/'s/g, '&#8217;s');
			s = s.replace(/'(\d\d(?:&#8217;|')?s)/g, '&#8217;$1');
			s = s.replace(/([\s"])'/g, '$1&#8216;');
			s = s.replace(/(\d+)"/g, '$1&Prime;');
			s = s.replace(/(\d+)'/g, '$1&prime;');
			s = s.replace(/([^\s])'([^'\s])/g, '$1&#8217;$2');
			s = s.replace(/(\s)"([^\s])/g, '$1&#8220;$2');
			s = s.replace(/"(\s)/g, '&#8221;$1');
			s = s.replace(/'(\s|.)/g, '&#8217;$1');
			s = s.replace(/\(tm\)/ig, '&#8482;');
			s = s.replace(/\(c\)/ig, '&#169;');
			s = s.replace(/\(r\)/ig, '&#174;');
			s = s.replace(/''/g, '&#8221;');
			s = s.replace(/(\d+)x(\d+)/g, '$1&#215;$2');
		} else if ( s.substr(0,5) == '<code' ) {
			next = false;
		} else {
			next = true;
		}
		output += s; 
	}
	return output.substr(1, output.length-2);*/ 
	return text;
}
function wpautop(p) {
	p = p + '\n\n';
	p = p.replace(/(<blockquote[^>]*>)/g, '\n$1');
	p = p.replace(/(<\/blockquote[^>]*>)/g, '$1\n');
	p = p.replace(/\r\n/g, '\n');
	p = p.replace(/\r/g, '\n');
	p = p.replace(/\n\n+/g, '\n\n');
	p = p.replace(/\n?(.+?)(?:\n\s*\n)/g, '<p>$1</p>');
	p = p.replace(/<p>\s*?<\/p>/g, '');
	p = p.replace(/<p>\s*(<\/?blockquote[^>]*>)\s*<\/p>/g, '$1');
	p = p.replace(/<p><blockquote([^>]*)>/ig, '<blockquote$1><p>');
	p = p.replace(/<\/blockquote><\/p>/ig, '<p></blockquote>');	
	p = p.replace(/<p>\s*<blockquote([^>]*)>/ig, '<blockquote$1>');
	p = p.replace(/<\/blockquote>\s*<\/p>/ig, '</blockquote>');	
	p = p.replace(/\s*\n\s*/g, '<br />');
	return p;
}
function updateLivePreview() {
	var cmnt = wpautop(wptexturize(document.getElementById('comment').value));
	var pnme = document.getElementById('comauthor').value;
	var purl = document.getElementById('url').value;
	var eml = document.getElementById('email').value;
		
	if(!purl && !pnme && !eml && cmnt=="<br />") {
		slideup('comment_preview');
		document.getElementById("submit").disabled = true;
		return;
	}

	if (pnme) 
		setfade(0,"author_error");
	if ((eml.indexOf("@") != -1)&&(eml.indexOf(".") != -1)&&(eml.length > 5))
		setfade(0,"mail_error");
	if (cmnt) document.getElementById('submit').disabled = false;
	if (cmnt == "<br />") document.getElementById('submit').disabled = true;

	if(purl && pnme) {
		var name = '<b><a href="' + purl + '">' + pnme + '</a></b>';
	} else if(!purl && pnme) {
		var name = '<b>' + pnme + '</b>';
	} else if(purl && !pnme) {
		var name = '<b><a href="' + purl + '">You</a> say</b>';
	} else {
		var name = "<b>You say</b>";
	}
	
	var wd = ["January","February","March","April","May","June","July","August","September","October","November","December"]
	
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var y=today.getFullYear();
	var M=wd[today.getMonth()];
	var d=today.getDate();
		
	if (m<10)
		m="0" + m;
	
	commenttimed=setTimeout('updateLivePreview()',60000);	// clear, a post butonon!!!!
	
	var previewFormat =name+"<span class=\"meta\"> on " + M + " " + d + ", " + y + " at " + h + ":" + m + "</span><br />" + cmnt; 
	
	document.getElementById('comment_preview').getElementsByTagName('DIV')[1].innerHTML = previewFormat;
	slidedown('comment_preview');
}
function updateLiveGravatar() {
	var cmnt = wpautop(wptexturize(document.getElementById('comment').value));
	var pnme = document.getElementById('comauthor').value;
	var purl = document.getElementById('url').value;
	var eml = document.getElementById('email').value;
	if(!purl && !pnme && !eml && cmnt=="<br />") {
		slideup('comment_preview');
		return;
	}
	if (eml != '') {
		gravimg = 'http://www.gravatar.com/avatar.php?gravatar_id='+ hex_md5(eml)+'?s=75&r=any&default='+themedir+'/images/avatar.jpg';
	} else {
		gravimg = themedir+'/images/avatar.jpg';
	}
	var previewFormat = "<img id=\"avatar\"src="+gravimg+" class=\"totalfaded\" onload=\"javascript: fadeIn(0,'avatar')\"/>";
	var previewFormat2 = "<img id=\"avatar2\"src="+gravimg+" class=\"totalfaded\" onload=\"javascript: fadeIn(0,'avatar2')\"/>"; 
	document.getElementById('comment_preview').getElementsByTagName('DIV')[0].innerHTML = previewFormat;
	document.getElementById('leave_comment').getElementsByTagName('DIV')[0].innerHTML = previewFormat2;
	slidedown('comment_preview');
}
function initLivePreview() {
	if (document.getElementById('comment_form'))
		document.getElementById('comment_form').onkeyup = updateLivePreview;
	if (document.getElementById('email')) 
		document.getElementById('email').onblur = updateLiveGravatar;
}
//---------------------------------COMMENTING----------------------------------------------
function GetComments() {
	aimObj = document.getElementById('commenting');
	getdata(themedir+'/comment.php?id='+actualEpisode); 
}	
function wpdetexturize(p) {
	p=p.replace(/&lt;/g, '<');
	p=p.replace(/&#8221;/g, '"');
	p=p.replace(/&#8243;/g, '"');
	p=p.replace(/&gt;/g, '>');
	return p;
}
