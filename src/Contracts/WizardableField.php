<?php

namespace Skvn\CrudWizard\Contracts;

interface WizardableField
{
    /**
     * Return wizard config sections.
     *
     * @return array
     */
    public function wizardConfigSections():array;

    /**
     * Get Wizard config defaults.
     *
     * @return array
     */
    public function wizardConfigDefaults(): array;

    /**
     * Returns database column type for the field.
     *
     * @return null|string
     */
    public function wizardDbType();

    /**
     * Returns true if the  control can be used only for virtual property.
     *
     * @return bool
     */
    public function wizardIsForVirtualOnly():bool;

    /**
     * Returns true if the  control can be used only for relation editing only.
     *
     * @return bool
     */
    public function wizardIsForRelationOnly():bool;

    /**
     * Return an array of relations for which the control can be used.
     *
     * @return array
     */
    public function wizardIsForRelations():array;

    public function wizardCaption();

    /**
     * Apply actions to the field config array during setup in wizard.
     *
     * @param strinf $fieldKey
     * @param array  $fieldConfig
     * @param  $modelPrototype
     */
    public function wizardCallbackFieldConfig(&$fieldKey, array &$fieldConfig, $modelPrototype);

    /**
     * Apply actions to the model config array during setup in wizard.
     *
     * @param strinf $fieldKey
     * @param array  $modelConfig
     * @param  $modelPrototype
     */
    public function wizardCallbackModelConfig($fieldKey, array &$modelConfig, $modelPrototype);

    /**
     * If the field is custom, it's not fully configurated via UI.
     *
     * @return bool
     */
    public function wizardIsCustomField(): bool;
}
