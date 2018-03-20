<?php

namespace Ixolit\Dislo\WorkingObjects\Subscription;


use Ixolit\Dislo\WorkingObjects\AbstractWorkingObject;
use Ixolit\Dislo\WorkingObjectsCustom\Subscription\DisplayNameCustom;

/**
 * Class DisplayNameObject
 *
 * @package Ixolit\Dislo\WorkingObjects
 */
final class DisplayNameObject extends AbstractWorkingObject {

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $language
     * @param string $name
     */
    public function __construct($language, $name) {
        $this->language = $language;
        $this->name     = $name;
        $this->addCustomObject();
    }

    /**
     * @return DisplayNameCustom|null
     */
    public function getCustom() {
        /** @var DisplayNameCustom $custom */
        $custom = ($this->getCustomObject() instanceof DisplayNameCustom) ? $this->getCustomObject() : null;
        return $custom;
    }

    /**
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * @param array $response
     *
     * @return DisplayNameObject
     */
    public static function fromResponse($response) {
        return new self(
            static::getValueIsSet($response, 'language'),
            static::getValueIsSet($response, 'name')
        );
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            '_type'    => 'DisplayName',
            'language' => $this->language,
            'name'     => $this->name,
        ];
    }

}