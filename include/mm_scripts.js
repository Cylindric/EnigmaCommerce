function MM_checkBrowser(NSvers,NSpass,NSnoPass,IEvers,IEpass,IEnoPass,OBpass,URL,altURL) { //v4.0
	var newURL='', verStr=navigator.appVersion, app=navigator.appName, version = parseFloat(verStr);

	if (app.indexOf('Netscape') != -1) {
		if (version >= NSvers) {if (NSpass>0) newURL=(NSpass==1)?URL:altURL;}
	else {
		if (NSnoPass>0) newURL=(NSnoPass==1)?URL:altURL;}
	}
	else if (app.indexOf('Microsoft') != -1) {
		if (version >= IEvers || verStr.indexOf(IEvers) != -1) {
			if (IEpass>0) newURL=(IEpass==1)?URL:altURL;
		}
		else {
			if (IEnoPass>0) newURL=(IEnoPass==1)?URL:altURL;
		}
	}
	else if (OBpass>0) newURL=(OBpass==1)?URL:altURL;
	if (newURL) {
		window.location=unescape(newURL); document.MM_returnValue=false;
	}
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_changeProp(objName,x,theProp,theValue) { //v6.0
  var obj = MM_findObj(objName);
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)){
    if (theValue == true || theValue == false)
      eval("obj."+theProp+"="+theValue);
    else eval("obj."+theProp+"='"+theValue+"'");
  }
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function OldsetFocus() {
  if (document.forms.length > 0) {
    var el, type, i = 0, j, els = document.forms[0].elements;
    while (el = els[i++]) {
      j = 0;
      while (type = arguments[j++])
      if (el.type == type) return el.focus();
    }
  }
}
function SetFocus(formName, fieldName) {
	eval("document."+formName+"."+fieldName+".focus()");
}


var interval = 1500;
var random_display = 0;
var imageNum = 0;
imageArray = new Array();
imageArray[imageNum++] = new imageItem(imageDir + "000770.png");
imageArray[imageNum++] = new imageItem(imageDir + "000771.png");
imageArray[imageNum++] = new imageItem(imageDir + "000772.png");
imageArray[imageNum++] = new imageItem(imageDir + "000773.png");
var totalImages = imageArray.length;

function imageItem(image_location) {
  this.image_item = new Image();
  this.image_item.src = image_location;
}
function get_ImageItemLocation(imageObj) {
  return(imageObj.image_item.src)
}
function randNum(x, y) {
  var range = y - x + 1;
  return Math.floor(Math.random() * range) + x;
}
function getNextImage() {
  if (random_display) {
    imageNum = randNum(0, totalImages-1);
  }
  else {
    imageNum = (imageNum+1) % totalImages;
  }
  var new_image = get_ImageItemLocation(imageArray[imageNum]);
  return(new_image);
}
function getPrevImage() {
  imageNum = (imageNum-1) % totalImages;
  var new_image = get_ImageItemLocation(imageArray[imageNum]);
  return(new_image);
}
function prevImage(place) {
  var new_image = getPrevImage();
  document[place].src = new_image;
}
function switchImage(place) {
  var new_image = getNextImage();
  document[place].src = new_image;
  var recur_call = "switchImage('"+place+"')";
  timerID = setTimeout(recur_call, interval);
}
