<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Number extends \Skvn\Crud\Form\Number implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Get Wizard config defaults.
     *
     * @return array
     */
    public function wizardConfigDefaults(): array
    {
        return ['step' => 1];
    }

    /**
     * Return wizard config sections.
     *
     * @return array
     */
    public function wizardConfigSections():array
    {
        return ['required', 'number'];
    }

    public function wizardDbType()
    {
        return 'integer';
    }

    public function wizardCaption()
    {
        return 'Number input';
    }
}
