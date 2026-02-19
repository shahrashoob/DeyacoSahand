
@extends('layouts.admin._master')


@section("content")
    <div class="row">

        <div class="col-sm-12">
           
            @foreach ($menu_type as $item)

            <div class="card">

                <div class="card-header">
                    <h5> {{$item->caption}}  </h5>
                    <div class="card-header-right">
                        
                    </div>
                </div>
                <div class="card-block">

                    
                   
                </div>


            </div>
                
            @endforeach
            

        </div>

    </div>

@endsection
@section("styles")
   
@endsection





