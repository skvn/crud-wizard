<?php


namespace Skvn\CrudWizard\Fields;

use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Traits\WizardCommonFieldTrait;

class Textarea extends \Skvn\Crud\Form\TextArea implements WizardableField
{
    use WizardCommonFieldTrait;


    /**
     * Get Wizard config defaults
     *
     * @return array
     */
    public function wizardConfigDefaults(): array {

        return ['height'=>500, 'editor_type'=>''];
    }

    /**
     * Return wizard config sections
     *
     * @return array
     */
    public function wizardConfigSections():array {

        return [
            'required',
            'height',
            'editor_type'
        ];
    }


    public function wizardDbType()
    {
        return 'longText';
    }


    function wizardCaption()
    {
        return "Textarea";
    }


    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig,  $modelPrototype)
    {
        unset($fieldConfig['editor']);
    }

    public  function wizardCallbackModelConfig($fieldKey,array &$modelConfig, $modelPrototype)
    {
        if (!empty($modelConfig['fields'][$fieldKey]['editor']))
        {
            if (!isset($modelConfig['inline_img']))
            {
                $modelConfig['inline_img'] = [];
                if (!isset($modelConfig['traits']))
                {
                    $modelConfig['traits'] = [];
                }
                $modelConfig['traits'][] = 'ModelInlineImgTrait';
            }

            $modelConfig['inline_img'][] = $fieldKey;
        }
    }


}