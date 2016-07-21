<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Radio extends \Skvn\Crud\Form\Radio implements WizardableField
{
    use WizardCommonFieldTrait;

    /**
     * Get Wizard config defaults
     *
     * @return array
     */
    public function wizardConfigDefaults(): array {

        return ['find'=>''];
    }


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
     * Return wizard config sections
     *
     * @return array
     */
    public function wizardConfigSections():array {

        return ['required','data_provider'];
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