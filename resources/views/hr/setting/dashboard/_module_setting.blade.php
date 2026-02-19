<div class="row">

    <div class="col-sm-12">


        <div class="table-responsive">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>عنوان</th>
                    <th>آیا حضور فرد در سازمان چک شود؟</th>
                    <th>افرادی که تحویل شیفت دارند،<br/> قبل از تایید تحویل شیفت می توانند از سازمان خارج شوند؟</th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php $row=$list->firstItem();@endphp
                @foreach($list as $item)
                    <tr>
                        <td>{{$row++}}</td>
                        <td>
                            <a href="{{route("hr.setting.dashboard.edit_module",$item)}}">{{$item->caption}}</a>
                        </td>
                        <td>
                            {{$item->presence_in_the_organization_checked?"بله":"خیر"}}
                        </td>
                        <td>
                            {{$item->people_who_have_a_delivery_shift_have_permission_to_exit?"بله":"خیر"}}
                        </td>
                        <td>


                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
        <div class="float-left">
            نمايش رکوردهای
            <b>{{$list->firstItem()}}</b>
            تا
            <b>{{$list->lastItem()}}</b>
            از
            <b>{{$list->total()}}</b>
            رکورد موجود


        </div>

        <div class="text-center">
            {{$list->links('pagination::bootstrap-4')}}
        </div>

    </div>

</div>
