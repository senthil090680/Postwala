var errorColor="#fbc311";
var normalColor="#FFFFFF";


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


//FUNCTION FOR CUSTOM SEARCH

function customsearchfunc(obj) {
	var nocustom			=	document.getElementById('nocustom');
	var advancedloca		=	document.getElementById('advancedlocation');
	var published			=	document.getElementById('publishdate');
	var categoryIfAny		=	document.getElementById('categoryIf');
	var totalcustomfields	=	document.getElementById('totalcustomfields');

	if(advancedloca.value == '') {
		document.getElementById('commonsearchspan').innerHTML="Select the location";
		document.getElementById('advancedlocation').style.backgroundColor=errorColor;
		advancedloca.focus();
		return false;
	}
	else if(published.value == '') {
		document.getElementById('commonsearchspan').innerHTML="Select the published date";
		document.getElementById('publishdate').style.backgroundColor=errorColor;
		published.focus();
		return false;
	}
	if(nocustom.value == '') {
		document.getElementById('commonsearchspan').innerHTML="Select other category to bring the search fields";
		document.getElementById('categoryIf').style.backgroundColor=errorColor;
		categoryIfAny.focus();
		return false;
	}	
	else if(totalcustomfields.value != 500) {
		var flag = 0;
		for(var b = 0; b < totalcustomfields.value; b++) {
			serialcustomfields	=	document.getElementById('customfield'+b);			
			if(serialcustomfields.value != '') {				
				flag = 1;
			}
		}
		if(flag == 0) {
			document.getElementById('commonsearchspan').innerHTML="Please select the extra fields";
			document.getElementById('customfield0').style.backgroundColor=errorColor;
			document.getElementById('customfield0').focus();
			return false;
		}
	}
	else if(categoryIfAny.value == '') {
		document.getElementById('commonsearchspan').innerHTML="Select the category";
		document.getElementById('categoryIf').style.backgroundColor=errorColor;
		categoryIfAny.focus();
		return false;
	}
}

function publishSelect(obj) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById('commonsearchspan').innerHTML="Select the published date";
		return false;
	} else { document.getElementById('commonsearchspan').innerHTML=""; 
		return true;
	}
}

function locationSelect(obj) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById('commonsearchspan').innerHTML="Select the location";
		return false;
	} else { document.getElementById('commonsearchspan').innerHTML=""; 
		return true;
	}
}


function categorySelect(obj) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById('commonsearchspan').innerHTML="Select the category";
		return false;
	} else { document.getElementById('commonsearchspan').innerHTML=""; 
		return true;
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//FUNCTION TO GET CUSTOM FIELDS THROUGH AJAX WHEN THE CATEGORY IS CHOOSEN ALREADY THROUGH ONLOAD EVENT

function getCustomFieldsOnLoad(id,path) {
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
				document.getElementById("dycustom").style.display = "block";
				document.getElementById("dyads").style.display = "none";
				document.getElementById("dycustom").innerHTML=a.responseText;
				//document.getElementById(idPost+"errormsg").innerHTML="Your reply has been sent to the poster of the ad";
			}
		}
		
	var url = path+"dynamiccustomfields.php?idCategory="+id;
	a.open("GET", url,true);
	a.send(null);
}

//Function to validate CONTACT EMAIL FORM

function emailValidate(obj) {
	var user		=	document.getElementById('name');
	var email		=	document.getElementById('emailemail');
	var mobile		=	document.getElementById('emailmobile');
	var msg			=	document.getElementById('emailmsg');
	//var math		=	document.getElementById('emailmath');
	var userName	=	document.getElementById('name').name;
	var emailName	=	document.getElementById('emailemail').name;
	var mobileName	=	document.getElementById('emailmobile').name;
	var msgName		=	document.getElementById('emailmsg').name;
	//var mathName	=	document.getElementById('emailmath').name;
	if(user.value == '') {
		document.getElementById(userName+'span').innerHTML="Enter the name";
		document.getElementById(userName).style.backgroundColor=errorColor;
		user.focus();
		return false;
	}
	else if(email.value == '') {
		document.getElementById(emailName+'span').innerHTML="Enter the email";
		document.getElementById(emailName).style.backgroundColor=errorColor;
		email.focus();
		return false;
	}
	else if(email.value.search(/^(\w+(?:\.\w+)*)@((?:\w+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i)) {
		document.getElementById(emailName+'span').innerHTML="Enter the valid email";
		document.getElementById(emailName).style.backgroundColor=errorColor;
		email.focus();
		return false;
	}
	else if(mobile.value != '' && mobile.value.length != 10) {
		document.getElementById(mobileName+'span').innerHTML="Enter the 10 digit mobile number";
		document.getElementById(mobileName).style.backgroundColor=errorColor;
		mobile.focus();
		return false;
	}	
	else if(msg.value == '') {
		document.getElementById(msgName+'span').innerHTML="Enter the message";
		document.getElementById(msgName).style.backgroundColor=errorColor;
		msg.focus();
		return false;
	}
	/*else if(math.value == '') {
		document.getElementById(mathName+'span').innerHTML="Answer the Question";
		document.getElementById(mathName).style.backgroundColor=errorColor;
		math.focus();
		return false;
	}*/
	return true;
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Function to validate CONTACT SMS FORM

function smsValidate(obj) {
	var email		=	document.getElementById('smsemail');
	var mobile		=	document.getElementById('smsmobile');
	var msg			=	document.getElementById('smsmsg');
	//var math		=	document.getElementById('smsmath');
	var emailName	=	document.getElementById('smsemail').name;
	var mobileName	=	document.getElementById('smsmobile').name;
	var msgName		=	document.getElementById('smsmsg').name;
	//var mathName	=	document.getElementById('smsmath').name;
	if(email.value == '') {
		document.getElementById(emailName+'span').innerHTML="Enter the email";
		document.getElementById(emailName).style.backgroundColor=errorColor;
		email.focus();
		return false;
	}
	else if(email.value.search(/^(\w+(?:\.\w+)*)@((?:\w+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i)) {
		document.getElementById(emailName+'span').innerHTML="Enter the valid email";
		document.getElementById(emailName).style.backgroundColor=errorColor;
		email.focus();
		return false;
	}
	else if(mobile.value != '' && mobile.value.length != 10) {
		document.getElementById(mobileName+'span').innerHTML="Enter the 10 digit mobile number";
		document.getElementById(mobileName).style.backgroundColor=errorColor;
		mobile.focus();
		return false;
	}	
	else if(msg.value == '') {
		document.getElementById(msgName+'span').innerHTML="Enter the message";
		document.getElementById(msgName).style.backgroundColor=errorColor;
		msg.focus();
		return false;
	}
	else if(msg.value != '') {
		alert(msg.value.length);
		
		var emailcnt = document.getElementById('smsemail').value.length;
		var mobilecnt = document.getElementById('smsmobile').value.length;
		
		if(mobilecnt != '') {
			var totalcnt		=	emailcnt+mobilecnt;
			var maxlimit		=	80 - totalcnt;
		}
		else {
			var maxlimit		=	80 - emailcnt;
		}

		if (msg.value.length > maxlimit) {
			document.getElementById(msg.name+'span').innerHTML="Characters exceeding the limit of 80 characters";
			document.getElementById(msg.name).style.backgroundColor=errorColor;
			return false;
		}
		else {
			document.getElementById(msg.name+'span').innerHTML="";
			document.getElementById(msg.name).style.backgroundColor=normalColor;
			return true;
		}
	}
	/*else if(math.value == '') {
		document.getElementById(mathName+'span').innerHTML="Answer the Question";
		document.getElementById(mathName).style.backgroundColor=errorColor;
		math.focus();
		return false;
	}*/
	return true;
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function validateChar(obj) {
	var name = obj.name;
	var emailcnt = document.getElementById('smsemail').value.length;
	var mobilecnt = document.getElementById('smsmobile').value.length;
	
	if(mobilecnt != '') {
		var totalcnt		=	emailcnt+mobilecnt;
		var maxlimit		=	80 - totalcnt;
	}
	else {
		var maxlimit		=	80 - emailcnt;
	}
	if ( obj.value.length > maxlimit ) {
		obj.value = obj.value.substring( 0, maxlimit );
		var val = maxlimit - obj.value.length;
		if(val == 0) {
			document.getElementById(name+'span').innerHTML = "";
			return true;
		}
		/*
		obj.blur();
		obj.focus();
		return false;*/
	 } 
	 else {
		document.getElementById(name+'span').innerHTML = maxlimit - obj.value.length + " Characters left";
	 }
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function validMobile(obj) {
	var name = obj.name;
	if(obj.value != '' && obj.value.length != 10) {
		document.getElementById(name+'span').innerHTML="Enter the 10 digit mobile number";
		document.getElementById(name).style.backgroundColor=errorColor;
		obj.focus();
		return false;
	}
	else {
		document.getElementById(name+'span').innerHTML="";
		document.getElementById(name).style.backgroundColor=normalColor;
	}

}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//function to validate REGISTER FORM

function checkRegisterForm(obj) {
	for(var idx=0;idx<obj.length;idx++){
		var typeName=obj[idx].type;
		var eleId=obj[idx].id;
		if(eleId == "email") {
			validateEmails(obj[idx]);
			if(validateEmails(obj[idx])){				
			}
			else { return false; }
		}
		if(eleId == "password_confirmation") {
			validateConfirm(obj[idx]);
			if(validateConfirm(obj[idx])){								
			}
			else { return false; }
		}
		if(typeName == "checkbox") {
			CustomCheck(obj[idx],"Terms & Conditions");
			if(CustomCheck(obj[idx],"Terms & Conditions")){				
			}
			else { obj[idx].focus(); return false; }			
		}
		if((typeName == "text" || typeName == "password") && (eleId != "password_confirmation") && (eleId != "email")) {
			validateTextBox(obj[idx]);			
			validateAdText(obj[idx]);						
			if(validateTextBox(obj[idx])){	
			}
			else { obj[idx].focus(); return false; }
			if(validateAdText(obj[idx])) {				
			}
			else { obj[idx].focus(); return false; }			
		}
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function validateConfirm(obj) {
	var name = obj.name;
	var pass = document.getElementById('password').value;
	
	if(obj.value == ''){
		document.getElementById(name+'span').innerHTML="Enter the confirm password";
		document.getElementById(name).style.backgroundColor=errorColor;
		obj.focus();
		return false;
	}
	else if(obj.value != pass) {
		document.getElementById(name+'span').innerHTML="Enter the same password as above";
		document.getElementById(name).style.backgroundColor=errorColor;
		obj.focus();
		return false;
	}
	else { 
		document.getElementById(name+'span').innerHTML="";
		document.getElementById(name).style.backgroundColor=normalColor;
		return true;
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//function to validate LOGIN FORM

function checkLoginForm(obj) {
	for(var idx=0;idx<obj.length;idx++){
		var typeName=obj[idx].type;
		var eleId=obj[idx].id;
		if(typeName == "password") {
			validateTextBox(obj[idx]);			
			validateAdText(obj[idx]);
			
			if(validateTextBox(obj[idx])){				
			}
			else { obj[idx].focus(); return false; }
			if(validateAdText(obj[idx])) {
			}
			else { obj[idx].focus(); return false; }
			if(eleId == "email") {
				if(validateEmails(obj[idx])){				
				}
				else { obj[idx].focus(); return false; }
			}
		}
		if(eleId == "email") {
			validateEmails(obj[idx]);
			if(validateEmails(obj[idx])){				
			}
			else { return false; }
		}
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Function to validate email

function validateEmails(obj) {
	var name = obj.name;
	if(obj.value == '') {
		document.getElementById(name+'span').innerHTML="Enter the email";
		document.getElementById(name).style.backgroundColor=errorColor;
		obj.focus();
		return false;
	}
	else if(obj.value.search(/^(\w+(?:\.\w+)*)@((?:\w+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i)) {
		document.getElementById(name+'span').innerHTML="Enter the valid email";
		document.getElementById(name).style.backgroundColor=errorColor;
		obj.focus();
		return false;
	}
	else { 
		document.getElementById(name+'span').innerHTML="";
		document.getElementById(name).style.backgroundColor=normalColor;
		return true;
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//function to control the maxlength of the textarea

function imposeMaxLength(Object, MaxLen)
{
  return (Object.value.length <= MaxLen);
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//FUNCTION TO VALIDATE SEARCH BOX

function searchvalidate() {
	var search = document.getElementById("title");
	validTextDespSearch(search,'Eg: Mobiles, Real estate, computer course, old mp3','word to search'); 
	if(validTextDespSearch(search,'Eg: Mobiles, Real estate, computer course, old mp3','word to search')) {
	}
	else { search.focus(); return false; }
}

function validTextDespSearch (obj,temp,spanvalue) {
	var name = obj.name;
	if(obj.value==temp || obj.value==""){
		document.getElementById(name+'span').innerHTML="Enter the "+spanvalue;
		return false; 
	}
	else { 
		document.getElementById(name+'span').innerHTML=""; 
		return true;
	}
}

function validSpanDespSearch (obj,temp) {
	var name = obj.name;
	var Textvalue	= obj.value;
	if (Textvalue==temp ) { document.getElementById(name).style.color="#000000"; obj.value=''; }
	if (Textvalue=='') { document.getElementById(name).style.color="#B7B7B7"; obj.value=temp; }
}

//FUNCTION TO GET PREMIUM ADS THROUGH AJAX

function getPremiumAds(obj,idCat,path) {
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
				document.getElementById("dyads").style.display = "block";
				document.getElementById("dyads").innerHTML=b.responseText;
				//document.getElementById(idPost+"errormsg").innerHTML="Your reply has been sent to the poster of the ad";
			}
		}
		
	var url = path+"dynamicpremiumads.php?idCategory="+idCat+"&type="+obj.value;
	b.open("GET", url,true);
	b.send(null);
}


//FUNCTION TO GET CUSTOM FIELDS THROUGH AJAX

function getCustomFields(obj,path) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById(name+'span').innerHTML="Select the "+name;
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
					document.getElementById("dycustom").style.display = "block";
					document.getElementById("dyads").style.display = "none";
					document.getElementById("dycustom").innerHTML=a.responseText;
					//document.getElementById(idPost+"errormsg").innerHTML="Your reply has been sent to the poster of the ad";
				}
			}
			
		var url = path+"dynamiccustomfields.php?idCategory="+obj.value;
		a.open("GET", url,true);
		a.send(null);
	}
}

// Function to validate all the inputs

function ValidateReg(obj) {
	for(var idx=0;idx<obj.length;idx++){
		var typeName=obj[idx].type;
		var eleId=obj[idx].id;
		var langName = obj[idx].getAttribute("lang");
		var labelName = obj[idx].getAttribute("labelValue");
		var descName = obj[idx].getAttribute("descValue");
		if(typeName == "select-one") {
			if(langName == "custom") {
				validateAdNumber(obj[idx]);
				CustomSelect(obj[idx],labelName);
				validateAdText(obj[idx]);
				if(validateAdNumber(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }
				if(CustomSelect(obj[idx],labelName)) {
				}
				else { obj[idx].focus(); return false; }
				if(validateAdNumber(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }
		
			}
			else if(langName == "false") {
				validateSelect(obj[idx]);
				validateAdText(obj[idx]);
				validateAdNumber(obj[idx]);				
				if(validateSelect(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }
				if(validateAdText(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }
				if(validateAdNumber(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }
			}
		}
		if(typeName == "textarea") {
			if(langName == "custom") {

				validTextDesp(obj[idx],descName,labelName); 
				if(validTextDesp(obj[idx],descName,labelName)) {
				}
				else { obj[idx].focus(); return false; }
			}
			else if(langName == "false") {

				var text = tinyMCE.get('description').getContent();

				/*validateAdText(obj[idx]);
				validateTextBox(obj[idx]);
				if(validateAdText(obj[idx])){
				}
				else { 
					window.location.hash = '#descfocus';
					return false; 
				}
				if(validateTextBox(obj[idx])) {
				}
				else { 
					window.location.hash = '#descfocus';
					return false; 
				}*/
				
				if(text != '') {
					document.getElementById("descriptionspan").innerHTML="";
				}
				else { 
					window.location.hash = '#descfocus';
					document.getElementById("descriptionspan").innerHTML="Enter the description";
					return false; 
				}
				
			}
		}
		if(typeName == "text") {
			if(langName == "custom") {
				CustomText(obj[idx],labelName); 
				validateAdText(obj[idx]);
				if(CustomText(obj[idx],labelName)) {
				}
				else { obj[idx].focus(); return false; }
				if(validateAdText(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }	
			}
			else if(langName == "false") {
				mathName = obj[idx].name;
				validateTextBox(obj[idx]);
				validateAdText(obj[idx]);
				if(validateTextBox(obj[idx])){				
				}
				else { obj[idx].focus(); return false; }
				if(validateAdText(obj[idx])) {
				}
				else { obj[idx].focus(); return false; }
			}
		}
		if(typeName == "checkbox") {
			if(langName == "custom") {			
				var uy = 0;
				var CheckElement = obj[idx].name;
				var CheckElementObject = document.getElementsByName(CheckElement);

				for(var u = 0; u < CheckElementObject.length; u++) {
					if(CheckElementObject[u].checked == true){
						uy++;
					}
				}
				if(uy == 0) {
					document.getElementById(eleId+'span').innerHTML="Choose the "+labelName;
					obj[idx].focus();
					return false;
				}
				else{					
					document.getElementById(eleId+'span').innerHTML="";
				}
			}
			else if(langName == "false") {		
			}		
		}
		if(typeName == "radio") {
			if(langName == "custom") {
				var uy = 0;
				var RadioElement = obj[idx].name;
				var RadioElementObject = document.getElementsByName(RadioElement);

				for(var u = 0; u < RadioElementObject.length; u++) {
					if(RadioElementObject[u].checked == true){
						uy++;
					}
				}
				if(uy == 0) {
					document.getElementById(eleId+'span').innerHTML="Choose the "+labelName;
					obj[idx].focus(); 
					return false;
				}
				else {
					document.getElementById(eleId+'span').innerHTML="";
				}
			}
			else if(langName == "false") {				
			}
		}
	}
	document.getElementById('submit').value="loading...";
	return true;
}



function validateSelect(obj) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById(name+'span').innerHTML="Select the "+name;
		return false;
	} else { document.getElementById(name+'span').innerHTML=""; 
		return true;
	}
}
function validateTextBox(obj) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		if(name == "math" || name == "emailmath" || name == "smsmath"){
			document.getElementById(name+'span').innerHTML="Answer the Question";
			return false;
		}		
		else if(name == "emailmsg" || name == "smsmsg"){
			document.getElementById(name+'span').innerHTML="Enter the message";
			return false;
		}
		else{
			document.getElementById(name+'span').innerHTML="Enter the "+name;
			return false;
		}
		return false;
	} else { document.getElementById(name+'span').innerHTML="";
		return true;
	}
}

function validTextArea(obj) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById(name+'span').innerHTML="Enter the "+name;
		return false;
	} else { document.getElementById(name+'span').innerHTML=""; 
		return true;
	}
}


function CustomText(obj,spanvalue) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById(name+'span').innerHTML="Enter the "+spanvalue;
		return false;
	} else { document.getElementById(name+'span').innerHTML=""; 
		return true;
	}
}

function CustomSelect(obj,spanvalue) {
	var name = obj.name;
	if (IsEmpty(obj,"text")) {
		document.getElementById(name+'span').innerHTML="Select the "+spanvalue;
		return false;
	} else { document.getElementById(name+'span').innerHTML=""; 
		return true;
	}
}

function CustomRadio(obj,spanvalue) {
	var name = obj.id;
	var uy = 0;
	var CheckElement = obj.name;
	var CheckElementObject = document.getElementsByName(CheckElement);

	for(var u = 0; u < CheckElementObject.length; u++) {
		if(CheckElementObject[u].checked == true){
			uy++;
		}
	}
	if(uy == 0) {
		document.getElementById(name+'span').innerHTML="Choose the "+spanvalue;
		obj.focus();
		return false;
	}
	else{					
		document.getElementById(name+'span').innerHTML="";
	}
}
function CustomCheck(obj,spanvalue) {
	var name = obj.id;
	var uy = 0;
	var CheckElement = obj.name;
	var CheckElementObject = document.getElementsByName(CheckElement);

	for(var u = 0; u < CheckElementObject.length; u++) {
		if(CheckElementObject[u].checked == true){
			uy++;
		}
	}
	if(uy == 0) {
		document.getElementById(name+'span').innerHTML="Choose the "+spanvalue;
		obj.focus();
		return false;
	}
	else{					
		document.getElementById(name+'span').innerHTML="";
		return true;
	}
}

function validTextDesp (obj,temp,spanvalue) {
	var name = obj.name;
	if(obj.value==temp || obj.value==""){
		document.getElementById(name+'span').innerHTML="Enter the "+spanvalue;
		return false; 
	}
	else { 
		document.getElementById(name+'span').innerHTML=""; 
		return true;
	}
}

function validSpanDesp (obj,temp) {
	var name = obj.name;
	var Textvalue	= obj.value;
	if (Textvalue==temp ) { obj.value=''; }
	if (Textvalue=='') { obj.value=temp; }
}

function validateAdText(e){
	var name = e.id;
	if(e.value.length<1){
		document.getElementById(name).style.backgroundColor=errorColor;
		return false;
	}
	else{
		document.getElementById(name).style.backgroundColor=normalColor;
		return true;
	}
}
function validateAdNumber(e){
	var name = e.id;
	if(e.value.length<1){
		document.getElementById(name).style.backgroundColor=errorColor;
		return false;
	}
	else{
		document.getElementById(name).style.backgroundColor=normalColor;
		return true;
	}
}
function isNumberKeyAd(evt){
	var charCode=(evt.which)?evt.which:event.keyCode;
	if((charCode==46||charCode==8||charCode==45||charCode==47)||(charCode>=48&&charCode<=57)){
		return true;
	}
	else{
		return false;
	}
}


function IsEmpty(obj, obj_type)
{
	if (obj_type == "text" || obj_type == "password" || obj_type == "textarea" || obj_type == "file")	{
		var objValue;
		objValue = obj.value.replace(/\s+$/,"");
		if (objValue.length == 0) {
			return true;
		} else {

			return false;
		}
	} else if (obj_type == "select" || obj_type == "select-one") {
		for (i=0; i < obj.length; i++) {
			if (obj.options[i].selected) 
				{
					if(obj.options[i].value=="") 
					{return true;obj.focus();} else {return false;}
					
					if(obj.options[i].value == "0") 
					{
						if(obj.options[i].seletedIndex == "0") 
						{return true;obj.focus();}
					} else {return false;}
				}
			
		}
		return true;	
	} else if (obj_type == "radio" || obj_type == "checkbox") {
		if (!obj[0] && obj) {
			if (obj.checked) {
				return false;
			} else {
				return true;	
			}
		} else {
			for (i=0; i < obj.length; i++) {
				if (obj[i].checked) {
					return false;
				}
			}
			return true;
		}
	} else {
		return false;
	}
}


/*function removeBlock(val) {
	if(document.getElementById(val.name+"span").style.display == "block") {
		document.getElementById(val.name+"span").style.display = "none";
	}
}*/
function validateList(val) {
	if(val.value == '') {
		alert("Please choose an option");		
	}
	else {
		val.style.backgroundColor=normalColor;
	}
}

function validateTextArea() {
	e				=	document.getElementById('cusvalues');
	var selObj		=	document.getElementById('custype');
	var selValue	=	selObj.options[selObj.selectedIndex].value;
	if(selValue == 1 || selValue == 3 || selValue == 6 || selValue == 0) {
		return true;
	}
	else {
		if(e.value == ''){e.style.backgroundColor=errorColor; return false;}
		else {
			if (e.value.indexOf(',') == -1){
				e.style.backgroundColor=errorColor; return false;
			}
			else{e.style.backgroundColor=normalColor; return true;}		
		}
	}
}
function show(id){
	element=document.getElementById(id);
	if(element!=null){
		element.style.display='block';
	}
}
function hide(id){
	element=document.getElementById(id);
	if(element!=null){
		element.style.display='none';
	}
}
function mailshow(div,otherdiv,id,otherid,imagepath){
	show(id);
	document.getElementById(div).innerHTML = imagepath+"/replyemail1.jpg />";
	document.getElementById(otherdiv).innerHTML = imagepath+"/replysms.jpg />";
	hide(otherid);
}
function smsshow(div,otherdiv,id,otherid,imagepath){
	show(id);
	document.getElementById(div).innerHTML = imagepath+"/replysms1.jpg />";
	document.getElementById(otherdiv).innerHTML = imagepath+"/replyemail.jpg />";
	hide(otherid);
}

function openClose(id){
	element=document.getElementById(id);
	if(element!=null){
		if(element.style.display=='block'){
			hide(id);
		}
		else{
			show(id);
		}
	}
}

function ValidationException(codigo,mensaje,campo){
	this.codigo=(codigo==undefined)?0:codigo;
	this.mensaje=(mensaje==undefined)?null:mensaje;
	this.campo=(campo==undefined)?null:campo;
}
function validateElements(elementos){
	for(var idx=0;idx<elementos.length;idx++){
		var vacio=elementos[idx].getAttribute("lang");		
		if(vacio=="radiolang") {
			var uy = 0;
			var RadioElement = elementos[idx].name;
			var RadioElementObject = document.getElementsByName(RadioElement);

			for(var u = 0; u < RadioElementObject.length; u++) {
				if(RadioElementObject[u].checked == true){
					uy++;
				}
			}
			if(uy == 0) {
				alert("Please choose at least one option");
				throw new ValidationException(1,"Needed: ",elementos[idx]);
			}			
		}
		else if(vacio=="checklang") {
			var uy = 0;
			var CheckElement = elementos[idx].name;
			var CheckElementObject = document.getElementsByName(CheckElement);

			for(var u = 0; u < CheckElementObject.length; u++) {
				if(CheckElementObject[u].checked == true){
					uy++;
				}
			}
			if(uy == 0) {
				alert("Please choose at least one option");
				throw new ValidationException(1,"Needed: ",elementos[idx]);
			}	
		}
		else {
			if(((vacio=="false")&&(elementos[idx].value==""))||elementos[idx].style.backgroundColor=="rgb(251, 195, 17)"){
				throw new ValidationException(1,"Needed: ",elementos[idx]);
			}
		}
	}
}
function checkForm(form){
	var ok;
	try{
		validateElements(form.elements);
		ok=true;
	}
	catch(ex){
		ex.campo.style.backgroundColor=errorColor;
		ex.campo.focus();
		ok=false;
	}
	if(ok==true)
	document.getElementById('submit').value="loading...";
	return ok;
}
function validateEmail(email){
	if(!isEmail(email.value)){
		email.style.backgroundColor=errorColor;
	}
	else{
		email.style.backgroundColor=normalColor;
	}
}
function validateText(e){
	if(e.value.length<3){
		e.style.backgroundColor=errorColor;
	}
	else{
		e.style.backgroundColor=normalColor;
	}
}
function validateNumber(e){
	if(e.value.length<1){
		e.style.backgroundColor=errorColor;
	}
	else{
		e.style.backgroundColor=normalColor;
	}
}
function isNumberKey(evt){
	var charCode=(evt.which)?evt.which:event.keyCode;
	if((charCode==46||charCode==8||charCode==45||charCode==47)||(charCode>=48&&charCode<=57)){
		return true;
	}
	else{
		return false;
	}
}
function isAlphaKey(evt){
	var charCode=(evt.which)?evt.which:event.keyCode;
	if((charCode==231||charCode==199)||(charCode==241||charCode==209)||(charCode==8||charCode==32)||((charCode>=65&&charCode<=90)||(charCode>=97&&charCode<=122))){
		return true;
	}
	else{
		return false;
	}
}
function isEmail(valor){
	if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(valor)){
		return(true)
	}
	else{
		return false;
	}
}
function youtubePrompt(){
    vurl=prompt('Youtube.com URL','http://www.youtube.com/watch?v=XXXXXXX');
    if(vurl.indexOf("http://www.youtube.com/watch?v=")==0){
        document.getElementById('video').value=vurl;
        file=vurl.substr(31,vurl.length);
        tags = "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\""+file+"\"></param><param name=\"wmode\" value=\"transparent\" ></param><embed src=\"http://www.youtube.com/v/"+file+"\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed> </object>"; 
        document.getElementById('youtubeVideo').innerHTML=tags;
    }
    else {
         document.getElementById('video').value="";
         document.getElementById('youtubeVideo').innerHTML="";
    }
}
function search_func(url)
{
		document.word_search.method="get";
		document.word_search.action=url;
		document.word_search.submit();
}