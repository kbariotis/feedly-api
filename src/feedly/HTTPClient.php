<?php

namespace feedly;

class HTTPClient
{

    private
        $_getParams,
        $_postParams,
        $_ch;


    public function __construct()
    {
        try {
            $this->_ch = curl_init();
        } catch (\Exception $e) {
            throw new \Exception("Cannot initialize cUrl handler. Is cUrl enabled?");
        }

        $this->setCurlOptions();
    }

    function __destruct()
    {
        curl_close($this->_ch);
    }

    private function setCurlOptions()
    {
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_ENCODING, "");
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->_ch, CURLOPT_CAINFO, "C:\\wamp\\bin\\apache\\Apache2.2.21\\cacert.crt");
    }

    public function setCustomHeader($headers)
    {
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $headers);
    }

    public function setGetParams($params)
    {
        $this->_getParams = $params;
    }

    public function setPostParams($params, $json = true)
    {

        $this->_postParams = $params;

        if ($json)
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, json_encode($this->_postParams));
        else
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, http_build_query($this->_postParams));
    }

    public function setPostParamsEncType($enctype)
    {
        $this->setCustomHeader(array(
                                   'Content-Type: ' . $enctype
                               )
        );
    }

    public function prepareUrl($url)
    {
        if (isset($this->_getParams))
            $url = $url . '?' . http_build_query($this->_getParams);

        return $url;
    }

    public function get($url)
    {

        $url = $this->prepareUrl($url);

        curl_setopt($this->_ch, CURLOPT_URL, $url);

        $response = $this->exec();

        return $this->checkResponse($response);
    }

    public function post($url)
    {

        curl_setopt($this->_ch, CURLOPT_URL, $url);
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, "POST");

        $response = $this->exec();

        return $this->checkResponse($response);
    }

    public function delete($url)
    {

        curl_setopt($this->_ch, CURLOPT_URL, $url);
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $response = $this->exec();

        return $this->checkResponse($response);
    }

    public function exec()
    {
        $response = curl_exec($this->_ch);

        if (curl_error($this->_ch)) {
            throw new \RuntimeException("Communication with the API failed: " . curl_error($this->_ch));
        }

        return $response;
    }

    public function checkResponse($response)
    {
        $httpStatus = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);

        $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);

        if ($httpStatus !== 200) {
            throw new \Exception("Something went wrong: " . $response[ 'errorMessage' ] . ' : ' .
                                 $response[ 'errorCode' ]);
        }

        return $response;
    }

}
