<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class GoodsKindPropertyController extends Controller {

    private $view_path = "line_product_station.goods_kind.property.";
    private $route_path = "line_product_station.goods_kind.property.";
    private $count_of_integer_compare = 4;

    public function index( GoodsKind $goods_kind ) {

        $goods_kind_property_list = $goods_kind->property()->whereNotNull( "parent_id" )->get();
        foreach ( $goods_kind_property_list as $item ) {

            if ( $item->dependent_values()->count() == 0 ) {
                $item->parent_id = null;
                $item->save();
            }
        }


         $goods_kind_lot_number_property=GoodsKind\GoodsKindLotNumberProperty::where("goods_kind_id",$goods_kind->id)->get();

        return view( $this->view_path . "index", compact( "goods_kind","goods_kind_lot_number_property" ) );
    }

    public function create( GoodsKind $goods_kind ) {
        $field_type_option   = Option::get( "field_type" );
        $special_unit_option = Option::get( "special_unit" );

        $dependent_property_option = Option::get(
            "goods_kind_dependent_property",
            0,
            0,
            $goods_kind->id
        );

        return view( $this->view_path . "create", compact( "goods_kind", "field_type_option", "special_unit_option", "dependent_property_option" ) );
    }

    public function store( Request $request, GoodsKind $goods_kind ) {

        if ( GoodsKindProperty::ExistsCode( $goods_kind->id, $request->caption ) ) {
            return back()->withErrors( "عنوان " . $request->caption . " تکراری است " );
        }

        $request["goods_kind_id"] = $goods_kind->id;
        $goods_kind_property      = GoodsKindProperty::create( $request->all() );

        if ( $request->parent_id ) {
            return redirect()->route( $this->view_path . "edit_property_dependent", compact( "goods_kind_property" ) )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد، لطفا حالت های فعال را ثبت نمایید." ] );

        }

        return redirect()->route( $this->view_path . "index", compact( "goods_kind" ) )->with( [ "success" => "یک مشخصه با موفقیت اضافه گردید، لطفا برای تکمیل تعریف، گزینه های انتخاب و وابستگی ها را تکیمل نمایید." ] );

    }

    public function edit( GoodsKind $goods_kind, GoodsKindProperty $goods_kind_property ) {
        $status_option       = Option::get( "active_status", $goods_kind_property->status_id );
        $field_type_option   = Option::get( "field_type", $goods_kind_property->field_type_id );
        $special_unit_option = Option::get( "special_unit", $goods_kind_property->special_unit_id );

        $dependent_property_option = Option::get(
            "goods_kind_dependent_property",
            $goods_kind_property->parent_id,
            $goods_kind_property->id,
            $goods_kind->id
        );

        return view( $this->view_path . "edit", compact( "goods_kind", "goods_kind_property", "field_type_option", "special_unit_option", "dependent_property_option", "status_option" ) );
    }

    public function update( Request $request, GoodsKind $goods_kind, GoodsKindProperty $goods_kind_property ) {


        if ( $request->status_id == 1210 ) {
            $list = GoodsKindProperty::where( [ "status_id" => 1200, "parent_id" => $goods_kind_property->id ] )->get();
            if ( count( $list ) > 0 ) {
                $error = "مشخصه های زیر به " . ( $goods_kind_property->caption ) . " وابسته و فعال هستند، و نمی توان این مشخصه را غیرفعال کرد" . "<br/>";
                foreach ( $list as $item ) {
                    $error .= "-" . $item->caption . "<br/>";
                }

                return back()->withErrors( $error );
            }
        }

        if ( GoodsKindProperty::ExistsCode( $goods_kind->id, $request->caption, $goods_kind_property->id ) ) {
            return back()->withErrors( "عنوان " . $request->caption . " تکراری است " );
        }

        $parent = GoodsKindProperty::where( "id", $request->parent_id )->first();
        if ( $parent && ! in_array( $parent->field_type_id, [ 1, 3, 5 ] ) ) {
            return back()->withErrors( "مشخصه وابسته از موارد مجاز نیست" );
        }
        if ( $goods_kind_property->parent_id != $request->parent_id ) {
            GoodsKind\GoodsKindPropertyDependentValue::where(
                [
                    "goods_kind_property_id"        => $goods_kind_property->id,
                    "goods_kind_property_parent_id" => $goods_kind_property->parent_id
                ] )->delete();
        }
        if ( $goods_kind_property->field_type_id != $request->field_type_id ) {
            GoodsKindPropertyOption::where( "goods_kind_property_id", $goods_kind_property->id )->delete();
        }
        $goods_kind_property->update( $request->all() );

        if ( $request->parent_id ) {
            return redirect()->route( $this->view_path . "edit_property_dependent", compact( "goods_kind_property" ) )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد، لطفا حالت های فعال را ثبت نمایید." ] );

        }

        return redirect()->route( $this->view_path . "index", compact( "goods_kind" ) )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }

    public function destroy( GoodsKind $goods_kind, GoodsKindProperty $goods_kind_property ) {

        $dependent_property = GoodsKindProperty::where( "parent_id", $goods_kind_property->id )->first();
        if ( $dependent_property ) {
            return back()->withErrors( "با توجه به اینکه این مشخصه، جزء مشخصه های وابسته <b>" . $dependent_property->caption . "</b> می باشد، امکان حذف آن وجود ندارد." );
        }


        if ( $goods_kind_property->field_type_id == 3 ) {
            $list = GoodsKindPropertyValue::
            where( "goods_kind_property_id", $goods_kind_property->id )->
            where( "value", "!=", 0 )->
            get();
        } else {
            $list = GoodsKindPropertyValue::
            where( "goods_kind_property_id", $goods_kind_property->id )->
            where( "value", "!=", "" )->
            get();
        }
        if ( $goods_kind->id == $goods_kind_property->goods_kind_id &&
             count( $list ) == 0
        ) {
            $goods_kind_property->delete();

            return back()->with( [ "success" => "مشخصه با موفقیت حذف شد" ] );
        } else {
            $message = "";
            foreach ( $list as $item ) {
                $message .= "<br/>" . "این مشخصه برای  " . ( $item->product->caption ?? "***" ) . " مقدار دهی شده است. ";
            }

            return back()->withErrors( $message . "به دلیل استفاده شدن در جداول، امکان حذف مشخصه وجود ندارد" );
        }
    }

    public function edit_product_type( GoodsKind $goods_kind, GoodsKindProperty $goods_kind_property ) {

        $product_type_list = $goods_kind_property->property_product_type->pluck( "product_type_id" )->toArray();

        return view( $this->view_path . "edit_product_type", compact( "goods_kind", "goods_kind_property", "product_type_list" ) );

    }

    public function update_product_type( Request $request, GoodsKind $goods_kind, GoodsKindProperty $goods_kind_property ) {

        GoodsKind\GoodsKindPropertyProductType::where( "goods_kind_property_id", $goods_kind_property->id )->delete();

        foreach ( $request->data["product_type"] as $id => $item ) {
            GoodsKind\GoodsKindPropertyProductType::create( [
                "goods_kind_id"          => $goods_kind->id,
                "goods_kind_property_id" =>
                    $goods_kind_property->id,
                "product_type_id"        => $id
            ] );
        }

        return redirect()->route( $this->route_path . "index", $goods_kind->id )->with( [ "success" => "گروه های کالایی با موفقیت برروز رسانی شدند." ] );
    }

    public function edit_property_dependent( GoodsKindProperty $goods_kind_property ) {

        if ( $goods_kind_property->status_id == 1210 ) {
            return redirect()->route( $this->route_path . "index", $goods_kind_property->goods_kind_id )->with( [ "warning" => "مشخصه " . $goods_kind_property->caption . " غیرفعال است." ] );
        }
        $property_option_list = GoodsKindPropertyOption::where( "goods_kind_property_id", $goods_kind_property->id )->get();

        if ( $goods_kind_property->parent->field_type_id == 3 ) { // Select
            $values = $goods_kind_property->dependent_values->pluck( "id", "parent_value" );
        } else {
            $values = $goods_kind_property->dependent_values->pluck( "parent_value" );
        }

        $k              = $this->count_of_integer_compare;
        $compare_option = null;
        if ( $goods_kind_property->parent->field_type_id == 1 ) { // Integer
            $compare = $goods_kind_property->dependent_values->pluck( "compare" );
            for ( $i = 1; $i <= $this->count_of_integer_compare; $i ++ ) {
                $compare_option[ $i ] = Option::get( "compare_number", $compare[ $i - 1 ] ?? "=" );
            }
        }

        return view( $this->view_path . "edit_property_dependent", compact( "goods_kind_property", "property_option_list", "values", "compare_option", "k" ) );

    }

    public function update_property_dependent( Request $request, GoodsKindProperty $goods_kind_property ) {

        $goods_kind_property->dependent_values()->delete();

        if ( $goods_kind_property->parent->field_type_id == 1 ) { // Number
            if ( ! isset( $request->value_number_1 ) ) {
                return back()->withErrors( "لطفا یک مورد را وارد نمایید." );
            }
            for ( $i = 1; $i <= $this->count_of_integer_compare; $i ++ ) {
                $compare = "compare_" . $i;
                $number  = "value_number_" . $i;
                if ( isset( $request->$number ) ) {
                    GoodsKind\GoodsKindPropertyDependentValue::create( [
                        "goods_kind_property_id"        => $goods_kind_property->id,
                        "goods_kind_property_parent_id" => $goods_kind_property->parent->id,
                        "parent_value"                  => $request->$number,
                        "compare"                       => $request->$compare
                    ] );
                }
            }


        } else if ( $goods_kind_property->parent->field_type_id == 3 ) { // Select

            if ( ! isset( $request->data ) ) {
                return back()->withErrors( "لطفا حداقل یک مورد را انتخاب نمایید." );
            }
            $data = $request->data["value_select"];

            foreach ( $data as $key => $item ) {
                GoodsKind\GoodsKindPropertyDependentValue::create( [
                    "goods_kind_property_id"        => $goods_kind_property->id,
                    "goods_kind_property_parent_id" => $goods_kind_property->parent->id,
                    "parent_value"                  => $key
                ] );

            }
        } else if ( $goods_kind_property->parent->field_type_id == 5 ) { // T/F

            GoodsKind\GoodsKindPropertyDependentValue::create( [
                "goods_kind_property_id"        => $goods_kind_property->id,
                "goods_kind_property_parent_id" => $goods_kind_property->parent->id,
                "parent_value"                  => $request->value_check
            ] );

        }

        return redirect()->route( "line_product_station.goods_kind.property.index", $goods_kind_property->goods_kind_id )->
        with( [ "success" => "اطلاعات با موفقیت ذخیره گردید." ] );
    }

}
