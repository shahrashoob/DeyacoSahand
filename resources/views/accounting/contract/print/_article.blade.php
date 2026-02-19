@php


    $token1_value=$article->token_id1?$keys_id[$article->token_id1]:"*********";
    $token2_value=$article->token_id2?$keys_id[$article->token_id2]:"*********";
    $token3_value=$article->token_id3?$keys_id[$article->token_id3]:"*********";
    $token4_value=$article->token_id4?$keys_id[$article->token_id4]:"*********";
    $token5_value=$article->token_id5?$keys_id[$article->token_id5]:"*********";
    $token6_value=$article->token_id6?$keys_id[$article->token_id6]:"*********";
    $token7_value=$article->token_id7?$keys_id[$article->token_id7]:"*********";
    $token8_value=$article->token_id8?$keys_id[$article->token_id8]:"*********";
    $token9_value=$article->token_id9?$keys_id[$article->token_id9]:"*********";
    $token10_value=$article->token_id10?$keys_id[$article->token_id10]:"*********";

    $caption=$article->caption;
    $caption=str_replace('token1',$token1_value,$caption);
    $caption=str_replace('token2',$token2_value,$caption);
    $caption=str_replace('token3',$token3_value,$caption);
    $caption=str_replace('token4',$token4_value,$caption);
    $caption=str_replace('token5',$token5_value,$caption);
    $caption=str_replace('token6',$token6_value,$caption);
    $caption=str_replace('token7',$token7_value,$caption);
    $caption=str_replace('token8',$token8_value,$caption);
    $caption=str_replace('token9',$token9_value,$caption);
    $caption=str_replace('token10',$token10_value,$caption);
    echo $caption;
@endphp