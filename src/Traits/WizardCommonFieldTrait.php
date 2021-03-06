<?php

namespace Skvn\CrudWizard\Traits;

trait WizardCommonFieldTrait
{
    /**
     * Get Wizard config defaults.
     *
     * @return array
     */
    public function wizardConfigDefaults(): array
    {
        return [];
    }

    /**
     * Return wizard config sections.
     *
     * @return array
     */
    public function wizardConfigSections():array
    {
        return [];
    }

    /**
     * Returns database column type for the field.
     *
     * @return string
     */
    public function wizardDbType()
    {
        return 'string';
    }

    /**
     * Returns true if the  control can be used only for virtual property.
     *
     * @return bool
     */
    public function wizardIsForVirtualOnly():bool
    {
        return false;
    }

    /**
     * Returns true if the  control can be used only for relation editing only.
     *
     * @return bool
     */
    public function wizardIsForRelationOnly():bool
    {
        return false;
    }

    /**
     * Return an array of relations for which the control can be used.
     *
     * @return array
     */
    public function wizardIsForRelations():array
    {
        return [
        ];
    }

    /**
     * Returns true if the  control can be used  for "many" - type relation editing.
     *
     * @return bool
     */
    public function wizardIsForManyRelation():bool
    {
        return false;
    }

    public function wizardCaption()
    {
        return '---';
    }

    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig, $modelPrototype)
    {
    }

    public function wizardCallbackModelConfig($fieldKey, array &$modelConfig, $modelPrototype)
    {
    }

    /**
     * If the field is custom, it's not fully configurated via UI.
     *
     * @return bool
     */
    public function wizardIsCustomField(): bool
    {
        return false;
    }
}
