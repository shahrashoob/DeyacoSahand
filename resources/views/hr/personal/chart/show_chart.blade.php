@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> چارت سازمانی  </h5>
                </div>
                <div class="card-block">
                    <div id="tree">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>/*CSS*/

        #tree {
            width: 100%;
            height: 100%;
        }</style>
@endsection

@section("scripts")
    <script src="{{url("assets/plugins/org_chart_js/orgchart.js")}}"></script>
    <script>

        var nodes =  @php echo json_encode($nodes);@endphp ;
        OrgChart.templates.group.min = Object.assign({}, OrgChart.templates.group);
        OrgChart.templates.group.min.name = '<text data-width="230" data-text-overflow="multiline" style="font-size: 18px;font-family: IRANSans" fill="#aeaeae" x="125" y="65" text-anchor="middle">{val}</text>';
        var chart = new OrgChart(document.getElementById("tree"), {
            enableSearch: false,
            mouseScrool: OrgChart.action.none,
            nodeMouseClick: OrgChart.action.none,
            nodeBinding: {
                name: "groupName",
                field_0: "name",
                field_1: "title",
                img_0: "img"
            },
            tags: {
                "node-with-subtrees": {
                    min: true,
                    template: "group",
                    subTreeConfig: {
                        siblingSeparation: 3,
                        columns: 2
                    }
                }
            }
        });

        chart.on('click', function (sender, args) {
            if (args.node.min) {
                sender.maximize(args.node.id);
            } else {
                sender.minimize(args.node.id);
            }
            return false;
        });

        chart.load(nodes);
    </script>
@endsection
