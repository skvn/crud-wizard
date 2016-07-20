<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Radio extends \Skvn\Crud\Form\Radio implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Returns true if the  control can be used only for relation editing only
     *
     * @return bool
     */
    public function wizardIsForRelationOnly():bool
    {
        return false;
    }


    /**
     * Return an array of relations for which the control can be used
     *
     * @return array
     */
    public function wizardIsForRelations():array {

        return [
            'hasOne',
            'belongsTo',
        ];
    }


    public function wizardDbType()
    {
        return '';
    }



    function wizardCaption()
    {
        return "Radio group";
    }
}