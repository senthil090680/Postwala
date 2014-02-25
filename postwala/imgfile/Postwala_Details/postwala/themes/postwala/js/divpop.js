// functions for trim, ltrim, and rtrim
function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
	return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
	return stringToTrim.replace(/\s+$/,"");
}
					
function divopen(idPost){
	var hidereplyvalue = document.getElementById("hidereply").value;

	if(hidereplyvalue != "") {
		document.getElementById(hidereplyvalue+"_1").style.display = "block";
		document.getElementById(hidereplyvalue).style.display = "none";
		document.getElementById(hidereplyvalue+"errormsg").style.display = "none";
	}
	document.getElementById("hidereply").value = idPost;
	document.getElementById(idPost).style.display = "block";
	document.getElementById(idPost+"errormsg").style.display = "none";
	document.getElementById(idPost+"_1").style.display = "none";
}
function divclose(idPost){
	document.getElementById(idPost).style.display = "none";
	document.getElementById(idPost+"errormsg").style.display = "none";
	document.getElementById(idPost+"_1").style.display = "block";
}

function replysubmit(idPost,path) {
	var replyname = document.getElementById(idPost+"replyname");
	var replymsg = document.getElementById(idPost+"replymsg");
	var replyemail = document.getElementById(idPost+"replyemail");
	var replymob = document.getElementById(idPost+"replymob");
	if(replyname.value == "") {
		document.getElementById(idPost+"errormsg").style.display = "block";
		document.getElementById(idPost+"errormsg").innerHTML = "Please enter the name";
		replyname.focus();
		return false;
	}
	if(replymsg.value == "") {
		document.getElementById(idPost+"errormsg").style.display = "block";
		document.getElementById(idPost+"errormsg").innerHTML = "Please enter the message";
		replymsg.focus();
		return false;
	}
	else if(replyemail.value == "") {
		document.getElementById(idPost+"errormsg").style.display = "block";
		document.getElementById(idPost+"errormsg").innerHTML = "Please enter the email";
		replyemail.focus();
		return false;
	}
	else if(replyemail.value.search(/^(\w+(?:\.\w+)*)@((?:\w+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i))
	{
		document.getElementById(idPost+"errormsg").style.display = "block";
		document.getElementById(idPost+"errormsg").innerHTML = "Please enter valid email";
		replyemail.focus();
		return false;
	}
	if(replymob.value != '')
	{
		if(isNaN(replymob.value)) {
			document.getElementById(idPost+"errormsg").style.display = "block";
			document.getElementById(idPost+"errormsg").innerHTML = "Please enter only numbers";
			replymob.focus();
			return false;
		}
		else if(replymob.value.length != 10) {			
			document.getElementById(idPost+"errormsg").style.display = "block";
			document.getElementById(idPost+"errormsg").innerHTML = "Please enter 10 digit mobile number";
			replymob.focus();
			return false;
		}
		else {
			var a=null;
			try
			{
				var a = new XMLHttpRequest();
			}
			catch(e)
			{
				try
				{
					var a = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e)
				{	
					var a =	new ActiveXObject("Microsoft.XMLHTTP");
				}
			}
			if(a==null)
			{
				alert("Your browser is out of version");
			}
				a.onreadystatechange=function()
				{
					if(a.readyState==4)
					{
						document.getElementById(idPost+"_1").style.display			=	"block";
						document.getElementById(idPost).style.display				=	"none";
						document.getElementById(idPost+"replyname").value			=	'';
						document.getElementById(idPost+"replymsg").value			=	'';
						document.getElementById(idPost+"replyemail").value			=	'';
						document.getElementById(idPost+"replymob").value			=	'';
						document.getElementById(idPost+"errormsg").style.display	=	"block";
						document.getElementById(idPost+"errormsg").innerHTML		=	"Your reply has been sent to the poster of the ad";
						window.location.hash										=	"#"+idPost+"_1";
						//document.getElementById(idPost+"errormsg").innerHTML		=	a.responseText;
					}
				}
				
				var url = path+"replyajax.php?id="+idPost+"&name="+replyname.value+"&email="+replyemail.value+"&msg="+replymsg.value+"&mob="+replymob.value;
				a.open("GET", url,true);
				a.send(null);
		}
	}
	else {
		var b=null;
			try
			{
				var b = new XMLHttpRequest();
			}
			catch(e)
			{
				try
				{
					var b = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e)
				{	
					var b =	new ActiveXObject("Microsoft.XMLHTTP");
				}
			}
			if(b==null)
			{
				alert("Your browser is out of version");
			}
				b.onreadystatechange=function()
				{
					if(b.readyState==4)
					{							
						document.getElementById(idPost+"_1").style.display			=	"block";
						document.getElementById(idPost).style.display				=	"none";
						document.getElementById(idPost+"replyname").value			=	'';
						document.getElementById(idPost+"replymsg").value			=	'';
						document.getElementById(idPost+"replyemail").value			=	'';
						document.getElementById(idPost+"errormsg").style.display	=	"block";
						document.getElementById(idPost+"errormsg").innerHTML		=	"Your reply has been sent to the poster of the ad";
						window.location.hash										=	"#"+idPost+"_1";
						//document.getElementById(idPost+"errormsg").innerHTML		=	b.responseText;
					}
				}
				
				var url = path+"replyajax.php?id="+idPost+"&name="+replyname.value+"&email="+replyemail.value+"&msg="+replymsg.value;
				b.open("GET", url,true);
				b.send(null);
	}
}

function loadcustomfields(obj,path,subcatId,publishdate,customArray,pubdate,advlocation) {
	//var searchlocation	=	document.getElementById("advancedlocation").value;
	//var searchdate		=	document.getElementById("publishdate").value;
	var searchlocation	=	advlocation;
	var searchdate		=	pubdate;
	if(searchlocation == null) {
		
		if(document.getElementById("advancedlocation").value !='') {
			searchlocation	=	document.getElementById("advancedlocation").value;
		}
	}
	if(searchdate == null) {
		if(document.getElementById("publishdate").value !='') {
			searchdate	=	document.getElementById("publishdate").value;
		}
	}
	if(obj == "") {
		document.getElementById("commonsearchspan").style.display = "block";
		document.getElementById("commonsearchspan").innerHTML = "Please select the category";
		document.getElementById("categoryIf").focus();
		return false;
	}
	else {
		var a=null;
		try
		{
			var a = new XMLHttpRequest();
		}
		catch(e)
		{
			try
			{
				var a = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch(e)
			{	
				var a =	new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		if(a==null)
		{
			alert("Your browser is out of version");
		}
			a.onreadystatechange=function()
			{
				if(a.readyState==4)
				{
					document.getElementById("loadcustom").style.display			=	"block";
					
					firstTrim	=	trim(a.responseText);
					secondTrim	=	ltrim(firstTrim);
					thirdTrim	=	rtrim(secondTrim);
					if(thirdTrim != '') {
						//document.getElementById("hidecategory").style.display	=	"none";
						//document.getElementById("hidecatspan").style.display	=	"none";
					}
					document.getElementById("loadcustom").innerHTML				=	thirdTrim;
				}
			}
			
			var url = path+"loadcustomfields.php?idLoadCategory="+obj+"&subId="+subcatId+"&selocation="+searchlocation+"&sedate="+searchdate+"&publish="+publishdate+"&customArr="+customArray;
			a.open("GET", url,true);
			a.send(null);
	}
}
