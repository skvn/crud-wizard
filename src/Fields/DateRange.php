<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class DateRange extends \Skvn\Crud\Form\DateRange implements WizardableField
{
    use WizardCommonFieldTrait;

    public function wizardDbType()
    {
        return '';
    }


    function wizardCaption()
    {
        return "Date range";
    }


    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig,   $modelPrototype)
    {
        $fieldConfig['db_type'] = $modelPrototype->column_types[$fieldConfig['fields'][0]];
        $formats = $modelPrototype->wizard->getAvailableDateTimeFormats();
        $fieldConfig['jsformat'] = $formats[$fieldConfig['format']]['js'];
        $fieldConfig['format'] = $formats[$fieldConfig['format']]['php'];

        unset($fieldConfig['property_name']);

    }


}