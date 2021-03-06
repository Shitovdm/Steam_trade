<?php

    require_once("../../service/dbconnect.php");

    $market_hash_name = $_POST['tmarket_hash_name'];
    $price = $_POST['tprice'];

    if ($price < 400000) {
        session_start();
        $sessionId = $_SESSION['sessionId'];
        $cookies = $_SESSION['cookies'];

        $data = array(
            'sessionid' => $sessionId,
            'currency' => 5,
            'appid' => 730,
            'market_hash_name' => $market_hash_name,
            'price_total' => $price,
            'quantity' => 1
        );
        $url = 'https://steamcommunity.com/market/createbuyorder/';
        $c = curl_init();
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_NOBODY, false);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)");
        curl_setopt($c, CURLOPT_COOKIE, $cookies);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Referer: http://steamcommunity.com/market/listings/'));
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, strtoupper('POST'));
        $output = curl_exec($c);
        curl_close($c);
        $res = json_decode($output);

        if ($res->success == 1) {
            $timestamp = date("Y-m-d H:i:s");
            $price_cent = $price / 100;
            $query = "INSERT 
                              INTO bought_items_STtoTM 
                              (market_hash_name, order_price, timestamp) 
                              VALUES 	
                              ('$market_hash_name', '$price_cent' , '$timestamp')";
            $result = mysql_query($query);

            $response = array(
                'status' => $res->success,
                'buy_orderid' => $res->buy_orderid,
                'price' => $price
            );
        } else {
            $response = array(
                'status' => $res->success,
                'message' => $res->message,
                'price' => $price
            );
        }
    } else {
        $response = array(
            'status' => "0",
            'message' => "The price of the item is above the preset.",
            'price' => $price
        );
    }

    echo(json_encode($response));
?>