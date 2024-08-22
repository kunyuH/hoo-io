<?php

namespace hoo\io\common\Response;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HmBinaryFileResponse extends BinaryFileResponse
{
    public function getData()
    {
        return [];
    }
}
