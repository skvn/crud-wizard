<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class DateTime extends \Skvn\Crud\Form\DateTime implements WizardableField
{
    use WizardCommonFieldTrait;

    public function wizardDbType()
    {
        return 'dateTime';
    }


    function wizardCaption()
    {
        return "Date + Time";
    }

    function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig,  $modelPrototype)
    {
        $formats = $modelPrototype->wizard->getAvailableDateTimeFormats();
        $fieldConfig['jsformat'] = $formats[$fieldConfig['format']]['js'];
        $fieldConfig['format'] = $formats[$fieldConfig['format']]['php'];
        $fieldConfig['db_type'] = $modelPrototype->column_types[$fieldKey];
    }
}