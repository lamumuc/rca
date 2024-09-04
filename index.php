<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RCA</title>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
		<script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.mini.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.21/jquery.csv.min.js" crossorigin="anonymous"></script>
		<script src="dbtask.js"></script>
		<meta charset="UTF-8">

		<style>
			body {
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
				font-family: Corbel, sans-serif;
			}
		</style>
		<script>
			document.addEventListener('keydown', function(event) {
			// Check if Ctrl+P is pressed (event.keyCode = 80 for 'P')
			if (event.ctrlKey && event.keyCode === 80) {
				// Prevent the default print action
				event.preventDefault();

				// Copy the table content to the new window
				var table = document.getElementById('tRank');
				if (table) {
					var clonedTable = table.cloneNode(true);

					// Create a new window for printing
					var printWindow = window.open('', '_blank');
					var printHead = printWindow.document.createElement('head');

					// Add link to Bootstrap CSS
					var bootstrapCSS = document.createElement('link');
					bootstrapCSS.rel = 'stylesheet';
					bootstrapCSS.href = 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css';
					printHead.appendChild(bootstrapCSS);

					// Copy the CSS styles to the print window's head
					var styles = document.querySelectorAll('style');
					for (var i = 0; i < styles.length; i++) {
						printHead.appendChild(styles[i].cloneNode(true));
					}

					// Append the print head and cloned table to the print window's body
					printWindow.document.head.appendChild(printHead);

					// Add textBox on top
					var textBox = document.createElement('h1');
					textBox.style.width = '100%';
					textBox.style.textAlign = 'center';
					textBox.style.lineHeight = '100px';
					textBox.textContent = 'Result';
					printWindow.document.body.appendChild(textBox);

					// Append the cloned table to the print window's body
					printWindow.document.body.appendChild(clonedTable);

					// Add textBox at the end
					var textBox = document.createElement('h3');
					textBox.style.width = '100%';
					textBox.style.textAlign = 'right';
					textBox.style.lineHeight = '100px';
					textBox.textContent = 'Race Officer: ____________________　　　Signature: ____________________';
					printWindow.document.body.appendChild(textBox);


					// Initiate printing and close the print window
					//printWindow.print();
					//printWindow.close();
				}
			}});
		</script>
	</head>

	<body class="p-2">
		<div class="my-1 d-flex justify-content-between" style="width: 100%; position: fixed; background-color: #FFFFFF">
			<ul class="nav nav-tabs p-1" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<a href="?p=race" data-bs-toggle="tooltip" data-placement="bottom" textBox="Page for Race Management">
						<button class="nav-link" id="navRace" data-bs-target="#race" aria-selected="false">Race</button>
					</a>
				</li>
				<li class="nav-item" role="presentation">
					<a href="?p=mark" data-bs-toggle="tooltip" data-placement="bottom" textBox="Page for Mark Boats">
						<button class="nav-link" id="navMark" data-bs-target="#mark" aria-selected="false">Mark</button>
					</a>
				</li>
				<li class="nav-item" role="presentation">
					<a href="?p=time" data-bs-toggle="tooltip" data-placement="bottom" textBox="Page for Timestamp Viewer">
						<button class="nav-link" id="navTime" data-bs-target="#time" aria-selected="false">Time</button>
					</a>
				</li>
				<li class="nav-item" role="presentation">
					<a href="?p=rank" data-bs-toggle="tooltip" data-placement="bottom" textBox="Page for Preliminary Result">
						<button class="nav-link" id="navRank" data-bs-target="#rank" aria-selected="false">Rank</button>
					</a>
				</li>
			</ul>
			<div class="d-flex flex-column justify-content-between">
				<div class="d-flex justify-content-between">
					<div>>>></div>
					<div id="i" style="width: 200px; font-size: 1vw; text-align: center;"><?php
      					$c = isset($_GET['c']) ? $_GET['c'] : 'null'; echo $c; ?></div>
				</div>
				<div class="d-flex justify-content-between">
					<div>>>></div>
					<div id="p" style="width: 100px; font-size: 2vw; text-align: center;"><?php
      					$p = isset($_GET['p']) ? $_GET['p'] : 'rank'; echo $p; ?></div>
					<div>>>></div>
					<div id="g" style="width: 100px; font-size: 2vw; text-align: center;"><?php
      					$g = isset($_GET['g']) ? $_GET['g'] : 'demo'; echo $g; ?></div>
				</div>
			</div>
		</div>

		<div style="margin-top: 60px;"><?php include($p.'.php'); ?></div>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	</body>
</html>
