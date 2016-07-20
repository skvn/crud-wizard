<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Number extends \Skvn\Crud\Form\Number implements WizardableField
{
    use WizardCommonFieldTrait;

    public function wizardDbType()
    {
        return 'integer';
    }


    function wizardCaption()
    {
        return "Number input";
    }
}