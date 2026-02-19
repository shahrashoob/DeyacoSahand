@foreach($document_receive_step_document_type_list as  $item)

    @if((empty($item->document_type->nationality_id) ||$item->document_type->nationality_id == $employment->nationality_id ))
        <label for="file_{{ $item->document_type_id }}">{{ $item->document_type->caption }}</label>
        <div class="input-group">
            <input class="form-control" maxlength="100" type="file" name="file_{{ $item->document_type_id }}">
        </div>
        <br/>
    @endif

@endforeach
