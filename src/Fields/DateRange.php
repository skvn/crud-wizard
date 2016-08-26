<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class DateRange extends \Skvn\Crud\Form\DateRange implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Get Wizard config defaults.
     *
     * @return array
     */
    public function wizardConfigDefaults(): array
    {
        return ['format' => 'd.m.Y H:i', 'fields' => ['', '']];
    }

    /**
     * Return wizard config sections.
     *
     * @return array
     */
    public function wizardConfigSections():array
    {
        return ['required', 'date_time_format', 'range'];
    }

    public function wizardDbType()
    {
        return '';
    }

    /**
     * Returns true if the  control can be used only for virtual property.
     *
     * @return bool
     */
    public function wizardIsForVirtualOnly():bool
    {
        return true;
    }

    public function wizardCaption()
    {
        return 'Date range';
    }

    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig,   $modelPrototype)
    {
        $fieldConfig['db_type'] = $modelPrototype->column_types[$fieldConfig['fields'][0]];
        $formats = $modelPrototype->wizard->getAvailableDateTimeFormats();

        foreach ($formats as $f) {
            if ($f['php'] == $fieldConfig['format']) {
                $fieldConfig['jsformat'] = $f['js'];
            }
        }
    }
}
