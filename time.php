<div class="tab-pane fade p-2 show active" id="tabTime" role="tabpanel" aria-labelledby="navTime">
    <div id="container"  class="my-1 d-flex flex-row-reverse justify-content-between align-items-end" style="width: 100%;">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-sm btn-dark">
                <input type="radio" name="viewOpts" value="viewMark" autocomplete="off"> Mark
            </label>
            <label class="btn btn-sm btn-dark" active>
                <input type="radio" name="viewOpts" value="viewCate" autocomplete="off" checked> Class
            </label>
        </div>
        <div class="my-1 d-flex flex-row">
            <img src="orca.png" class="align-self-center" width="30" height="30" style="transform: scaleX(-1) rotate(-45deg);" onload="initTime()">
            <h1 class="p-1">TIME STAMPS</h1>
        </div>
    </div>
    <table class="table" id="tTimeTool"></table>
    <table class="table" id="tTimeView"></table>
    <div class="d-flex flex-column justify-content-end">
        <div class="d-flex justify-content-end">
            <div>Current Race ></div>
            <div id="r" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
            <div>At Mark ></div>
            <div id="m" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
        </div>
    </div>
    
    <div class="modal fade" id="modalTime" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit timestamp for this Sail</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="d-flex align-self-center" >
                            <div class="col  align-self-center" >
                                <div class="row">
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 30%;">Race: </div>
                                        <div id="eTimeRace" style="width: 70%; font-size: 2vw;"></div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 30%;">Class: </div>
                                        <div id="eTimeCate" style="width: 70%; font-size: 2vw;"></div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 30%;">Sail: </div>
                                        <div id="eTimeSail" style="width: 70%; font-size: 2vw;"></div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 30%;">Route: </div>
                                        <div id="eTimePath" style="width: 70%; font-size: 2vw;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col  align-self-center">
                                <table class="table-sm text-center" id="eTimeGrid" style="width: 100%; table-layout: auto;"></table>
                            </div>
                            <div class="col  align-self-center">
                                <div><span id="eTimeEdit" style="font-weight: bold; color: darkorange;">Edit</span> Timestamp<br></div>
                                <div class="form-row">
                                    <label for="eTimeMark">- Mark</label>
                                    <textarea type="text" class="form-control" rows="1" id="eTimeMark" placeholder="Enter Mark" style="font-weight: bold; color: darkorange;"></textarea>
                                </div>
                                <div class="form-row">
                                    <label for="eTimeTime">- Time</label>
                                    <textarea type="text" class="form-control" rows="1" id="eTimeTime" placeholder="Enter Time HH:MM:SS:XXX" style="font-weight: bold; color: darkorange;"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" 
                        onclick="TimeSubmitEdit()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (document.getElementById("navTime")) {
        document.getElementById("navTime").classList.add("active");
    }
</script>
