<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 24/03/2017
 * Time: 11:54 AM
 */
function uploadImage($image)
{
    $img = $_FILES[$image];

    if ($img['name'] == '') {

    } else {
        $filename = $img['tmp_name'];
        $client_id = "78f1e2525a07bf6";
        $handle = fopen($filename, "r");
        $data = fread($handle, filesize($filename));
        $pvars = array('image' => base64_encode($data));
        $timeout = 30;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
        $out = curl_exec($curl);
        curl_close($curl);
        $pms = json_decode($out, true);
        $url = $pms['data']['link'];
        if ($url != "") {
            return $url;
        } else {
            throw new Exception("Invalid Image Upload");
        }
    }
}