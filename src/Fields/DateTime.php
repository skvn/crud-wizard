<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class DateTime extends \Skvn\Crud\Form\DateTime implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Get Wizard config defaults
     *
     * @return array
     */
    public function wizardConfigDefaults(): array {

        return ['format'=>'d.m.Y H:i'];
    }

    /**
     * Return wizard config sections
     *
     * @return array
     */
    public function wizardConfigSections():array {

        return ['required','date_time_format'];
    }

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

        foreach ($formats as $f) {
            if ($f['php'] == $fieldConfig['format']) {
                $fieldConfig['jsformat'] = $f['js'];
                if (!empty($modelPrototype->column_types[$fieldKey])) {
                    $fieldConfig['db_type'] = $modelPrototype->column_types[$fieldKey];
                }
            }
        }


    }
}