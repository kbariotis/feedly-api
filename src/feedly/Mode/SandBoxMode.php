<?php

namespace feedly\Mode;

use feedly\Mode\Mode;

class SandBoxMode implements Mode
{

    public function getApiBaseUrl()
    {
        return "https://sandbox.feedly.com";
    }

}