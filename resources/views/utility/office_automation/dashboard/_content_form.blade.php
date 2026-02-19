<div class="row">
    <div class="col-md-12">
        <label>توضیحات</label>
        <textarea class="form-control max-textarea"
                  name="description"
                  maxlength="255"
                  rows="4"></textarea>
        <br/>
    </div>
    @include("component.input._file_upload",["id"=>"work_file","label"=>"فایل (ها)","value"=>"","class_col"=>"col-md-12","multiple"=>1])

    @include("component.input._hidden",["id"=>$hidden_id,"value"=>0])
    <div class="col-md-12">
        <a class="btn  md-close btn-dark text-white ">انصراف </a>
        <button type="submit" class="btn btn-success" style="display: inline"> ثبت</button>
    </div>
</div>
