<?php

namespace AppBundle\Service;

class ApiService
{
    // could also be moved to a config file
    const URL = "http://127.0.0.1:8000/api/V1/offer";

    public static function call($method = 'GET', $data = [])
    {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => $method,
                'content' => json_encode($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents(self::URL, false, $context);
        $results = json_decode($result);

        return $results;
    }
}