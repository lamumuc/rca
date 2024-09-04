<div class="tab-pane fade p-2 show active" id="tabMark" role="tabpanel" aria-labelledby="navMark">
    <div class="my-1 d-flex justify-content-between align-items-end">
        <div id="container" style="width: 52%" class="my-1 d-flex justify-content-between">
            <h1 class="p-1">SAIL LIST</h1>
        </div>
        <div style="width: 40%" >
            <h6 class="text-sm">* One touch to Mark <br>* Long touch to UnMark</h6>
        </div>
    </div>
	<table class="table table-dark" id="tMark"></table>
    <br>
    <img src="orca.png" width="30" height="30" style="transform: scaleX(-1) rotate(-45deg);" style="opacity: 0.5;" onload="initMark()"> Select >  
    <input class="btn btn-info" type="button" style="width:10%;" value="1 Mark" onclick='document.getElementById("m").innerHTML = "1"; GetTable("marksail.php", "m=" + 1, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="2 Mark" onclick='document.getElementById("m").innerHTML = "2"; GetTable("marksail.php", "m=" + 2, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="3 Mark" onclick='document.getElementById("m").innerHTML = "3"; GetTable("marksail.php", "m=" + 3, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="4 Mark" onclick='document.getElementById("m").innerHTML = "4"; GetTable("marksail.php", "m=" + 4, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="5 Mark" onclick='document.getElementById("m").innerHTML = "5"; GetTable("marksail.php", "m=" + 5, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="6 Mark" onclick='document.getElementById("m").innerHTML = "6"; GetTable("marksail.php", "m=" + 6, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="7 Mark" onclick='document.getElementById("m").innerHTML = "7"; GetTable("marksail.php", "m=" + 7, "tMark");'>
    <input class="btn btn-info" type="button" style="width:10%;" value="Finish" onclick='document.getElementById("m").innerHTML = "F"; GetTable("marksail.php", "m=" + 99, "tMark");'>
    <br><br>
    <div class="d-flex flex-column justify-content-end">
        <div class="d-flex justify-content-end">
            <div>Current Race ></div>
            <div id="r" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
            <div>At Mark ></div>
            <div id="m" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
        </div>
    </div>
</div>

<script>
    if (document.getElementById("navMark")) {
        document.getElementById("navMark").classList.add("active");
    }
</script>
