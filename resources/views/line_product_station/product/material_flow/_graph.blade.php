

    <div id="mf">
        <div id="graph" style="width: 100%; height: {{min(50+count($option["series"][0]["data"])*160,65000)}}px; ">
        </div>
            <a href="{{route($route_path."index",($product_creation_process?$product_creation_process:$product))}}" class="btn btn-outline-dark">بازگشت</a>


    </div>
    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>
    <script>

        var chartDom = document.getElementById('graph');
        var myChart = echarts.init(chartDom);
        var option;
        var source = false;
        var target = false;

        option = {!! json_encode($option) !!};
        myChart.setOption(option);

        myChart.on('click', function (params) {

            if (params['value']) {
                var source_list = params['value'].split("bom_item_")
                if (source_list[1]) {
                    source = source_list[1];
                    target = false;

                }
                var target_list = params['value'].split("band_code_")
                if (target_list[1] && source) {
                    target = target_list[1];
                    addLink()

                }

            }


        });


        function addLink() {

            request = $.ajax({
                url: "{{url("api/material_flow/add_link/".$product->id."/".$bom->id."/".$line_product_station->id)}}",
                type: "post",
                data: {
                    "source": source,
                    "target": target,
                    "route_path":"{{$route_path}}",
                    "product_creation_process_id":"{{$product_creation_process->id??0}}"
                }
            });
            request.done(function (response, textStatus, jqXHR) {
                $("#mf").html(response);

            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown)
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }
    </script>

