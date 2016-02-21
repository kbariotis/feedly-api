<?php

namespace feedly\Mode;

use feedly\Mode\Mode;

class DeveloperMode implements Mode
{

    public function getApiBaseUrl()
    {
        return "https://cloud.feedly.com";
    }

}