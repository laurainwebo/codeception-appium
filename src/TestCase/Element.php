<?php
namespace Appium\TestCase;

class Element
    extends \PHPUnit_Extensions_Selenium2TestCase_Element
{
    protected function getSessionUrl()
    {
        return $this->url;
    }

    /**
     * @param array                                        $value
     * @param \PHPUnit_Extensions_Selenium2TestCase_URL    $parentFolder
     * @param \PHPUnit_Extensions_Selenium2TestCase_Driver $driver
     *
     * @return \Appium\TestCase\Element
     */
    public static function fromResponseValue(
        array $value,
        \PHPUnit_Extensions_Selenium2TestCase_URL $parentFolder,
        \PHPUnit_Extensions_Selenium2TestCase_Driver $driver)
    {
        if (!isset($value['ELEMENT'])) {
            throw new \InvalidArgumentException('Element not found.');
        }
        $url = $parentFolder->descend($value['ELEMENT']);

        return new static($driver, $url);
    }

    // override to return Appium element
    public function element(\PHPUnit_Extensions_Selenium2TestCase_ElementCriteria $criteria)
    {
        $value = $this->postCommand('element', $criteria);

        return Element::fromResponseValue($value, $this->getSessionUrl()->descend('element'), $this->driver);
    }

    public function elements(\PHPUnit_Extensions_Selenium2TestCase_ElementCriteria $criteria)
    {
        $values = $this->postCommand('elements', $criteria);

        $elements = [];
        foreach ($values as $value) {
            $elements[] = Element::fromResponseValue($value, $this->getSessionUrl()->descend('element'), $this->driver);
        }

        return $elements;
    }

    public function by($strategy, $value)
    {
        $el = $this->element($this->using($strategy)->value($value));

        return $el;
    }

    public function setImmediateValue($value)
    {
        $data = [
            'id'    => $this->getId(),
            'value' => $value,
        ];
        $url  = $this->getSessionUrl()->descend('appium')->descend('element')->descend($this->getId())->descend('value');
        $this->driver->curl('POST', $url, $data);
    }

    public function setText($keys)
    {
        $data = [
            'id'    => $this->getId(),
            'value' => [$keys],
        ];
        $url  = $this->getSessionUrl()->descend('appium')->descend('element')->descend($this->getId())->descend('replace_value');
        $this->driver->curl('POST', $url, $data);
    }

}