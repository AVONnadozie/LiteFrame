<?php

function getHttpResponseMessage($code)
{
    return LiteFrame\Http\Response::getInstance()
                    ->getHttpResponseMessage($code);
}
