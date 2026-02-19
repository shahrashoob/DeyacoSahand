<?php
/************* گزارش مصرف چله و نخ برای پارچه خام به صورت تست انجام شد. ****/
$goods_kind_id = 5;
// هر کالای پارچه خام چند bom دارد
$product_bom_count = "SELECT product_id, round(COUNT(id)/count(DISTINCT(bill_of_material_id))) bom_count FROM bill_of_material_item GROUP BY product_id";

$allocation_actual_calculated_count = "SELECT allocation_id, product_id, COUNT(material_id) FROM machine_allocation_actual_consumption GROUP BY  allocation_id, product_id ";

$packing_form_current_machine_log = "SELECT allocation_id,production_id, product_id , material_id , packing_forms.code    from   current_machine_input_logs JOIN packing_forms ON packing_forms.id=entry_packing_form_id ";

$actual_amount = "SELECT allocation_id, product_id , material_id , predictive_amount, actual_amount   FROM machine_allocation_actual_consumption ";

$packing_forms = "SELECT allocation_id,packing_form_item.product_id,packing_forms.id AS packing_form_id, packing_forms.code, SUM(packing_form_item.amount) , SUM(packing_form_item.final_amount)
                            FROM production_form_item
                            JOIN packing_form_item ON production_form_item.id=production_form_item_id
                            JOIN packing_forms ON packing_forms.id=packing_form_id
                            JOIN products ON products.id=packing_form_item.product_id
                            WHERE packing_forms.status_id !=7007007
                            AND  packing_forms.deleted_at  is NULL
                            AND goods_kind_id=4
                            GROUP BY allocation_id , packing_form_item.product_id";

$production_list="SELECT id,serial , product_id  , parent_production_id FROM production_cards";

$q1="SELECT allocation_id, current_machine_inputs.packing_form_id, packing_form_item.amount, packing_form_item.final_amount, packing_forms.weight FROM current_machine_inputs JOIN packing_forms ON packing_forms.id=packing_form_id

JOIN packing_form_item ON packing_form_item.packing_form_id=current_machine_inputs.packing_form_id

WHERE goods_kind_id = 3 and allocation_id IN (5156	,


)";

$q2="SELECT packing_form_item_id,form_item.form_id,product_request_form_id ,product_request_forms.allocation_id, production_id, serial  FROM form_item JOIN forms ON forms.id=form_item.form_id

JOIN product_request_form_form ON product_request_form_form.form_id=forms.id
JOIN product_request_forms ON product_request_forms.id=product_request_form_form.product_request_form_id
JOIN allocations ON product_request_forms.allocation_id=allocations.id
JOIN machine_allocation ON machine_allocation.allocation_id =allocations.id
JOIN production_cards ON production_cards.id = production_id

WHERE

forms.form_type_id=0 AND
packing_form_item_id IN (57993	,";

// کوِ،یری های مرحله دوم گزارش مصرف 20/2/1403

//SELECT allocation_id,packing_form_item.product_id,packing_forms.id AS packing_form_id, packing_forms.code, packing_form_item.amount , packing_form_item.final_amount
//                            FROM production_form_item
//                            JOIN packing_form_item ON production_form_item.id=production_form_item_id
//                            JOIN packing_forms ON packing_forms.id=packing_form_id
//                            JOIN products ON products.id=packing_form_item.product_id
//                            WHERE packing_forms.status_id !=7007007
//AND  packing_forms.deleted_at  is NULL
//AND goods_kind_id=4
//
//
//
//SELECT forms.id,allocations.id AS allocation_id,machine_allocation.product_id from forms
//
//JOIN product_request_form_form ON product_request_form_form.form_id=forms.id
//JOIN product_request_forms ON product_request_forms.id=product_request_form_form.product_request_form_id
//JOIN allocations ON product_request_forms.allocation_id=allocations.id
//JOIN machine_allocation ON machine_allocation.allocation_id =allocations.id
//JOIN production_cards ON production_cards.id = production_id
//WHERE forms.id IN (
//    2172	,
//	)
//
//
//    SELECT allocation_id,machine_allocation_actual_consumption.product_id,material_id,predictive_amount,actual_amount , products.goods_kind_id
//
//FROM machine_allocation_actual_consumption
//
//JOIN products ON products.id=material_id
// WHERE allocation_id IN (
//    245	,
//
//	)
//
//    SELECT allocation_id,packing_form_id FROM current_machine_inputs
//WHERE goods_kind_id =3 AND allocation_id IN (
//    5199	,
//
//
// SELECT allocation_id , SUM(packing_form_item.sub_amount) AS sub_amount,SUM(packing_form_item.final_amount) AS final_amount
