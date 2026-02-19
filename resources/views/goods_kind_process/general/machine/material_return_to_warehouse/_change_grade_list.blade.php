@if($modification_temp->change_grades()->count() > 0)
    <div class="col-md-12 center">
        <h5>لیست کالاهای تغییر درجه داده شده</h5></div>
    <div class="col-md-12">
        <table class="table table-styling center">
            <tbody>
            @php $row=0;@endphp
            @foreach($modification_temp->change_grades as $modification_change_grade )
                <tr>
                    <td>{{++$row}}</td>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                {{$modification_change_grade->product->fullCaption()}}
                            </div>
                            <div class="col-md-1"> وزن ناخالص:
                                {{$modification_change_grade->gross_weight}}
                            </div>
                            @if(!isset($before_degree) && isset($degree_for_products[$modification_change_grade->product_id]))
                            <div class="col-md-1"> درجه قبلی:
                                {{ implode(',',$degree_for_products[$modification_change_grade->product_id])}}
                            </div>
                            @endif
                                <div class="col-md-1"> درجه فعلی:
                                    {{$modification_change_grade->degree->caption}}
                                </div>

                            <div class="col-md-1" title="{{$modification_change_grade->packing_type->caption??""}}">نوع
                                بسته بندی:
                                <span
                                > {{$modification_change_grade->packing_type->code??""}}</span>
                            </div>
                            @if(isset($modification_change_grade->packing_type->first_packing_type))
                                <div class="col-md-1">تعداد بسته بندی فرعی:
                                    {{$modification_change_grade->sub_packing_form_number}}
                                </div>
                            @endif
                        </div>


                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
