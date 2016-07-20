<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Date extends \Skvn\Crud\Form\Date implements WizardableField
{
    use WizardCommonFieldTrait;



    function wizardCaption()
    {
        return "Date";
    }

    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig, $modelPrototype)
    {
        $formats = $modelPrototype->wizard->getAvailableDateFormats();
        $fieldConfig['jsformat'] = $formats[$fieldConfig['format']]['js'];
        $fieldConfig['format'] = $formats[$fieldConfig['format']]['php'];
        $fieldConfig['db_type'] = $modelPrototype->column_types[$fieldKey];
    }
}