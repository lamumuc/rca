function initView() {
	const now = getNowDateTime();

}


function json2Table () {
	mastertable.forEach( (element) => {
		$('#masterTable').append(
			`<tr>
				<td>${element["Device Name"]}</td>
				<td>${element["DEV_EUI"]}</td>
				<td>${element["F/W Ver."]}</td>
				<td>${element["Location"]}</td>
				<td>${element["Floor"]}</td>
				<td>${element["Relocated Parking Space"]}</td>
				<td>${element["Position in parking Space"]}</td>
			</tr>`)
		}
	)
}

async function csv2Table () {
	$.ajax({
		type: "GET",
		url: "./rdb/mastertable.csv",
		dataType: "text",
		success: function(csv) {
			var data = $.csv.toObjects(csv);
			$('#masterTable').empty();
			data.forEach( (element) => {
				$('#masterTable').append(
					`<tr>
						<td>${element["Device Name"]}</td>
						<td>${element["DEV_EUI"]}</td>
						<td>${element[" F/W Ver. "]}</td>
						<td>${element["Location"]}</td>
						<td>${element["Floor"]}</td>
						<td>${element["Relocated Parking Space"]}</td>
						<td>${element["Position in parking Space"]}</td>
					</tr>`)
				}
			)
		},
		error: function (jqXHR, textStatus, errorThrown) { console.log(jqXHR, textStatus, errorThrown) }
	});
	console.log(data.DEV_EUI)
}


async function uploadFile () {
	var inputCSV = $('#inputCSV').prop('files');
	console.log(inputCSV[0])
	if (inputCSV[0] != undefined) {
		if ($('#inputPassword1').val() == ''){
			let formData = new FormData(); 
			formData.append("file", inputCSV[0]);
			await fetch('./upload.php', {
				method: "POST", 
				body: formData
			}); 
			csv2Table()
			alert('Master Table has been updated successfully.');
			$('#exampleModal').modal('hide');
		} else {
			alert('Wrong password.');
			$('#exampleModal').modal('hide');
		}
	} else {
		alert('No file selected.');
		$('#exampleModal').modal('hide');
	}

}



const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

function getNowDateTime () {
	const currentTimeStamp = new Date();
	let month = months[currentTimeStamp.getMonth()];
	let day = currentTimeStamp.getDate();
	let hour = currentTimeStamp.getHours();
	let minute = `0${currentTimeStamp.getMinutes()}`;
	minute = minute.slice(-2);
	return `${day}-${month} ${hour}:${minute}`
}

function getNowDateTimeCustomFormat () {
	const currentTimeStamp = new Date();
	let year = currentTimeStamp.getYear() - 100;
	let month = currentTimeStamp.getMonth() + 1;
	month = (`0${month}`).slice(-2);
	let day = currentTimeStamp.getDate();
	day = (`0${day}`).slice(-2);
	let hour = currentTimeStamp.getHours();
	let minute = `0${currentTimeStamp.getMinutes()}`;
	minute = minute.slice(-2);
	return `20${year}-${month}-${day}T${hour}:${minute}`
}




$(document).ready(function(){
	csv2Table();
	// json2Table();

	$("#myInput").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#masterTable tr").filter(function() {
		  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
	
	dateTimeString = getNowDateTimeCustomFormat()
	$("#utcDateTimePicker").val(dateTimeString);
});

