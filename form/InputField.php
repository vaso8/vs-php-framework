<?php


namespace App\Core\Form;


use App\Core\BaseModel;
use App\Models\User;

class InputField extends BaseField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public string $type;





    public function __construct(BaseModel $model, $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function password()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function email()
    {
        $this->type = self::TYPE_EMAIL;
        return $this;
    }


    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control %s" id="%s">',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->attribute
        );
    }
}