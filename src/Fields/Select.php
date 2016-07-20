<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Select extends \Skvn\Crud\Form\Select implements WizardableField
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
            'hasMany',
            'belongsTo',
            'belongsToMany',
        ];
    }

    public function wizardDbType()
    {
        return 'integer';
    }


    function wizardCaption()
    {
        return "Select";
    }

    public function wizardCallbackFieldConfig (&$fieldKey,array &$fieldConfig,  $modelPrototype)
    {
        if (!empty($fieldConfig['property_name']))
        {
            $fieldKey = $fieldConfig['property_name'];
            unset($fieldConfig['property_name']);
        }

        if (!empty($fieldConfig['relation'])) {
            if (in_array($fieldConfig['relation'], [
                "belongsToMany",
                "hasMany"
            ])) {
                $fieldConfig['multiple'] = true;
            }
        }
    }
}