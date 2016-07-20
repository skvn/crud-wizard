<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class EntitySelect extends \Skvn\Crud\Form\EntitySelect implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Returns true if the  control can be used only for relation editing only
     *
     * @return bool
     */
    public function wizardIsForRelationOnly():bool
    {
        return true;
    }


    /**
     * Return an array of relations for which the control can be used
     *
     * @return array
     */
    public function wizardIsForRelations():array {

        return [
            'hasOne',
            'hasMany',
            'belongsTo',
            'belongsToMany',
        ];
    }


    public function wizardDbType()
    {
        return 'integer';
    }


    function wizardTemplate()
    {
        return "crud::wizard.blocks.fields.select";
    }


    function wizardCaption()
    {
        return "Entity Select";
    }



}