
<div class="col-md-12 " id="docrowdelete_<?=$id?>">
<input type="hidden" name="doc_id_<?=$id?>" value="0" id="doc_id_<?=$id?>">
    <div class="col-md-5 col-sm-5">
        <div class="form-group" id="documentnumber1_div">
            <div class="col-md-12 pr-xs pl-xs">
                <input id="documentname_<?=$id?>" name="documentname_<?=$id?>" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
            </div>
        </div>
    </div>
    <div class="col-md-5 col-sm-5">
        <div class="form-group" id="docfile_div">
            <div class="col-md-12 pr-xs pl-xs">
                <input type="hidden" id="isvaliddocfile" value="0">
                <input type="hidden" name="olddocfile_<?=$id?>" id="olddocfile" value="">
                <div class="input-group" id="fileupload1">
                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                        <span class="btn btn-primary btn-raised btn-file"><i
                                class="fa fa-upload"></i>
                            <input type="file" name="docfile_<?=$id?>"
                                class="docfile" id="docfile"
                                accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile')">
                        </span>
                    </span>
                    <input type="text" readonly="" id="Filetextdocfile"
                        class="form-control documentrow" placeholder="Enter File" name="Filetextdocfile_<?=$id?>" value="">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-2">
        <div class="form-group" style="float:left; margin:13px 50px 20px 20px;">
            <button type="button" onclick="removedata('docrowdelete_<?=$id?>')" class="addprodocitem btn-danger"><i class="fa fa-minus"></i></button>
        </div>
    </div>
</div>
