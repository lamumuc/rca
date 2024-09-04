
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})

function initRace() {
	GetCurrR();
	var r = parseInt(document.getElementById("r").innerHTML);
	var m = parseInt(document.getElementById("m").innerHTML);
	GetTable("racecurr.php","","tRaceCurr");
	GetTable("racecomp.php","","tRaceComp");
	var currentTime = new Date();
	var timeNext000 = 1000 - (currentTime.getMilliseconds() % 1000);

	setTimeout(function() {
		setInterval(function(){
			GetCurrR(); 

			if (r != parseInt(document.getElementById("r").innerHTML)) {
				console.log("Race - Last r: ",r," Curr r: ",document.getElementById("r").innerHTML);
				r = parseInt(document.getElementById("r").innerHTML);
				m = parseInt(document.getElementById("m").innerHTML);
				
				GetTable("racecurr.php","","tRaceCurr");
				GetTable("racecomp.php","","tRaceComp");
			}

			RaceGetT();
		}, 1000);
	}, timeNext000);
}

function initMark() {
	GetCurrR();
	var r = parseInt(document.getElementById("r").innerHTML);
	var m = parseInt(document.getElementById("m").innerHTML);
	var currentTime = new Date();
	var timeNext000 = 1000 - (currentTime.getMilliseconds() % 1000);

	setTimeout(function() {
		setInterval(function(){
			GetCurrR(); 

			if (r != parseInt(document.getElementById("r").innerHTML)) {
				console.log("Mark - Last r: ",r," Curr r: ",document.getElementById("r").innerHTML);
				r = parseInt(document.getElementById("r").innerHTML);
				m = parseInt(document.getElementById("m").innerHTML);
				
				GetTable("marksail.php","m=" + m,"tMark");
			}
		}, 1000);
	}, timeNext000);
}

function initTime() {
	GetCurrR();
	var r = parseInt(document.getElementById("r").innerHTML);
	var v = "viewCate";
	GetTable("timetool.php","v=" + v,"tTimeTool");

	var currentTime = new Date();
	var timeNext000 = 1000 - (currentTime.getMilliseconds() % 1000);

	setTimeout(function() {
		setInterval(function(){
			GetCurrR(); 

			if (r != parseInt(document.getElementById("r").innerHTML) || v != document.querySelector('input[name="viewOpts"]:checked').value) {
				console.log("Time - Last r: ",r," Curr r: ",document.getElementById("r").innerHTML);
				r = parseInt(document.getElementById("r").innerHTML);
				v = document.querySelector('input[name="viewOpts"]:checked').value;
				GetTable("timetool.php","v=" + v,"tTimeTool");
			}

			GetTable("timeview.php","r=" + parseInt(document.querySelector('input[name="vRace"]:checked').value) + "&m=" + document.querySelector('input[name="vMark"]:checked').value,"tTimeView");
		}, 1000);
	}, timeNext000);
}

function initRank() {
	GetCurrR();
	var r = parseInt(document.getElementById("r").innerHTML);
	GetTable("rankgrid.php","","tRank");
	var currentTime = new Date();
	var timeNext000 = 1000 - (currentTime.getMilliseconds() % 1000);

	setTimeout(function() {
		setInterval(function(){
			GetCurrR(); 

			if (r != parseInt(document.getElementById("r").innerHTML)) {
				console.log("Rank - Last r: ",r," Curr r: ",document.getElementById("r").innerHTML);
				r = parseInt(document.getElementById("r").innerHTML);
				
				var rE = Math.floor((r - 4) / 8) + 1;
				document.getElementById("rE").innerHTML = rE + ' worst race(s) excluded.';
			}
				
			GetTable("rankgrid.php","","tRank");
		}, 1000);
	}, timeNext000);
}

function GetCurrR() {
	const xmlhttp = new XMLHttpRequest();
	xmlhttp.onload = function(){
		document.getElementById("r").innerHTML = parseInt(this.responseText);
	}
	xmlhttp.open("GET", "racegetr.php", true);
	//xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send();
}

function GetTable(url, data, tId) {
	const xmlhttp = new XMLHttpRequest();
	xmlhttp.onload = function() {
		document.getElementById(tId).innerHTML = this.responseText;
	};
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(data);
}

function RaceGetT() {  // update timeS_ timeF_ timeC_ when changed, compute timeL_ every second
	const xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200) {
			var jsonArray = JSON.parse(this.responseText);

			for (var i = 0; i < jsonArray.length; i++) {
				var obj = jsonArray[i];
				// console.log(" name " + obj.name + " t " + obj.t );
				if (document.getElementById(obj.name) !== null && document.getElementById(obj.name).innerHTML != obj.t) {
					// console.log("new value name " + obj.name + " t " + obj.t );
					document.getElementById(obj.name).innerHTML = obj.t;

					if (obj.name.startsWith("timeS_")) {
						if (obj.t != "") {		// race started , show time
							document.getElementById(obj.name).style.display = "block";
							document.getElementById("timeL_" + obj.name.substring(6)).style.display = "block";
							// document.getElementById("b" + obj.name.substring(6)).style.display = "none";
						} else {				// race not yet started , hide time
							document.getElementById(obj.name).style.display = "none";
							document.getElementById("timeL_" + obj.name.substring(6)).style.display = "none";
							// document.getElementById("b" + obj.name.substring(6)).style.display = "block";
						}
					}
				}
			}
		}
	}
	xmlhttp.open("GET", "racegett.php", true);
	xmlhttp.send();	


	// get time offset
	var offsetTime = document.getElementById("bOffset").innerHTML * 1000;

	// update current time
	var currentTime = new Date();
	var currentDate = new Date(currentTime.getFullYear(), currentTime.getMonth(), currentTime.getDate());
	var timeNow = currentTime-currentDate;
	document.getElementById("timeCurrent").innerHTML = TimeHHMMSS(currentTime.getTime() + offsetTime);

	//for all elements ID start with "timeS_", generate "timeL_"
	var timeElements = document.querySelectorAll('span[id^="timeS_"]');
	if (timeElements) {
		for (var i = 0; i < timeElements.length; i++) {
			if (timeElements[i].innerHTML != "") {
				var timeDif = timeNow + offsetTime - TimeMS(timeElements[i].innerHTML); // new Date("1970-01-01T" + timeVal + "Z");
				// console.log(timeElements[i].id, " timeNow " + timeNow, " timeVal " + TimeMS(timeElements[i].innerHTML), " timeDif " + timeDif);
				
				var elementTimeL = document.getElementById("timeL_" + timeElements[i].id.substr(6));
				if (timeDif > 0){
					if (timeDif > (document.getElementById("bLimits").innerHTML * 60 * 1000)) { // check if timeDif > time limits
						elementTimeL.innerHTML = "Time's Up!";
						elementTimeL.style.color = "red";
					} else {
						elementTimeL.innerHTML = new Date(timeDif).toISOString().substr(11, 8);
					}
					
					if (timeDif > (5 * 60 * 1000) && document.getElementById("b" + timeElements[i].id.substring(6))) { // check if timeDif > 5 mins
						if (document.getElementById("b" + timeElements[i].id.substring(6))) { document.getElementById("b" + timeElements[i].id.substring(6)).remove(); } // destroy the Start button of the gp
						if (document.getElementById("racemode")) { document.getElementById("racemode").remove(); } // destroy the change mode button
					}
				} else {
					if (timeDif < -(30 * 60 * 1000)) {
						if (document.getElementById("b" + timeElements[i].id.substring(6))) { document.getElementById("b" + timeElements[i].id.substring(6)).remove(); } // destroy the Start button of the gp
						if (document.getElementById("racemode")) { document.getElementById("racemode").remove(); } // destroy the change mode button
					} else {
						elementTimeL.innerHTML = ">> " + new Date(1000-timeDif).toISOString().substr(14, 5);
					}
				}
			}
		}
	}

	//for all elements ID start with "timeF_", generate "timeC_"
	timeElements = document.querySelectorAll('span[id^="timeF_"]');
	if (timeElements) {
		for (var i = 0; i < timeElements.length; i++) {
			if (timeElements[i].innerHTML != "") {
				var cutoff = TimeMS(timeElements[i].innerHTML) + document.getElementById("bCutoff").innerHTML * 60 * 1000;
				
				var elementTimeC = document.getElementById("timeC_" + timeElements[i].id.substr(6));
				if ((parseInt(document.getElementById("numOUT_" + timeElements[i].id.substr(6)).innerHTML) //+ parseInt(document.getElementById("numDNF_" + timeElements[i].id.substr(6)).innerHTML)
				+ parseInt(document.getElementById("numFIN_" + timeElements[i].id.substr(6)).innerHTML)) 
				== parseInt(document.getElementById("numALL_" + timeElements[i].id.substr(6)).innerHTML)) {
					elementTimeC.innerHTML = "Done!";
					elementTimeC.style.color = "green";
					if (document.getElementById("bLimits")) { document.getElementById("bLimits").disabled = true; } // disable the Limits button
					if (document.getElementById("bCutoff")) { document.getElementById("bCutoff").disabled = true; } // disable the Cutoff button
				} else if (TimeMS(document.getElementById("timeCurrent").innerHTML) > cutoff) {
					elementTimeC.innerHTML = "Cut!";
					elementTimeC.style.color = "yellow";
					if (document.getElementById("bLimits")) { document.getElementById("bLimits").disabled = true; } // disable the Limits button
					if (document.getElementById("bCutoff")) { document.getElementById("bCutoff").disabled = true; } // disable the Cutoff button
				} else {
					elementTimeC.innerHTML = TimeHHMMSS(currentDate.getTime() + cutoff);
				}
			}
		}
	}
}

function RaceUploadFile() {
	if (document.getElementById('inputEntry').files.length +
		document.getElementById('inputGrade').files.length +
		document.getElementById('inputRoute').files.length === 0) { // Check if a file is selected
	  alert('Please select a file.');
	  return;
	} else {
		if (confirm("Delete all Races and all the timestamp?")) {
			if (document.getElementById('inputEntry').files.length > 0) {
				FileSend(document.getElementById('inputEntry'),'entry');
			}
		
			if (document.getElementById('inputGrade').files.length > 0) {
				FileSend(document.getElementById('inputGrade'),'grade');
			}
		
			if (document.getElementById('inputRoute').files.length > 0) {
				FileSend(document.getElementById('inputRoute'),'route');
			}
	
			for (let r = parseInt(document.getElementById("r").innerHTML); r > 0; r--)  {
				RaceButtonDel(r);
				console.log("RaceButtonDel ",r);
			}
		}
	}
	
	console.log("RaceUploadFile ");
	$(document.getElementById('modalFile')).modal('hide');
}

function FileSend(fileInput,table) {
	// Perform database deletion
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			// console.log(this.responseText);
		}
	};
	xmlhttp.open("POST", "racefile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("table=" + table + "&value=" + "DELETE");

	const reader = new FileReader();
	
	reader.onload = function(event) {
		const fileData = event.target.result;
		const lines = fileData.split("\n");
		const heads = lines[0].split(",");
		
		let insertValues = '';
		for (let i = 1; i < lines.length; i++) {
			const currentLine = lines[i].split(",");
			if (currentLine.length !== heads.length) { continue; }
	
			const cells = [];
			for (let j = 0; j < heads.length; j++) {
				cells.push("'" + currentLine[j].trim() + "'");
			}
			insertValues += "(" + cells.join(",") + "),";
		}
		insertValues = insertValues.slice(0, -1);	// Remove the trailing comma
	
		// Perform database insertion here
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				// console.log(this.responseText);
			}
		};
		xmlhttp.open("POST", "racefile.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("table=" + table + "&value=" + insertValues);
	};

	reader.readAsText(fileInput.files[0]); // Read the file
}

function RaceButtonNew(mode) { // @@@@  copy DNC/DSQ
	r = parseInt(document.getElementById("r").innerHTML) +1;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			document.getElementById("i").innerHTML = this.responseText;			// Return what was done
		}
	};
	xmlhttp.open("POST", "racepath.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("r=" + r + "&mode=" + mode);

	console.log("RaceButtonNew ",r," ",mode);
}

function RaceButtonDel(r) {
	if (confirm("Delete Race " + r +" and all the timestamp?")) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				document.getElementById("i").innerHTML = this.responseText;			// Return what was done
			}
		};
		xmlhttp.open("POST", "racepath.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("r=" + r + "&mode=" + "DEL");

		console.log("RaceButtonDel ",r);
	}
}

function RaceButtonSync(b) { // read offset from sql
	var input = null;
	while (input === null || isNaN(input)) {
		input = prompt("Input Time in " + (b === "bOffset" ? "Seconds" : "Minutes"));
		if (input === null) {
			return;
		}
		if (input.indexOf("+") === 0) {
			input = parseFloat(input.substring(1));
		} else if (input.indexOf("-") === 0) {
			input = parseFloat(input.substring(1)) + 90000;
		} else {
			input = parseFloat(input);
		}
	}

	if (!isNaN(input) && input != document.getElementById(b).innerHTML && !(input == 0 && b != "bOffset")) {
		var r = (b === "bOffset") ? 50 : parseInt(document.getElementById("r").innerHTML);
		var m = 50;
		var s = b;
		var t = (b === "bOffset") ? input * 1000 : input * 60 * 1000;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var flag = this.responseText;			// Check how many stamps in r ,m
				console.log("MarkSend ",r," ",m," ",s," ",t," ",flag);
				document.getElementById(b).style.backgroundColor = flag;
			}
		};
		xmlhttp.open("POST", "marksend.php", true);//?param=value
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("r=" + r + "&m=" + m + "&s=" + s + "&t=" + t + "&o=" + 0);
	}
	console.log("RaceButtonSync ", b, " as ", input, (b === "bOffset" ? "Seconds" : "Minutes"));
}

function RaceButtonPath(r,mode,gp,path) { // get new path from input 
	var userInput = prompt("New route:", path);
	if (userInput !== null && userInput !== '' && userInput !== path) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				document.getElementById("i").innerHTML = this.responseText;			// Return what was done
			}
		};
		xmlhttp.open("POST", "racepath.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("r=" + r + "&mode=" + mode + "&gp=" + gp + "&path=" + userInput);

		console.log("RaceButtonPath ",r," ",mode," ",gp," ",userInput);
		setTimeout(function() {
			GetTable("racecurr.php","","tRaceCurr");
			GetTable("racecomp.php","","tRaceComp");
		}, 600);
	}
}

function RaceButtonMode(r,mode) { // get change mode confirmation
	var confirmed = confirm("Change to " + mode + "?");
	if (confirmed) { // change mode
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				document.getElementById("i").innerHTML = this.responseText;			// Return what was done
			}
		};
		xmlhttp.open("POST", "racepath.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("r=" + r + "&mode=" + mode);

		console.log("RaceButtonMode ",r," ",mode);
		setTimeout(function() { //refresh table
			GetTable("racecurr.php","","tRaceCurr");
			GetTable("racecomp.php","","tRaceComp");
		}, 900);
	}
}

function RaceSubmitSail() {
	sOld = document.getElementById('sOld').value;
	sNew = document.getElementById('sNew').value;

	if (sOld === null || sNew === null || sOld === sNew) {
        alert('Please enter valid Sail Numbers.');
        return;
    } else {
		var confirmed = confirm("Confirm to change " + sOld + " to " + sNew + "?");
		if (confirmed) {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					console.log(this.responseText);
					document.getElementById("i").innerHTML = this.responseText;			// Return what was done
				}
			};
			xmlhttp.open("POST", "racesail.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send("sOld=" + sOld + "&sNew=" + sNew );
		}
	}	
	
	console.log("RaceSubmitSail ");
	$(document.getElementById('modalSail')).modal('hide');
	document.getElementById('sOld').value = "";
	document.getElementById('sNew').value = "";
}

function RaceButtonCode(r) {	// get list of s with r,code
	CodeSend(r,'null','DNC','sListDNC');
	CodeSend(r,'null','OCS','sListOCS');
	CodeSend(r,'null','DNS','sListDNS');
	CodeSend(r,'null','RET','sListRET');
	CodeSend(r,'null','DSQ','sListDSQ');
	console.log("RaceButtonCode ",r);
}

function RaceSubmitCode(r) {	// sync code to result with r,s
	CodeSend(r,document.getElementById('sListDNC').value.trim(),'DNC','i');
	CodeSend(r,document.getElementById('sListOCS').value.trim(),'OCS','i');
	CodeSend(r,document.getElementById('sListDNS').value.trim(),'DNS','i');
	CodeSend(r,document.getElementById('sListRET').value.trim(),'RET','i');
	CodeSend(r,document.getElementById('sListDSQ').value.trim(),'DSQ','i');
	console.log("RaceSubmitCode ",r);
	$(document.getElementById('modalCode')).modal('hide');
	setTimeout(function() {
		GetTable("racecomp.php","","tRaceComp");	// refresh count
	}, 600);
}

function CodeSend(r,sList,code,ta) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (sList === 'null') {		// show s in the text area
				console.log("CodeSend Get sList with r =",r," code =",code);
			} else {
				console.log("CodeSend Set ",r," ",sList," as ",code);
			}
		}
	};
	xmlhttp.open("POST", "racecode.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("r=" + r + "&sList=" + sList + "&code=" + code);
}

function TimeButtonEdit(r,cate,s,gppath,startt,sailed) {
	// modal column 1 
	document.getElementById("eTimeRace").innerHTML = r;
	document.getElementById("eTimeCate").innerHTML = cate;
	document.getElementById("eTimeSail").innerHTML = s;
	document.getElementById("eTimePath").innerHTML = '[' +gppath +']';
	
	// modal column 2
	var currentTime = new Date();
	var currentDate = new Date(currentTime.getFullYear(), currentTime.getMonth(), currentTime.getDate());
	var timestamp = '<table><tr><td colspan="2">Timestamp</td><td><input class="btn-sm btn-warning" type="button" style="height:25px; width:20px;" value="+" onclick=\'document.getElementById("eTimeEdit").innerHTML = "Add "; document.getElementById("eTimeMark").value = ""; document.getElementById("eTimeTime").value = "' + TimeHHMMSSXXX(currentDate.getTime()+parseInt(startt)+1) + '"; \'></td></tr>';
	for (var i = 0; i < sailed.length; i++) {
		timestamp += '<tr><td>[' + ((sailed[i].m === '99') ? 'F' : sailed[i].m) + ']</td><td>' 
			+ TimeHHMMSSXXX(currentDate.getTime()+parseInt(sailed[i].t)) + '</td><td>' 
			+ '<input class="btn-sm btn-warning" type="button" style="height:25px; width:20px;" value="-" onclick=\'document.getElementById("eTimeEdit").innerHTML = "Remove "; document.getElementById("eTimeMark").value = "' + ((sailed[i].m === '99') ? 'F' : sailed[i].m) + '"; document.getElementById("eTimeTime").value = "' + TimeHHMMSSXXX(currentDate.getTime()+parseInt(sailed[i].t)) + '"; \'>' 
			+ '<input class="btn-sm btn-warning" type="button" style="height:25px; width:20px;" value="+" onclick=\'document.getElementById("eTimeEdit").innerHTML = "Add "; document.getElementById("eTimeMark").value = ""; document.getElementById("eTimeTime").value = "' + TimeHHMMSSXXX(currentDate.getTime()+parseInt(sailed[i].t)+1) + '"; \'>' 
			+ '</td></tr>';
	}
	timestamp += '</table>';
	document.getElementById("eTimeGrid").innerHTML = timestamp;

	// modal column 3
	document.getElementById("eTimeEdit").innerHTML = "Edit "; 
	document.getElementById("eTimeMark").value = ""; 
	document.getElementById("eTimeTime").value = "";

	console.log("TimeButtonEdit ");
}

function TimeSubmitEdit() {
	var e = document.getElementById("eTimeEdit").innerHTML;
	var r = parseInt(document.getElementById("eTimeRace").innerHTML);
	var m = ((document.getElementById("eTimeMark").value === 'F') ? 99 : parseInt(document.getElementById("eTimeMark").value));
	var s = document.getElementById("eTimeSail").innerHTML;
	var tHMSX = document.getElementById("eTimeTime").value;
	
	// check input
	if (e === "Edit ") {
		alert("Please select +/-");
	} else if (isNaN(m)) {
		alert("Please enter valid Mark");
	} else if (t === null) {
		alert("Please enter valid Time");
	} else {
		var parts = tHMSX.split(":");
		if (parts.length === 4 && parseInt(parts[0]) < 24  && parseInt(parts[1]) < 60  && parseInt(parts[2]) < 60  && parseInt(parts[2]) < 1000) {
			var t = TimeMSX(tHMSX);

			if (e === "Add ") {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var flag = this.responseText;			// Check how many stamps in r ,m
						console.log("MarkSend ",r," ",m," ",s," ",t," ",flag);
					}
				};
				xmlhttp.open("POST", "marksend.php", true);//?param=value
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("r=" + r + "&m=" + m + "&s=" + s + "&t=" + t + "&o=" + 0);
			} else {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var flag = this.responseText;			// Check how many stamps in r ,m
						console.log("MarkUndo ",r," ",m," ",s," ",t," ",flag);
					}
				};
				xmlhttp.open("POST", "markundo.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("r=" + r + "&m=" + m + "&s=" + s + "&t=" + t);
			}

			console.log("TimeSubmitEdit - ",e,"stamp [",r," ",m," ",s," ",t,"]");
			document.getElementById("eTimeEdit").innerHTML = "Edit "; 
			document.getElementById("eTimeMark").value = ""; 
			document.getElementById("eTimeTime").value = "";
			$(document.getElementById('modalTime')).modal('hide');
		} else {
			alert("Invalid time format! (HH:MM:SS:XXX)");
		}
	}
}

var pressTimer;
var isShortPress = true;
function MarkButtonPress(s) {
	m = parseInt(document.getElementById("m").innerHTML);
	if (document.getElementById("m").innerHTML == "S") {m = 91;}
	if (document.getElementById("m").innerHTML == "F") {m = 99;}

	if (event.button === 2) { // right click
		MarkUndo(m,s);
		isShortPress = false;
		return;
	} 

	pressTimer = window.setTimeout(function() {
		isShortPress = false;
		MarkUndo(m,s);
		return;
	}, 1000);
}

function MarkButtonLease(s) {
	m = parseInt(document.getElementById("m").innerHTML);
	if (document.getElementById("m").innerHTML == "S") {m = 91;}
	if (document.getElementById("m").innerHTML == "F") {m = 99;}

	if (isShortPress) {
		MarkSend(m,s);
	}

    clearTimeout(pressTimer);
	isShortPress = true;
}

function MarkButtonCMenu(s) {
	// @@@@ handle context menu event , mark code DNC/DNS/DNF/RET/DSQ...


	return false;
}

function MarkSend(m,s) {
	var r = parseInt(document.getElementById("r").innerHTML);

	// Get t = Unixtimestamp
	var currentTime = new Date(); 
	var currentDate = new Date(currentTime.getFullYear(), currentTime.getMonth(), currentTime.getDate());
	var t = currentTime-currentDate;
	var offset = 1;

	if (m == 91) { // if m = 91 , pop-up box to set start time / NOW / Cancel
		while (document.getElementById("timeS_" + s).innerHTML == "") {	
			var startTime = prompt("Set Start time (HH:MM:SS) or leave blank to Start NOW):");
			if (startTime !== null) {
				var parts = startTime.split(":");
				//currentTime = new Date(); 
				//t = currentTime-currentDate;

				if (startTime === "") {
					//currentTime = new Date(); 
					//t = currentTime-currentDate;
					//offset = 1;	// get current time with offset
					break;
				} else {
					if (parts.length === 3 && parseInt(parts[0]) < 24  && parseInt(parts[1]) < 60  && parseInt(parts[2]) < 60) {
						if (t - TimeMS(startTime) > 10*60*1000) {
							alert("Cannot set more than 10mins earlier!");
						} else if (TimeMS(startTime) - t > 35*60*1000) {
							alert("Cannot set more than 30mins later!");
						} else {
							t = TimeMS(startTime);
							offset = 0;	// get input time without offset
							break;
						}
					} else {
						alert("Invalid time format! (HH:MM:SS)");
					}
				}
			} else {
				return;
			}
		}
	}

	if (m == 0) {						/* if m=0, prompt "Select Mark at the bottom" */
		alert("Select Mark at the bottom");
		return 0;
	} else {							/* else insert stamp*/
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var flag = this.responseText;			// Check how many stamps in r ,m
				console.log("MarkSend ",r," ",m," ",s," ",t," ",offset," ",flag);
				document.getElementById("b"+s).style.backgroundColor = flag;
			}
		};
		xmlhttp.open("POST", "marksend.php", true);//?param=value
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("r=" + r + "&m=" + m + "&s=" + s + "&t=" + t + "&o=" + offset);
	}
	return t;
}

function MarkUndo (m,s) {
	var r = parseInt(document.getElementById("r").innerHTML);
	
	if (m == 0) {						/* if m=0, warning "Select Mark at the bottom" */
		alert("Select Mark at the bottom");
	} else {							/* else insert stamp*/
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var flag = this.responseText;			// Check how many stamps in r ,m
				console.log("MarkUndo ",r," ",m," ",s," ",9999," ",flag);
				document.getElementById("b"+s).style.backgroundColor = flag;
			}
		};
		xmlhttp.open("POST", "markundo.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("r=" + r + "&m=" + m + "&s=" + s + "&t=" + 9999);
	}
}

function TimeHHMMSSXXX(time) { // for time display only
	// Unixtimestamp to "HH:MM:SS:XXX"
	var t = new Date(time);
	return  ("0" + t.getHours()).slice(-2) + ":" + 
			("0" + t.getMinutes()).slice(-2) + ":" + 
			("0" + t.getSeconds()).slice(-2) + ":" + 
			("00" + t.getMilliseconds()).slice(-3);
}

function TimeHHMMSS(time) { // for time calculation only
	// Unixtimestamp to "HH:MM:SS"
	var t = new Date(time);
	return 	("0" + t.getHours()).slice(-2) + ":" + 
			("0" + t.getMinutes()).slice(-2) + ":" + 
			("0" + t.getSeconds()).slice(-2);
}

function TimeMS(t) {
	// "HH:MM:SS" to Unixtimestamp
	var parts = t.split(":");
 	return 	parseInt(parts[0],10)*60*60*1000 + 
			parseInt(parts[1],10)*60*1000 + 
			parseInt(parts[2],10)*1000;
}

function TimeMSX(t) {
	// "HH:MM:SS:XXX" to Unixtimestamp
	var parts = t.split(":");
 	return 	parseInt(parts[0],10)*60*60*1000 + 
			parseInt(parts[1],10)*60*1000 + 
			parseInt(parts[2],10)*1000 + 
			parseInt(parts[3],10);
}

var RankExportFile = (function() {
	var uri = 'data:application/vnd.ms-excel;base64,'
	  , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
	  , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
	  , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
	return function(table, name) {
	  if (!table.nodeType) table = document.getElementById(table)
	  var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
	  window.location.href = uri + base64(format(template, ctx))
	}
})()