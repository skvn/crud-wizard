<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Range extends \Skvn\Crud\Form\Range implements WizardableField
{
    use WizardCommonFieldTrait;

    public function wizardDbType()
    {
        return '';
    }


    function wizardCaption()
    {
        return "Range";
    }


}