<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\DataHandler;

use DigitalMarketingFramework\Typo3\Core\Form\Element\JsonFieldElement;
use JsonException;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;

class JsonDataHandler implements SingletonInterface
{
    /**
     * @param array<string,mixed> $fieldArray
     */
    protected function updateJsonField(array &$fieldArray, string $fieldName): void
    {
        $data = (string)($fieldArray[$fieldName] ?? '');
        if ($data !== '') {
            try {
                $jsonData = json_decode($data, flags: JSON_THROW_ON_ERROR);
                $data = json_encode($jsonData, flags: JSON_THROW_ON_ERROR);
                $fieldArray[$fieldName] = $data;
            } catch (JsonException) {
                // if the data is not valid JSON, don't do anything
            }
        }
    }

    /**
     * @return array<string>
     */
    protected function getJsonFields(string $table): array
    {
        $result = [];
        $columns = $GLOBALS['TCA'][$table]['columns'] ?? [];
        foreach ($columns as $fieldName => $column) {
            $type = $column['config']['type'] ?? '';
            $renderType = $column['config']['renderType'] ?? '';
            if ($type === 'user' && $renderType === JsonFieldElement::RENDER_TYPE) {
                $result[] = $fieldName;
            }
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $fieldArray
     */
    public function processDatamap_preProcessFieldArray(array &$fieldArray, string $table, string $id, DataHandler $parentObj): void
    {
        if (!$parentObj->isImporting) {
            $fields = $this->getJsonFields($table);
            foreach ($fields as $field) {
                $this->updateJsonField($fieldArray, $field);
            }
        }
    }
}
