<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element;

use JsonException;
use TYPO3\CMS\Backend\Form\Element\TextElement;

class JsonFieldElement extends TextElement
{
    /**
     * @var string
     */
    public const RENDER_TYPE = 'digitalMarketingFrameworkJsonFieldElement';

    /**
     * Render textarea and use whitespaces to format JSON
     *
     * @return array<mixed>
     */
    public function render(): array
    {
        $data = (string)$this->data['parameterArray']['itemFormElValue'];
        if ($data !== '') {
            try {
                $jsonData = json_decode($data, flags: JSON_THROW_ON_ERROR);
                $data = json_encode($jsonData, flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
                $this->data['parameterArray']['itemFormElValue'] = $data;
            } catch (JsonException) {
                // if the data is not valid JSON, don't do anything
            }
        }

        return parent::render();
    }
}
