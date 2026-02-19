<script>

    var dom = document.getElementById('{{$id}}');
    var myChart = echarts.init(dom);
    var app = {};
    var app = {};
    var app = {};
    var option = null;

    @if($type=="radar")

        option = {
        title: {
            text: ''
        },
        tooltip: {},
        toolbox: {
            show: true,
            feature: {
                mark: {
                    show: true
                },
                dataView: {
                    show: true,
                    readOnly: false
                },
                magicType: {
                    show: true,
                    type: ['pie', 'funnel'],
                    option: {
                        funnel: {
                            x: '25%',
                            width: '50%',
                            funnelAlign: 'left',
                            max: 1548
                        }
                    }
                },
                restore: {
                    show: true
                },
                saveAsImage: {
                    show: true
                }
            }
        },
        color: "#F4574D",
        radar: {
            // shape: 'circle',
            name: {
                textStyle: {
                    color: '#fff',
                    backgroundColor: '#3F4D67',
                    borderRadius: 3,
                    padding: [5, 8],
                    fontSize: 15
                }
            },
            indicator: [
                    @foreach($data as $item)
                {
                    name: '{{$item['caption']}}', max: 100
                },
                @endforeach
            ]
        },
        series: [{
            name: '',
            type: 'radar',
            // areaStyle: {normal: {}},
            data: [
                {
                    value: [@foreach($data as $item) '{{$item['value']}}', @endforeach],
                    name: ''
                },

            ]
        }],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };


    @elseif($type=="bar")

        option = {
        tooltip: {
            trigger: 'item',
            formatter: "{c} : {b}"
        },
        toolbox: {
            show: true,
            feature: {
                mark: {
                    show: true
                },
                dataView: {
                    show: true,
                    readOnly: false
                },
                magicType: {
                    show: true,
                    type: ['pie', 'funnel'],
                    option: {
                        funnel: {
                            x: '25%',
                            width: '50%',
                            funnelAlign: 'left',
                            max: 1548
                        }
                    }
                },
                restore: {
                    show: true
                },
                saveAsImage: {
                    show: true
                }
            }
        },
        xAxis: {
            type: 'category',
            data: [@foreach($data as $item) '{{$item['caption']}}', @endforeach]
        },
        yAxis: {
            type: 'value'
        },
        color: '{{$color??"#A389D4"}}',
        series: [{
            data: [@foreach($data as $item) '{{$item['value']}}', @endforeach],
            type: '{{$type}}',
        }],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };

    @elseif($type=="pie")

        option = {
        title: {
            left: 'center',
            text: '{{$title??""}}'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{c} ({d}%) : {b}"
        },
        {{--legend: {--}}
            {{--    orient: 'vertical',--}}
            {{--    x: 'left',--}}
            {{--    data:  [@foreach($data as $item) '{{$item['caption']}}', @endforeach],--}}
            {{--},--}}
        color: ['#f4c22b', '#A389D4', '#3ebfea', '#04a9f5', '#1de9b6'],
        toolbox: {
            show: true,
            feature: {
                mark: {
                    show: false
                },
                dataView: {
                    show: false,
                    readOnly: false
                },
                magicType: {
                    show: false,
                    type: ['pie', 'funnel'],
                    option: {
                        funnel: {
                            x: '25%',
                            width: '50%',
                            funnelAlign: 'left',
                            max: 1548
                        }
                    }
                },
                restore: {
                    show: true
                },
                saveAsImage: {
                    show: true
                }
            }
        },
        calculable: true,
        series: [{
            name: 'Dorna',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [
                    @foreach($data as $item)
                {
                    value: {{$item['value']}},
                    name: "{{$item['caption']}}"
                },
                @endforeach
            ],
        }],
        textStyle: {
            fontFamily: 'IranSans'
        }

    };

    @elseif($type=="pie2")

        option = {

        title: {
            x: 'center',
            text: '{{$title??""}}',
            subtext: '',
        },
        tooltip: {
            trigger: 'item',
            formatter: '{b} : {c} ({d}%)'
        },
        legend: {
            type: 'scroll',
            orient: 'vertical',
            left: 10,
            top: 20,
            bottom: 20,
            data: [@foreach($data as $item) '{{$item['caption']}}', @endforeach],
        },
        color: ['#A389D4', '#3ebfea', '#04a9f5', '#1de9b6'],
        toolbox: {
            show: true,
            feature: {
                mark: {
                    show: true
                },
                magicType: {
                    show: true,
                    type: ['pie', 'funnel'],
                    option: {
                        funnel: {
                            x: '25%',
                            width: '50%',
                            funnelAlign: 'left',
                            max: 1548
                        }
                    }
                },
                restore: {
                    show: false
                },
                saveAsImage: {
                    show: true
                }
            }
        },
        calculable: true,
        series: [{
            name: 'ERP',
            type: 'pie',
            radius: ['50%', '70%'],
            itemStyle: {
                normal: {
                    label: {
                        show: false
                    },
                    labelLine: {
                        show: false
                    }
                },
                emphasis: {
                    label: {
                        show: true,
                        position: 'center',
                        textStyle: {
                            fontSize: '15',
                            fontWeight: 'bold'
                        }
                    }
                }
            },
            data: [
                    @foreach($data as $item)
                {
                    value: {{$item['value']}},
                    name: "{{$item['caption']}}"
                },
                @endforeach
            ],
        }],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };

    @elseif($type=="area")

        option = {
        tooltip: {
            trigger: 'axis'
        },
        toolbox: {
            show: false,
            feature: {
                mark: {
                    show: true
                },
                dataView: {
                    show: true,
                    readOnly: false
                },
                magicType: {
                    show: true,
                    type: ['line', 'bar', 'stack', 'tiled']
                },
                restore: {
                    show: true
                },
                saveAsImage: {
                    show: true
                }
            }
        },
        calculable: true,
        xAxis: [{
            type: 'category',
            splitLine: {
                show: false
            },
            boundaryGap: false,
            data: [@foreach($data as $item) '{{$item['caption']}}', @endforeach],
        }],
        color: ["rgba(163, 137, 212, 0.5)", "rgba(4, 169, 246, 0.5)", "rgba(28, 233, 181, 0.5)"],
        yAxis: [{
            type: 'value',
            splitLine: {
                show: false
            }
        }],
        series: [{
            name: 'abc',
            type: 'line',
            smooth: true,
            itemStyle: {
                normal: {
                    areaStyle: {
                        type: 'macarons'
                    }
                }
            },
            data: [@foreach($data as $item) '{{$item['value']}}', @endforeach],
        }],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };

    @elseif($type=="multi_line")

        option = {
        tooltip: {
            trigger: 'axis'
        },
        toolbox: {
            show: false,
            feature: {
                mark: {
                    show: true
                },
                dataView: {
                    show: true,
                    readOnly: false
                },
                magicType: {
                    show: true,
                    type: ['line', 'bar', 'stack', 'tiled']
                },
                restore: {
                    show: true
                },
                saveAsImage: {
                    show: true
                }
            }
        },
        calculable: true,
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [{
            type: 'category',
            splitLine: {
                show: false
            },
            boundaryGap: false,
            data: [@foreach($data as $item) '{{$item['caption']}}', @endforeach],
        }],
        color: ["rgba(163, 137, 212, 0.5)", "rgba(4, 169, 246, 0.5)", "rgba(28, 233, 181, 0.5)"],
        yAxis: [{
            type: 'value',
            splitLine: {
                show: false
            }
        }],
        series: [
                @foreach($series as $series_item)
            {
                name: '{{$series_item["name"]}}',
                type: 'line',
                smooth: true,
                itemStyle: {
                    normal: {
                        areaStyle: {
                            type: 'macarons'
                        }
                    }
                },
                data: [@foreach($series_item["data"] as $item) '{{$item['value']}}', @endforeach],
            },
            @endforeach
        ],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };


    @elseif($type=="bar_stack")

        option = {
        legend: {
            orient: 'vertical',
            left: 'button'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross'
            },
        },
        color: [
            '#0076BC',
            '#F14B25',
            '#FEE2B6',
            '#863A20',
            '#F16829',
            '#6EBF49',
            '#ACE0EE',
            '#262A67',
            '#ECD798',
            '#75613C',
            '#E45626',
            '#6DA9DB',
            '#122047',
            '#FAB68F',
            '#93562A',
            '#54585B',
            '#0076BC',
            '#F3A71D',
            '#483628',
            '#8E8D88',
            '#0076BC',
            '#765CA6',
            '#D19229',
            '#CADB2A',
            '#F0483F',
            '#B9BCB3',
            '#5E84C3',
            '#853594',
            '#FBB15C',
            '#FAE347',
            '#CF3438',
            '#D9D5C9',
            '#59622B',
            '#0070BA',
            '#4A2366',
            '#C6883B',
            '#FFD102',
            '#CF202F',
            '#FFFFFF',
            '#9ED4B2',
            '#17468C',
            '#E5B7D4',
            '#B97729',
            '#FDB525',
            '#AB1E2E',
            '#71C498',
            '#232D6A',
            '#008DA9',
            '#ACE0EE',
            '#0076BC',
            '#5E84C3',
            '#0070BA',
            '#F17FB2',
            '#AD4625',
            '#F4A81E',
            '#7A242F',
            '#008DA9',
            '#01528A',
            '#B41F85',
            '#D6A361',
            '#F58320',
            '#F15B5D',
            '#6DB33F',
        ],
        calculable: true,
        grid: {
            top: '15%',
            left: '30%',
            right: '10%',
            bottom: '10%',
            containLabel: true
        },
        xAxis: [
            {
                type: 'category',
                data: [@foreach($data as $item) '{{$item}}', @endforeach],
                axisLabel: { interval: 0, rotate: 60 }
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: [
                @foreach($series as $series_item)
            {
                name: '{{$series_item["name"]}}',
                type: 'bar',
                stack: 'Ad',
                emphasis: {
                    focus: 'series'
                },
                data: [@foreach($series_item["data"] as $item) '{{$item}}', @endforeach],
            },
            @endforeach
        ],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };


    @elseif($type=="bar_stack2")

    const posList = [
        'left',
        'right',
        'top',
        'bottom',
        'inside',
        'insideTop',
        'insideLeft',
        'insideRight',
        'insideBottom',
        'insideTopLeft',
        'insideTopRight',
        'insideBottomLeft',
        'insideBottomRight'
    ];
    app.configParameters = {
        rotate: {
            min: -90,
            max: 90
        },
        align: {
            options: {
                left: 'left',
                center: 'center',
                right: 'right'
            }
        },
        verticalAlign: {
            options: {
                top: 'top',
                middle: 'middle',
                bottom: 'bottom'
            }
        },
        position: {
            options: posList.reduce(function (map, pos) {
                map[pos] = pos;
                return map;
            }, {})
        },
        distance: {
            min: 0,
            max: 100
        }
    };
    app.config = {
        rotate: 90,
        align: 'left',
        verticalAlign: 'middle',
        position: 'insideBottom',
        distance: 60,
        onChange: function () {
            const labelOption = {
                rotate: app.config.rotate,
                align: app.config.align,
                verticalAlign: app.config.verticalAlign,
                position: app.config.position,
                distance: app.config.distance
            };
            myChart.setOption({
                series: [
                    {
                        label: labelOption
                    },
                    {
                        label: labelOption
                    },
                    {
                        label: labelOption
                    },
                    {
                        label: labelOption
                    }
                ]
            });
        }
    };
    const labelOption = {
        show: true,
        position: app.config.position,
        distance: app.config.distance,
        align: app.config.align,
        verticalAlign: app.config.verticalAlign,
        rotate: app.config.rotate,
        formatter: '',
        fontSize: 16,
        fontFamily: 'IranSans',
        rich: {
            name: {}
        }
    };
    option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        toolbox: {
            show: false,

        },
        xAxis: [
            {
                type: 'category',
                axisTick: {show: false},
                data: [@foreach($data as $item) '{{$item['caption']}}', @endforeach],
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        // color: ['#5470C6', '#91CC75', '#FAC858', '#EE6666', '#73C0DE',"#3BA272","#FC8452"],
        color: ['#A389D4', '#3ebfea', '#04a9f5', '#1de9b6', '#f4c22b',],

        series: [
                @foreach($series as $series_item)
            {
                name: '{{$series_item["name"]}}',
                type: 'bar',
                stack: 'total',
                barGap: 0,
                label: labelOption,
                emphasis: {
                    focus: 'series'
                },
                data: [@foreach($series_item["data"] as $item) '{{$item}}', @endforeach],
            },
            @endforeach
        ]
    };
    @else
        option = {
        tooltip: {
            trigger: 'axis'
        },
        xAxis: {
            type: 'category',
            data: [@foreach($data as $item) '{{$item['caption']}}', @endforeach]
        },
        yAxis: {
            type: 'value'
        },
        color: "#04A9F5",
        series: [{
            data: [@foreach($data as $item) '{{$item['value']}}', @endforeach],
            type: '{{$type}}',
        }],
        textStyle: {
            fontFamily: 'IranSans'
        }
    };

    @endif

    myChart.setOption(option, true);
</script>
