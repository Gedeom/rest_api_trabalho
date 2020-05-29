<?php


namespace App\Services;

use App\Models\Utils;
use Illuminate\Validation\Validator;

class CustomValidator extends Validator
{

    private $_custom_messages = array(
        "cpf_valid" => "CPF não é valido!",
    );

    protected function _set_custom_stuff()
    {
        //setup our custom error messages
        $this->setCustomMessages($this->_custom_messages);
    }

    public function __construct($translator, $data, $rules, $messages = array(), $customAttributes = array())
    {
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);

        $this->_set_custom_stuff();

    }

    protected
    function validateCpfValid($attribute, $value)
    {

        return Utils::validate_cpf($value);
    }
}
