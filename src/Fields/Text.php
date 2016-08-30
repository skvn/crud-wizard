<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Text extends \Skvn\Crud\Form\Text implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Return wizard config sections.
     *
     * @return array
     */
    public function wizardConfigSections():array
    {
        return ['required'];
    }

    public function wizardCaption()
    {
        return 'Text input';
    }
}
