<div class="tab-pane fade p-2 show active" id="tabRace" role="tabpanel" aria-labelledby="navRace">
    <div class="my-1 d-flex flex-row-reverse justify-content-between">
        <div class="my-1 d-flex flex-row-reverse">
            <button class="btn btn-secondary btn-sm" type="button" style="height: 70%;" onclick="RaceButtonSync('bOffset')" id="bOffset"
            data-toggle="tooltip" data-placement="top" title="Set time offset in Seconds">#</button>&nbsp;
            <button class="btn btn-secondary btn-sm" type="button" style="height: 70%;" onclick="RaceButtonNew('RM')"
            data-toggle="tooltip" data-placement="top" title="Start New Round Mark Race">New RM</button>&nbsp;
            <img src="orca.png" width="30" height="30" style="transform: scaleX(-1) rotate(-45deg);" onload="initRace()">&nbsp;
        </div>
        <div id="container"><h2 class="p-2" id="timeCurrent">CURRENT TIME</h2></div>&nbsp;
        <div id="container"><h1 class="p-1">CURRENT RACE</h1></div>
    </div>
    <table class="table text-center" id="tRaceCurr" style="table-layout: fixed;"></table>

    <div id="container" style="width: 100%;"><h1 class="p-1">COMPLETED RACE</h1></div>
    <table class="table text-center" id="tRaceComp" style="table-layout: fixed;"></table>

    <br>
    <div class="d-flex justify-content-end">
		<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalSail">Change Sail</button>&nbsp;
        <button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalFile">Import Entry</button>&nbsp;
    </div>
    <div class="d-flex flex-column justify-content-end">
        <div class="d-flex justify-content-end">
            <div>Current Race ></div>
            <div id="r" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
            <div>At Mark ></div>
            <div id="m" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
        </div>
    </div>

    <div class="modal fade" id="modalFile" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import CSV for Entry / Class / Route</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="inputEntry">Entry</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="inputEntry" aria-label="Upload"
                                        accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <h6><a href="./entry.csv" download>Download Template</a></h6>
                                </div>
                            </div>
                            <div class="form-group col-4">
                                <label for="inputGrade">Class</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="inputGrade" aria-label="Upload"
                                        accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <h6><a href="./class.csv" download>Download Template</a></h6>
                                </div>
                            </div>
                            <div class="form-group col-4">
                                <label for="inputRoute">Route</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="inputRoute" aria-label="Upload"
                                        accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <h6><a href="./route.csv" download>Download Template</a></h6>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="RaceUploadFile('RM')">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSail" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Sail Number</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-6">
								<label for="sOld">Old Sail Number</label>
                                <textarea type="text" class="form-control" rows="1" id="sOld" placeholder="Enter old sail number"></textarea>
                            </div>
                            <div class="form-group col-6">
								<label for="sNew">New Sail Number</label>
                                <textarea type="text" class="form-control" rows="1" id="sNew" placeholder="Enter new sail number"></textarea>
							</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="RaceSubmitSail()">Submit</button>
                </div>
            </div>
        </div>
    </div>
	
    <div class="modal fade" id="modalCode" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCodeTitle"></h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-4" >
                                <label for="sListDNC">DNC</label>
                                <textarea type="text" class="form-control" rows="5" id="sListDNC" placeholder="Enter sail number separated by Enter"></textarea>
                            </div>
                            <div class="form-group col-4">
                                <label for="sListOCS">OCS</label>
                                <textarea type="text" class="form-control" rows="5" id="sListOCS" placeholder="Enter sail number separated by Enter"></textarea>
                            </div>
                            <div class="form-group col-4">
                                <label for="sListDNS">DNS</label>
                                <textarea type="text" class="form-control" rows="5" id="sListDNS" placeholder="Enter sail number separated by Enter"></textarea>
                            </div>                    
                        </div>
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="sListRET">RET</label>
                                <textarea type="text" class="form-control" rows="5" id="sListRET" placeholder="Enter sail number separated by Enter"></textarea>
                            </div>
                            <div class="form-group col-4">
                                <label for="sListDSQ">DSQ</label>
                                <textarea type="text" class="form-control" rows="5" id="sListDSQ" placeholder="Enter sail number separated by Enter"></textarea>
                            </div>
                            <div class="form-group col-4">
                            </div>                    
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" 
                        onclick="RaceSubmitCode(
                            parseInt(document.getElementById('modalCodeTitle').innerHTML.charAt(document.getElementById('modalCodeTitle').innerHTML.length - 1)))"
                        >Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (document.getElementById("navRace")) {
        document.getElementById("navRace").classList.add("active");
    }
</script>
