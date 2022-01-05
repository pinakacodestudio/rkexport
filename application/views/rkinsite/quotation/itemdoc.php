
<div class="row countdocuments" id="countdocuments">
   <input type="hidden" name="doc_id_<?=$id?>" value="0" id="doc_id_<?=$id?>">
    <div class="col-md-6 col-sm-6">
        <div class="form-group" id="documentnumber1_div">
            <div class="col-md-12 pr-xs pl-xs">
                <input id="documentnumber_<?=$id?>" name="documentnumber_<?=$id?>" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="form-group" id="docfile1_div">
            <div class="col-md-12 pr-xs pl-xs">
                <input type="hidden" id="isvaliddocfile1" value="0">
                <input type="hidden" name="olddocfile" id="olddocfile1" value="">
                <div class="input-group" id="fileupload1">
                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                        <span class="btn btn-primary btn-raised btn-file"><i
                                class="fa fa-upload"></i>
                            <input type="file" name="docfile_<?=$id?>"
                                class="docfile" id="docfile_<?=$id?>"
                                accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile1')">
                        </span>
                    </span>
                    <input type="text" readonly="" id="Filetext1"
                        class="form-control documentrow docfile" placeholder="Enter File" name="Filetextdocfile[1]" value="">
                </div>
            </div>
        </div>
    </div>
</div>
