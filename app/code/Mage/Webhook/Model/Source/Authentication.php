<?php
/**
 * The list of available authentication types
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Webhook_Model_Source_Authentication
{
    /** @var Mage_Core_Model_Translate $_translator */
    private $_translator;

    /** @var array $_authenticationTypes */
    private $_authenticationTypes;


    /**
     * @param array $authenticationTypes
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(array $authenticationTypes, Mage_Core_Model_Translate $translator)
    {
        $this->_translator = $translator;
        $this->_authenticationTypes = $authenticationTypes;
    }

    /**
     * Get available authentication types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_authenticationTypes;

    }

    /**
     * Return authentications for use by a form
     *
     * @return array
     */
    public function getAuthenticationsForForm()
    {
        $elements = array();
        foreach ($this->_authenticationTypes as $authName => $authentication) {
            $elements[] = array(
                'label' => $this->_translator->translate(array($authentication)),
                'value' => $authName,
            );
        }

        return $elements;
    }
}
