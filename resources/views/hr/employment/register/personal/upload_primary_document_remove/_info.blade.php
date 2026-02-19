@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @foreach($post_document_type as $item)
        <label for="file_{{ $item->id }}">{{ $item->document_type->caption }}</label>
        <input style="margin:15px" type="file" name="file_{{ $item->id }}" required>

<br/>
    @endforeach

<br/>

@endsection
