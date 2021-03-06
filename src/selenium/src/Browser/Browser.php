<?php

namespace PrestaShop\Selenium\Browser;

use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;
use PrestaShop\PSTest\Helper\URLHelper as URL;

use PrestaShop\Selenium\Browser\Exception\ElementNotFoundException;
use PrestaShop\Selenium\Browser\Exception\TooManyElementsFoundException;
use PrestaShop\Selenium\Browser\Exception\NoAlertException;
use PrestaShop\Selenium\Browser\Exception\SelectValueNotFoundException;

use Exception;

use WebDriver;
use NoSuchElementException;
use RemoteWebDriver;
use WebDriverDimension;
use WebDriverBy;
use WebDriverSelect;
use WebDriverKeys;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;

class Browser implements BrowserInterface, EventEmitterInterface
{
    use EventEmitterTrait;

    // the underlying WebDriver
    private $driver;

    private $defaultTimeout = 5;
    private $defaultInterval = 500;

    private $quitted = false;

    private $wrappedStackDepth = 0;

    public function __construct(WebDriver $driver)
    {
        $this->driver = $driver;
    }

    public function __destruct()
    {
        $this->quit();
    }

    public function quit()
    {
        if (!$this->quitted) {
            $this->quitted = true;
            $this->driver->quit();
        }

        return $this;
    }

    private function before($function, $arguments)
    {
        if ($this->wrappedStackDepth === 0) {
            $this->emit('before action', [$function, $arguments]);
        }
    }

    private function after($function, $arguments)
    {
        if ($this->wrappedStackDepth === 0) {
            $this->emit('after action', [$function, $arguments]);
        }
    }

    private function wrap($function, $arguments)
    {
        $this->before($function, $arguments);
        $this->wrappedStackDepth++;

        try {
            return call_user_func_array([$this, '_' . $function], $arguments);
        } finally {
            $this->wrappedStackDepth--;
            $this->after($function, $arguments);
        }
    }

    public function resizeWindow($width, $height)
    {
        $this->driver->manage()->window()->setSize(new WebDriverDimension($width, $height));

        return $this;
    }

    public function setDefaultRetryTimeout($timeout)
    {
        $this->defaultTimeout = $timeout;
        return $this;
    }

    public function setDefaultRetryInterval($interval)
    {
        $this->defaultInterval = $interval;
        return $this;
    }

    public function getCurrentURL()
    {
        return $this->driver->getCurrentURL();
    }

    public function getPageSource()
    {
        return $this->driver->getPageSource();
    }

    public function getURLParameter($param)
    {
        $url = $this->getCurrentURL();

        return URL::getParameter($url, $param);
    }

    /**
     * visit
     */

    private function _visit($url, array $basic_auth = null)
    {
        if ($basic_auth) {
            $url = preg_replace('/^(\w+:\/\/)/', '\1'.$basic_auth['user'].':'.$basic_auth['pass'].'@', $url);
        }
        $this->driver->get($url);

        return $this;
    }

    public function visit($url, array $basic_auth = null)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * refresh
     */

    private function _refresh()
    {
        $this->driver->navigate()->refresh();

        return $this;
    }

    public function refresh()
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * reload
     */

    private function _reload()
    {
        $this->visit($this->getCurrentURL());

        return $this;
    }

    public function reload()
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    public function back()
    {
        $this->driver->navigate()->back();
        return $this;
    }

    public function forward()
    {
        $this->driver->navigate()->forward();
        return $this;
    }

    /**
     * sleep
     */

    private function _sleep($seconds)
    {
        sleep($seconds);

        return $this;
    }

    public function sleep($seconds)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * clearCookies
     */

    private function _clearCookies()
    {
        $this->driver->manage()->deleteAllCookies();

        return $this;
    }

    public function clearCookies()
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * executeScript
     */

    private function _executeScript($script, array $args = array())
    {
        return $this->driver->executeScript($script, $args);
    }

    public function executeScript($script, array $args = array())
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    private function _executeAsyncScript($script, array $args = array())
    {
        return $this->driver->executeAsyncScript($script, $args);
    }

    public function executeAsyncScript($script, array $args = array())
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    public function setScriptTimeout($seconds)
    {
        $this->driver->manage()->timeouts()->setScriptTimeout($seconds);

        return $this;
    }

    /**
     * acceptAlert
     */
    private function _acceptAlert()
    {
        try {
            Spin::assertNoException(function() {
                $alert = $this->driver->switchTo()->alert();
                $alert->accept();
            }, $this->defaultTimeout, $this->defaultInterval, 'Did not find alert.');
        } catch (Exception $e) {
            throw new NoAlertException("There was no alert to accept.", 1, $e);
        }

        return $this;
    }

    public function acceptAlert()
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * switchToIFrame
     */

    private function _switchToIFrame($name)
    {
        $this->driver->switchTo()->frame($name);
        return $this;
    }

    public function switchToIFrame($name)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * switchToDefaultContent
     */

    private function _switchToDefaultContent()
    {
        $this->driver->switchTo()->defaultContent();
        return $this;
    }

    public function switchToDefaultContent()
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * find
     */

    private function _find($selector, array $options = array())
    {
        $defaults = [
            'unique' => true,
            'wait'  => true,
            'baseElement' => null,
            'timeout' => $this->defaultTimeout,
            'interval' => $this->defaultInterval,
            'displayed' => null,
            'enabled' => null
        ];

        $options = array_merge($defaults, $options);

        $base = $this->driver;
        if ($options['baseElement']) {
            $base = $options['baseElement']->getNativeElement();
        }

        if (!$options['wait']) {
            $options['timeout'] = 0;
        }

        $finalSelector = $selector;
        $selectorType = 'cssSelector';

        if (preg_match('/^{xpath}(.*)$/', $selector, $m)) {
            $finalSelector = $m[1];
            $selectorType = 'xpath';
        }

        $finder = function ($spinner) use ($base, $selectorType, $finalSelector, $options) {
            $spinner->abortOnException('PrestaShop\Selenium\Browser\Exception\TooManyElementsFoundException');

            $found = $base->findElements(WebDriverBy::$selectorType($finalSelector));

            $elements = array_map(function ($nativeElement) {
                return new Element($nativeElement, $this);
            }, $found);

            if ($options['displayed'] !== null || $options['enabled'] !== null) {
                $elements = array_filter($elements, function ($element) use ($options) {

                    $ok = true;

                    if ($options['displayed'] !== null) {
                        $ok = $ok && ($element->isDisplayed() == $options['displayed']);
                    }

                    if ($ok && $options['enabled'] !== null) {
                        $ok = $ok && ($element->isEnabled() == $options['enabled']);
                    }

                    return $ok;
                });
            }

            if (empty($elements)) {
                throw new Exception('No element found.');
            }

            $nFound = count($elements);

            if ($options['unique'] && $nFound > 1) {
                throw new TooManyElementsFoundException("Found `$nFound` elements matching selector `$finalSelector`.");
            }

            // reindex array starting at 0
            $elements = array_values($elements);

            if ($options['unique']) {
                return $elements[0];
            } else {
                return $elements;
            }

        };

        try {
            return Spin::assertNoException($finder,$options['timeout'], $options['interval']);
        } catch (Exception $e) {

            if ($e instanceof TooManyElementsFoundException) {
                throw $e;
            }

            throw new ElementNotFoundException('Could not find element(s) (selector: `' . $selector . '`).', 1, $e);
        }
    }

    public function find($selector, array $options = array())
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * waitFor
     */

    private function _waitFor($selector, $timeout_in_second = null, $interval_in_millisecond = null)
    {
        $options = [
            'unique' => false,
            'displayed' => true
        ];

        if ($timeout_in_second) {
            $options['timeout'] = $timeout_in_second;
        }

        if ($interval_in_millisecond) {
            $options['interval'] = $interval_in_millisecond;
        }

        $this->find($selector, $options);

        return $this;
    }

    public function waitFor($selector, $timeout_in_second = null, $interval_in_millisecond = null)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    public function ensureElementShowsUpOnPage($selector, $timeout_in_second = null, $interval_in_millisecond = null)
    {
        return $this->waitFor($selector, $timeout_in_second, $interval_in_millisecond);
    }

    /**
     * ensureElementIsOnPage
     */

    private function _ensureElementIsOnPage($selector)
    {
        return $this->waitFor($selector, 0);
    }

    public function ensureElementIsOnPage($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * all
     */

    public function all($selector)
    {
        try {
            return $this->find($selector, ['unique' => false]);
        } catch (ElementNotFoundException $e) {
            return [];
        }
    }

    /**
     * count
     */

    public function count($selector)
    {
        return count($this->all($selector));
    }

    /**
     * getAttribute
     */

    private function _getAttribute($selector, $attribute)
    {
        return $this->find($selector)->getAttribute($attribute);
    }

    public function getAttribute($selector, $attribute)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * fillIn
     */

    private function _fillIn($selector, $value)
    {
        $this->find($selector)->fillIn($value);

        return $this;
    }

    public function fillIn($selector, $value)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * setFile
     */

    private function _setFile($selector, $path)
    {
        $element = $this->find($selector);
        if (!$element->isDisplayed()) {
            $this->executeScript("
                arguments[0].style.setProperty('display', 'inherit', 'important');
                arguments[0].style.setProperty('visibility', 'visible', 'important');
                arguments[0].style.setProperty('width', 'auto', 'important');
                arguments[0].style.setProperty('height', 'auto', 'important');
            ", [$element->getNativeElement()]);
        }
        $element->sendKeys($path);

        return $this;
    }

    public function setFile($selector, $value)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * getValue
     */

    private function _getValue($selector)
    {
        return $this->getAttribute($selector, 'value');
    }

    public function getValue($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * getText
     */

    private function _getText($selector)
    {
        return $this->find($selector)->getText();
    }

    public function getText($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * select
     */

    private function _select($selector, $value)
    {
        $element = $this->find($selector)->getNativeElement();
        $select = new WebDriverSelect($element);
        $select->selectByValue($value);

        return $this;
    }

    public function select($selector, $value)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * jqcSelect
     */

    private function tryJqcSelect($selector, $value)
    {
        $script = "$(arguments[0]).val(arguments[1]).trigger('chosen:updated').trigger('liszt:updated');";

        $this->executeScript($script, [$selector, $value]);

        $actual = $this->getSelectedValue($selector);
        if ($actual != $value) {
            throw new Exception(
                'JQuery Choosen selection of value `' . $value . '` seems to have failed, `'. $actual .'` got selected instead.'
            );
        }

        return $this;
    }

    private function _jqcSelect($selector, $value)
    {
        return Spin::assertNoException(function() use ($selector, $value) {
            return $this->tryJqcSelect($selector, $value);
        }, $this->defaultTimeout, $this->defaultInterval, 'Could not select value (JQuery Chosen Select)');
    }

    public function jqcSelect($selector, $value)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * getSelectOptions
     */

    private function _getSelectOptions($selector)
    {
         $element = $this->find($selector)->click();
         if ($element->getAttribute('multiple')) {
             sleep(1); // seems needed, God knows why
         }
         $select = new WebDriverSelect($element->getNativeElement());
         foreach ($select->getOptions() as $option) {
             $options[$option->getAttribute('value')] = $option->getText();
         }
         $this->sendKeys(WebDriverKeys::ESCAPE);
         return $options;
    }

    public function getSelectOptions($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * getSelectedValue
     */

    private function _getSelectedValue($selector)
    {
        $select = new WebDriverSelect($this->find($selector)->getNativeElement());

        $option = $select->getFirstSelectedOption();

        return $option->getAttribute('value');
    }

    public function getSelectedValue($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * getSelectedText
     */

    private function _getSelectedText($selector)
    {
        $select = new WebDriverSelect($this->find($selector)->getNativeElement());

        $option = $select->getFirstSelectedOption();

        return $option->getText();
    }

    public function getSelectedText($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * multiSelect
     */

    private function _multiSelect($selector, array $values)
    {
        $element = $this->find($selector);
        $element->click();
        sleep(1);    // strangely, this is needed,
                     // otherwise Selenium throws a StaleElementException
                     // I don't have the faintest idea why

        $options = $element->find('option', ['unique' => false]);

        $this->driver->getKeyboard()->pressKey(WebDriverKeys::CONTROL);

        $matched = 0;

        foreach ($options as $option) {
            if (in_array($option->getValue(), $values)) {
                $matched++;
                if (!$option->isSelected()) {
                    $option->click();
                }
            } elseif ($option->isSelected()) {
                $option->click();
            }
        }

        $this->driver->getKeyboard()->releaseKey(WebDriverKeys::CONTROL);

        if ($matched !== count($values))
            throw new Exception('Could not select all values.');

        return $this;
    }

    public function multiSelect($selector, array $values)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * getSelectedValues
     */

    private function _getSelectedValues($selector)
    {
        $select = new WebDriverSelect($this->find($selector)->getNativeElement());

        $options = $select->getAllSelectedOptions();

        return array_map(function ($option) {
            return $option->getAttribute('value');
        }, $options);
    }

    public function getSelectedValues($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * click
     */

    private function _click($selector)
    {
        $this->find($selector)->click();

        return $this;
    }

    public function click($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * clickFirst
     */

    private function _clickFirst($selector, array $options = array())
    {
        $options['unique'] = false;

        $element = $this->find($selector, $options)[0];

        $element->click();

        return $this;
    }

    public function clickFirst($selector, array $options = array())
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    public function clickFirstVisible($selector)
    {
        return $this->clickFirst($selector, [
            'displayed' => true
        ]);
    }

    public function clickFirstVisibleAndEnabled($selector)
    {
        return $this->clickFirst($selector, [
            'displayed' => true,
            'enabled' => true
        ]);
    }

    /**
     * clickButtonNamed
     */

    private function _clickButtonNamed($name)
    {
        return $this->clickFirstVisibleAndEnabled('button[name=' . $name . ']');
    }

    public function clickButtonNamed($name)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * hover
     */

    private function _hover($selector)
    {
        $element = $this->find($selector)->getNativeElement();
        $this->driver->action()->moveToElement($element)->perform();

        return $this;
    }

    public function hover($selector)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * sendKeys
     */

    private function _sendKeys($keys)
    {
        $this->driver->getKeyboard()->sendKeys($keys);

        return $this;
    }

    public function sendKeys($keys)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    public function hasVisible($selector)
    {
        try {
            $this->find($selector, ['displayed' => true, 'timeout' => 0, 'unique' => false]);
            return true;
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * checkbox
     */

    private function _checkbox($selector, $on_off = null)
    {
        $checkbox = $this->find($selector);

        if (null === $on_off) {
            return $checkbox->isSelected();
        }

        if ($on_off xor $checkbox->isSelected()) {
            $checkbox->click();
        }

        return $this;
    }

    public function checkbox($selector, $on_off = null)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * clickLabelFor
     */

    private function _clickLabelFor($for)
    {
         $this->find("label[for=$for]")->click();

         return $this;
    }

    public function clickLabelFor($for)
    {
        return $this->wrap(__FUNCTION__, func_get_args());
    }

    /**
     * curl
     */

    public function curl($url = null, array $options = array())
    {
        if ($url === null)
            $url = $this->getCurrentURL();

        $ch = curl_init($url);

        $defaults = [CURLOPT_RETURNTRANSFER => 1];

        // Can't use array_merge here cuz keys are numeric
        foreach ($options as $option => $value) {
            $defaults[$option] = $value;
        }
        $options = $defaults;

        foreach ($options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        $cookies = [];
        foreach ($this->driver->manage()->getCookies() as $cookie) {
            $cookies[] = $cookie['name'].'='.$cookie['value'];
        }
        $cookies = implode(';', $cookies);

        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $ret = curl_exec($ch);
        curl_close($ch);

        return $ret;
    }

    public function xhr($url, array $options = array())
    {
        $script = <<<'EOS'

        var done = arguments[1];

        var oReq = new XMLHttpRequest();
        oReq.open("GET", arguments[0], true);
        oReq.responseType = "arraybuffer";

        oReq.onload = function (oEvent) {
            var arrayBuffer = oReq.response;
            if (arrayBuffer) {
                var byteArray = new Uint8Array(arrayBuffer);
                var len = byteArray.byteLength;
                var binary = '';
                for (var i = 0; i < len; i++) {
                    binary += String.fromCharCode(byteArray[i]);
                }
                done(window.btoa(binary));
            }
        };

        oReq.send(null);
EOS;

        return base64_decode($this->executeAsyncScript($script, [$url]));
    }

    public function takeScreenshot($filename)
    {
        $this->driver->takeScreenshot($filename . '.png');

        if (function_exists('imagecreatefrompng') && function_exists('imagejpeg') && file_exists($filename . '.png')) {
            $image = @imagecreatefrompng($filename . '.png');
            if ($image) {
                imagejpeg($image, $filename . '.jpg', 50);
                imagedestroy($image);
                unlink($filename . '.png');
                return $filename . '.jpg';
            }
        }

        return $filename . '.png';
    }
}
