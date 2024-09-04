<div class="tab-pane fade p-2 show active" id="tabRank" role="tabpanel" aria-labelledby="navRank">
    <div class="my-1 d-flex justify-content-between align-items-end">
        <div id="container" style="width: 52%" class="my-1 d-flex justify-content-between">
            <h1 class="p-1">PRELIMINARY RESULT</h1>
            <img src="orca.png" class="align-self-center" width="30" height="30" style="transform: scaleX(-1) rotate(-45deg);" onload="initRank()">
        </div>
        <div id="rE" ><h4 class="text-sm-right"></h4></div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-secondary" type="button" onclick="RankExportFile('tRank','Result')">Export File</button>&nbsp;
        </div>
    </div>

    <table class="table text-center" id="tRank"></table>
    <div class="d-flex flex-column justify-content-end">
        <div class="d-flex justify-content-end">
            <div>Current Race ></div>
            <div id="r" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
            <div>At Mark ></div>
            <div id="m" style="width: 40px; font-size: 2vw; text-align: center;">0</div>
        </div>
    </div>


    <div class="modal fade" id="modalRank" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Rank Points for this Sail</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="d-flex align-self-center" >
                            <div class="col  align-self-center" >
                                <div class="row">
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 30%;">Class: </div>
                                        <div id="eRankCate" style="width: 70%; font-size: 2vw;"></div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 30%;">Sail: </div>
                                        <div id="eRankSail" style="width: 70%; font-size: 2vw;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col  align-self-center">
                                <table class="table-sm text-center" id="eRankGrid" style="width: 100%; table-layout: auto;"></table>
                            </div>
                            <div class="col  align-self-center">
                                <div><span id="eRankEdit" style="font-weight: bold; color: darkorange;">Edit</span> Rank Point<br></div>
                                <div class="form-row">
                                    <label for="eRankRace">- Race</label>
                                    <textarea type="text" class="form-control" rows="1" id="eRankRace" placeholder="Enter Race" style="font-weight: bold; color: darkorange;"></textarea>
                                </div>
                                <div class="form-row">
                                    <label for="eRankRank">- Rank</label>
                                    <textarea type="text" class="form-control" rows="1" id="eRankRank" placeholder="Enter Rank" style="font-weight: bold; color: darkorange;"></textarea>
                                </div>
                                <div class="form-row">
                                    <label for="eRankRaPY">- Rank PY</label>
                                    <textarea type="text" class="form-control" rows="1" id="eRankRaPY" placeholder="Enter Rank PY" style="font-weight: bold; color: darkorange;"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" 
                        onclick="RankSubmitEdit()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    if (document.getElementById("navRank")) {
        document.getElementById("navRank").classList.add("active");
    }
</script>
