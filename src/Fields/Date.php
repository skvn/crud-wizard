<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Date extends \Skvn\Crud\Form\Date implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Get Wizard config defaults
     *
     * @return array
     */
    public function wizardConfigDefaults(): array {

        return ['format'=>'d.m.Y'];
    }


    /**
     * Return wizard config sections
     *
     * @return array
     */
    public function wizardConfigSections():array {

        return ['required','date_format'];
    }

    function wizardCaption()
    {
        return "Date";
    }

    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig, $modelPrototype)
    {
        $formats = $modelPrototype->wizard->getAvailableDateFormats();
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