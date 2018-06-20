<?php

namespace LiteFrame\Utility;

use FormValidator\Validator as CoreValidator;

class Validator extends CoreValidator
{
    public function validate($key, $recursive = false, $label = '')
    {
        parent::validate($key, $recursive, $label);
        if ($this->hasErrors()) {
            abort(422, $this->getAllErrors());
        }
        return $this;
    }

    public function check($data = null, $rules = null)
    {
    }
}
