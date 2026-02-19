

@include("component.input._lable",["id"=>"can_i_borrow_from_this_supplier",'label'=>"آیا می توان از این تامین کننده قرض گرفت","value"=>$employment->supplier->can_i_borrow_from_this_supplier?"بله":"خیر"])

@include("component.input._lable",["id"=>"supplier_type_id","label"=>"نوع تامین کننده","value"=>$employment->nationality_id==1?"تامین کننده داخل کشور":"تامین کننده خارج از کشور"])

@include("component.input._lable",[
    "id"=>"end_date_of_contract",
    "label"=>" پایان قرارداد ",
    "value"=>$employment->supplier->get_end_date_of_contract()??"",
    "class_col"=>""
    ])