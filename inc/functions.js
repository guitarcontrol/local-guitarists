/*
	functions.js: by Joel Firestone
	
	This just controls all of the JavaScript for the site. That's about it.
*/

function regForm() {
	var message = "The followed required fields were not supplied:\n";
	var strUsername = document.register.strUsername.value;
	var strPassword = document.register.strPassword.value;
	var bolChecked = document.register.acceptTerms.checked;
	var counter = 0;
	
	if (document.register.strFName.value == "") { message += " - First Name\n"; counter++; }
	if (document.register.strLName.value == "") { message += " - Last Name\n"; counter++; }
	if (document.register.strEmail.value == "") { message += " - Email Address\n"; counter++; }
	if (document.register.strEmail2.value == "") { message += " - Confirmed Email\n"; counter++; }
	if (strUsername == "") { message += " - Username\n"; counter++; }
	if (document.register.strPassword.value == "") { message += " - Password\n"; counter++; }
	if (document.register.strPassword2.value == "") { message += " - Confirmed Password\n"; counter++; }
	if (document.register.intAge.value == "") { message += " - Age\n"; counter++; }
	if (document.register.strCity.value == "") { message += " - City\n"; counter++; }
	if (document.register.intState.options[document.register.intState.selectedIndex].value == "") { message += " - State\n"; counter++; }
	if (document.register.strZipCode.value == "") { message += " - Zip Code\n"; counter++; }
	if (document.register.intCountry.options[document.register.intCountry.selectedIndex].value == "") { message += " - Country\n"; counter++; }
	
	if (counter > 0) {
		alert(message);
		return false;
	}
	
	// check to make sure the username is ok
	if (strUsername.length < 4 || strUsername.length > 20) {
		alert("Please choose a username between 4 and 20 characters in length.\n" +
			  "Currently it's " + strUsername.length + " characters.");
		return false;
	}
	
	// check to make sure the password is ok
	if (strPassword.length < 4 || strPassword.length > 20) {
		alert("Please choose a password between 4 and 12 characters in length.\n" +
			  "Currently it's " + strPassword.length + " characters.");
		return false;
	}
	
	// make sure the passwords match
	if (document.register.strPassword.value != document.register.strPassword2.value) {
		alert("Please make sure your passwords match before continuing.");
		return false;
	}
	
	// make sure the username and password aren't the same
	if (document.register.strUsername.value == document.register.strPassword.value) {
		alert("Please choose a password that is not the same as your username.");
		return false;
	}
	
	// make sure the passwords match
	if (document.register.strEmail.value != document.register.strEmail2.value) {
		alert("Please confirm your correct email address.");
		return false;
	}
	
	// make sure the email is valid
	var intGoodEmail = checkEmail(document.register.strEmail.value);
	
	if (intGoodEmail == 0) {
		alert("Your email address \(" + document.register.strEmail.value + "\) appears to be invalid.");
		return false;
	}
	
	// make sure they accept the "terms of use"
	if (document.register.acceptTerms.checked == false) {
		alert("You must accept the \"Terms of Use\" policy before we will\n" +
			  "continue the registration process.");
		return false;
	}
	
	// all good!
	return true;
}

function checkEmail(email) {
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/.test(email)) {
		return 1;
	}
		// alert("Invalid E-mail Address! Please re-enter.")
		return 0;
	}

function checkPost() {
	// set our variables
	strMessage = "Please provide the following:\n";
	intCount = 0;
	
	// make sure our required variables are set
	if (document.myForm.intForum.options[document.myForm.intForum.selectedIndex].value == "") { strMessage += " - Forum\n"; intCount++ }
	if (document.myForm.strTitle.value == "") { strMessage += " - Title\n"; intCount++ }
	if (document.myForm.txtPost.value == "") { strMessage += " - Message Body\n"; intCount++ }
	
	// stop, if we need to
	if (intCount > 0) {
		alert(strMessage);
		return false;
	}
	// all good
	return true;
}

function checkPostEdit() {
	// set our variables
	strMessage = "Please provide the following:\n";
	intCount = 0;
	
	// make sure our required variables are set
	if (document.myForm.strTitle.value == "") { strMessage += " - Title\n"; intCount++ }
	if (document.myForm.txtPost.value == "") { strMessage += " - Message Body\n"; intCount++ }
	
	// stop, if we need to
	if (intCount > 0) {
		alert(strMessage);
		return false;
	}
	// all good
	return true;
}

function checkReply() {
	// set our variables
	strMessage = "Please provide the following:\n";
	intCount = 0;
	
	if (document.myForm.strTitle.value == "") { strMessage += " - Title\n"; intCount++ }
	if (document.myForm.txtReply.value == "") { strMessage += " - Message Body\n"; intCount++ }
	
	// stop, if we need to
	if (intCount > 0) {
		alert(strMessage);
		return false;
	}
	// all good
	return true;
}

function checkChords() {
	// var check fields
	for (i = 0; i < document.myForm.elements.length; i++ ) {
		if (document.myForm.elements[i].value == "") {
			alert("Please choose all criteria before continuing.");
			return false;
		}
	}
	// if it's all good, go ahead
	strImage = new Image(493,217);
	strRoot = document.myForm.root[document.myForm.root.options.selectedIndex].value;
	strType = document.myForm.type[document.myForm.type.options.selectedIndex].value
	strImage.src = "images/c01" + strRoot + strType + document.myForm.instrument.value + "00.gif";
	document.images['myChord'].src = strImage.src;
	return true;
}

function checkScales() {
	// var check fields
	for (i = 0; i < document.myForm.elements.length; i++ ) {
		if (document.myForm.elements[i].type == "select-one") {
			if (document.myForm.elements[i].selectedIndex == 0) {
				alert("Please choose all criteria before continuing.");
				return false;
			}
		}
	}
	// if it's all good, go ahead
	// strInst = document.myForm.instrument[document.myForm.instrument.options.selectedIndex].value;
	strInst = "00";
	strRoot = document.myForm.root[document.myForm.root.options.selectedIndex].value;
	strType = document.myForm.type[document.myForm.type.options.selectedIndex].value
	
	if (strInst == "00") {
		strImage = new Image(505,225);
	} else {
		strImage = new Image(505,175);
	}
	
	strImage.src = "images/c00" + strRoot + strType + strInst + "00.gif";
	document.images['myScale'].src = strImage.src;
	
	return true;
}

function checkPersonalInfo() {
	// set our values
	strMessage = "The following required fields are incomplete:\n";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.myInfo.strFName.value == "") { strMessage += "- First Name\n"; intCount++ }
	if (document.myInfo.strLName.value == "") { strMessage += "- Last Name\n"; intCount++ }
	
	// check our photo upload (if supplied)
	/* strPhoto = document.myInfo.strPhoto.value;
	if (strPhoto != "") {
		if (strPhoto.indexOf(".gif") == -1 && strPhoto.indexOf(".jpg") == -1 && strPhoto.indexOf(".jpeg") == -1) {
			strMessage = "You can only upload GIF \(.gif\) or JPEG \(.jpg\) files\nto our site. All other files are not allowed.";
			intCount++;
		}
	}
	*/
	
	// proceed
	if (intCount > 0) {
		alert(strMessage);
		return false;
	}
	// all good
	return true;
}

function checkDemoInfo() {
	// set our values
	strMessage = "The following required fields are incomplete:\n";
	intCount = 0;
	
	// check for our required fields
	if (document.myInfo.strCity.value == "") { strMessage += "\nCity"; intCount++ }

	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage); return false;
	}
	
	return true;
}

function checkPlayInfo() {
	// set our values
	strMessage = "The following required fields are incomplete:";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.myInfo.txtGear.value == "") { strMessage += "\nEquipment Used"; intCount++ }
	
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	return true;
}

function checkPassInfo() {
	// set our values
	strMessage = "The following required fields are incomplete:\n";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.myPass.password.value == "") { strMessage += "- Your Password\n"; intCount++ }
	if (document.myPass.newpassword.value == "") { strMessage += "- New Password\n"; intCount++ }
	if (document.myPass.confirmpassword.value == "") { strMessage += "- Confirmed Password\n"; intCount++ }
		
	// make sure the new password match
	if (intCount == 0) {
		if (document.myPass.newpassword.value != document.myPass.confirmpassword.value) {
			strMessage = "Your new passwords do not match. The need to be exactly the same.";
			intCount++;
		}
	}
	
	// proceed
	if (intCount > 0) {
		alert(strMessage); return false;
	} 
	return true;
}

function checkEmailInfo() {
	// based on our section, set our required fields
	if (document.myEmail.strPublicEmail.value == "" || document.myEmail.strEmail.value == "") { 
		alert("Both addresses are required to submit the change. Your public email\n" +
			  "can be anything you like \(i.e. myname at domain dot com\). Your\n" +
			  "private email, however, must be a real, live address.");
		return false;
	}
	
	// make sure the password is valid
	intValid = checkEmail(document.myEmail.strEmail.value);
	
	if (!intValid) {
		alert("Your email address \(" + document.myEmail.strEmail.value + "\) appears to be invalid.");
		return false;
	}
	
	// all good
	return true;
}

function valApp() {
	// make sure our required fields have been supplied
	strMessage = "The following required fields are incomplete:\n";
	intCat = document.myApp.intCatID.options[document.myApp.intCatID.selectedIndex].value;
	intCount = 0;
	
	// based on our section, set our required fields
	if (intCat == "") { strMessage += "Category\n"; intCount++ }
	if (document.myApp.strName.value == "") { strMessage += "Program Name\n"; intCount++ }
	if (document.myApp.txtDescription.value == "") { strMessage += "Description\n"; intCount++ }
	if (document.myApp.strPrice.value == "") { strMessage += "Price\n"; intCount++ }
	if (document.myApp.strURL.value == "") { strMessage += "URL\n"; intCount++ }
		
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	
	// make sure they chose an operating system
	/* for (i = 0; i < document.myApp.strOS.length; i++) {
		if (document.myApp.strOS[i].checked == true) {
			return true;
		}
	}
	// no OS chosen
	alert("Please choose an operating system to list the software under.");
	return false; */
	
	return true;
}

function valLink() {
	// make sure our required fields have been supplied
	strMessage = "The following required fields are incomplete:\n";
	intCat = document.myLink.intCatID.options[document.myLink.intCatID.selectedIndex].value;
	intCount = 0;
	
	// based on our section, set our required fields
	if (intCat == "") { strMessage += "Category\n"; intCount++ }
	if (document.myLink.strName.value == "") { strMessage += "Program Name\n"; intCount++ }
	if (document.myLink.txtDescription.value == "") { strMessage += "Description\n"; intCount++ }
	if (document.myLink.strURL.value == "") { strMessage += "URL\n"; intCount++ }
		
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	
	// all done!
	return true;
}

function valSongPost() {
	// make sure our required fields have been supplied
	strMessage = "The following fields are required:\n";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.mySong.Title.value == "") { strMessage += " - Song Title\n"; intCount++ }
	if (document.mySong.CategoryID.options[document.mySong.CategoryID.selectedIndex].value == "") { strMessage += " - Category\n"; intCount++ }
	if (document.mySong.FileSize.value == "") { strMessage += " - File Size\n"; intCount++ }
	if (document.mySong.Blurb.value == "") { strMessage += " - Brief Description\n"; intCount++ }
	if (document.mySong.Description.value == "") { strMessage += " - Description\n"; intCount++ }
	if (document.mySong.SongURL.value == "") { strMessage += " - Song URL\n"; intCount++ }
		
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	
	// all done!
	return true;
}

function valTuning() {
	// make sure our required fields have been supplied
	strMessage = "The following required fields are incomplete:\n";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.myTuning.strName.value == "") { strMessage += "Tuning Name\n"; intCount++ }
	
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	
	// all good!
	return true;
}

function valLesson() {
	// make sure our required fields have been supplied
	strMessage = "The following required fields are incomplete:\n";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.myForm.intCatID.selectedIndex == 0) { strMessage += "Category\n"; intCount++ }
	if (document.myForm.strTitle.value == "") { strMessage += "Lesson Name\n"; intCount++ }
	if (document.myForm.txtPost.value == "") { strMessage += "Lesson Text\n"; intCount++ }
	
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	
	// all good!
	return true;
}

function valLogin() {
	// make sure the username and password both exist
	if (document.myLogin.username.value == "" || document.myLogin.password.value == "") {
		alert("Both your username and password are required to login. Thanks.");
		return false;
	}
	// all good!
	return true;
}

function deliverIt() {
	// make sure the username and password both exist
	if (document.emailIt.name.value == "" || document.emailIt.email.value == "") {
		alert("Both fields are required to continue. Thanks.");
		return false;
	}
	// all good!
	return true;
}

function smileIt(face,page) {
	// specify our value
	if (page == 1) {
		document.myForm.txtReply.value += ' ' + face;
		document.myForm.txtReply.focus();
	} else {
		document.myForm.txtPost.value += ' ' + face;
	}
}

function displayWindow(url, width, height) {
	// open a new window
	var Win = window.open(url,"displayWindow",'width=' + width + ',height=' + height + ',resizable=0,scrollbars=1,menubar=0,status=0' );
}

function newWin(URL,width,height) {
	var newWin = window.open(URL, "newWin", "width=" + width + ",height=" + height + ",menu=0,scrollbars=1");
}

function plainWin(URL,width,height) {
	
	var left = (screen.width/2)-(400/2);
    var top = (screen.height/2)-(400/2);
	var newWin = window.open(URL, "newWin", "width=" + width + ",height=" + height + ",menu=0,scrollbars=0,top="+ top +",left=" +left);
}

// Unique Random Numbers Picker
// -Picks a number of unique random numbers from an array
// (c) 2002 Premshree Pillai
// http://www.qiksearch.com, http://javascript.qik.cjb.net
// E-mail : qiksearch@rediffmail.com

var numArr = new Array("0","1","2","3","4","5","6","7","8","9"); // Add elements here
var pickArr = new Array(); // The array that will be formed
var count=0;
var doFlag=false;
var iterations=0;

function pickNums(nums) {
	iterations+=1;
	var currNum = Math.round((numArr.length-1)*Math.random());
	
	if(count!=0) {
		for(var i=0; i<pickArr.length; i++) {
			if(numArr[currNum]==pickArr[i]) {
				doFlag=true;
				break;
			}
		}
	}
	
	if(!doFlag) {
		// document.write('<b>' + numArr[currNum] + '</b> <font color="#808080">|</font> ');
		count+=1;
	}
	
	if(iterations<(numArr.length*3)) {
		if((count<nums)) {
			pickNums(nums);
		}
	} else {
		location.reload();
	}
}

function valRegCode() {
	// make sure all of the fields have been supplied
	var intID = document.myCode.ID.value;
	var strRegKey = document.myCode.regKey.value;
	var strPass = document.myCode.password.value;
	
	// if any are blank, stop
	if (intID == "" || strRegKey == "" || strPass == "") {
		alert("All fields are required to continue.");
		return false;
	}
	return true;
}

function valQuery() {
	intChecked = 0;
	
	// make sure they chose a city
	/* if (document.mySearch.strCity.options.selectedIndex == -1) {
		alert("Please choose at least 1 city to search by.");
		return false;
	} */
	
	// make sure they chose at least 1 style of music
	for (i = 0; i < document.mySearch.elements.length; i++) {
		if (document.mySearch.elements[i].type == "select-multiple" && document.mySearch.elements[i].selectedIndex == -1) {
			alert("Please choose a city to search by.");
			return false;
		}
		
		/* if (document.mySearch.elements[i].type == "checkbox") {
			if (document.mySearch.elements[i].checked == true) {
				intChecked++;
			}
		} */
	}
	
	/* if (!intChecked) {
		alert("Please choose a style of music to search by.");
		return false;
	} */
	
	return true;
}

function valTab() {
	// make sure our required fields have been supplied
	strMessage = "The following required fields are incomplete:\n";
	intCount = 0;
	
	// based on our section, set our required fields
	if (document.newTab.strBandName.value == "") { strMessage += " - Artist Name\n"; intCount++ }
	if (document.newTab.strSongName.value == "") { strMessage += " - Song Name\n"; intCount++ }
	if (document.newTab.txtTablature.value == "") { strMessage += " - Tab Text\n"; intCount++ }
	
	// proceed
	if (intCount > 0) {
		// something's missing
		alert(strMessage);
		return false;
	}
	
	// all good!
	return true;
}
