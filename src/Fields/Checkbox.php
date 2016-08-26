<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Checkbox extends \Skvn\Crud\Form\Checkbox implements WizardableField
{
    use WizardCommonFieldTrait;

    public function wizardDbType()
    {
        return 'boolean';
    }

    public function wizardCaption()
    {
        return 'Checkbox';
    }
}
