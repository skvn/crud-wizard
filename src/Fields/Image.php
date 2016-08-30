<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Image extends \Skvn\Crud\Form\Image implements WizardableField
{
    use WizardCommonFieldTrait;

    public function wizardCaption()
    {
        return 'Image';
    }

    /**
     * Returns true if the  control can be used only for relation editing only.
     *
     * @return bool
     */
    public function wizardIsForRelationOnly():bool
    {
        return true;
    }

    /**
     * Return an array of relations for which the control can be used.
     *
     * @return array
     */
    public function wizardIsForRelations():array
    {
        return [
            'hasFile',
        ];
    }
}
