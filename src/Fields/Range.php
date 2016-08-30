<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Range extends \Skvn\Crud\Form\Range implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Get Wizard config defaults.
     *
     * @return array
     */
    public function wizardConfigDefaults(): array
    {
        return ['fields' => ['', '']];
    }

    /**
     * Return wizard config sections.
     *
     * @return array
     */
    public function wizardConfigSections():array
    {
        return ['required', 'range'];
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

    public function wizardDbType()
    {
        return '';
    }

    public function wizardCaption()
    {
        return 'Range';
    }
}
