<?php

namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Tags extends \Skvn\Crud\Form\Tags implements WizardableField
{
    use WizardCommonFieldTrait;

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
            'hasMany',
            'belongsToMany',
        ];
    }

    public function wizardCaption()
    {
        return 'Tags';
    }
}
