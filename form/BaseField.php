<?php


namespace App\Core\Form;


use App\Core\BaseModel;

abstract class BaseField
{
    public BaseModel $model;
    public string $attribute;
    /**
     * Field constructor.
     * @param BaseModel $model
     * @param string $attribute
     */
    public function __construct(BaseModel $model, $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }
    abstract public function renderInput() : string;

    public function __toString()
    {
        return sprintf('
            <div class="mb-3">
                <label for="%s" class="form-label">%s</label>
                %s
                <div class="is-invalid">%s</div>
            </div>
        ',
            $this->model->getLabel($this->attribute) ?? $this->attribute,
            $this->attribute,
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }
}