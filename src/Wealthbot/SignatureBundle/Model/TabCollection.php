<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 18.09.13
 * Time: 19:03
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\SignatureBundle\Model;


use Wealthbot\SignatureBundle\Model\Tab\AbstractTab;

class TabCollection
{
    private $tabs;

    public function __construct(array $tabs = array())
    {
        $this->tabs = $tabs;
    }

    /**
     * Add tab element
     *
     * @param AbstractTab $tab
     */
    public function addTab(AbstractTab $tab)
    {
        $this->tabs[] = $tab;
    }

    /**
     * Remove tab element by key
     *
     * @param $key
     * @return AbstractTab|null
     */
    public function remove($key)
    {
        if (isset($this->tabs[$key])) {
            $removed = $this->tabs[$key];
            unset($this->tabs[$key]);

            return $removed;
        }

        return null;
    }

    /**
     * Remove tab element
     *
     * @param AbstractTab $tab
     * @return bool
     */
    public function removeTab(AbstractTab $tab)
    {
        $key = array_search($tab, $this->tabs, true);
        if ($key !== false) {
            unset($this->tabs[$key]);

            return true;
        }

        return false;
    }

    /**
     * Is contains element with key $key
     *
     * @param $key
     * @return bool
     */
    public function containsKey($key)
    {
        return isset($this->tabs[$key]);
    }

    /**
     * Is contains element
     *
     * @param AbstractTab $tab
     * @return bool
     */
    public function contains(AbstractTab $tab)
    {
        return in_array($tab, $this->tabs, true);
    }

    /**
     * Get count of elements
     *
     * @return int
     */
    public function count()
    {
        return count($this->tabs);
    }

    /**
     * Convert tabs data to array
     *
     * @return array
     * @throws \RuntimeException
     */
    public function toArray()
    {
        $result = array();
        $textTabs = array();
        $checkboxTabs = array();
        $radioTabs = array('radios' => array());

        foreach ($this->tabs as $tab) {
            if (!($tab instanceof AbstractTab)) {
                throw new \RuntimeException(sprintf(
                    'Object must be instance of %s %s given.',
                    'AbstractTab',
                    get_class($tab)
                ));
            }

            $type = $tab->getType();
            if ($type === AbstractTab::TYPE_TEXT) {
                $textTabs[] = array(
                    'tabLabel' => $tab->getTabLabel(),
                    'value' => $tab->getValue()
                );
            } elseif ($type === AbstractTab::TYPE_RADIO_GROUP) {
                $radioTabs[$tab->getGroupName()]['groupName'] = $tab->getGroupName();
                $radioTabs[$tab->getGroupName()]['radios'][] = array(
                    'selected' => $tab->getSelected(),
                    'value' => $tab->getValue()
                );
            } elseif ($type === AbstractTab::TYPE_CHECKBOX) {
                $checkboxTabs[] = array(
                    'tabLabel' => $tab->getTabLabel(),
                    'selected' => $tab->getSelected()
                );
            }
        }

        if (!empty($textTabs)) {
            $result['textTabs'] = $textTabs;
        }

        if (!empty($checkboxTabs)) {
            $result['checkboxTabs'] = $checkboxTabs;
        }

        foreach ($radioTabs as $radioTab) {
            if (!empty($radioTab)) {
                $result['radioGroupTabs'][] = $radioTab;
            }
        }

        return $result;
    }
}