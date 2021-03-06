<?php
/**
 * Mage_Webhook_Model_User
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
class Mage_Webhook_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_User */
    protected $_user;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAclUser;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAuthorization;

    public function setUp()
    {
        $webApiId = 'web api id';

        $this->_mockAclUser = $this->getMockBuilder('Mage_Webapi_Model_Acl_User_Factory')
            ->setMethods(array('load', 'getRoleId', 'getSecret'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserFactory = $this->getMockBuilder('Mage_Webapi_Model_Acl_User_Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_mockAclUser));

        $this->_mockAclUser->expects($this->once())
            ->method('load')
            ->with($this->equalTo($webApiId));

        $mockRLocatorFactory = $this->getMockBuilder('Mage_Webapi_Model_Authorization_Role_Locator_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAclUser->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue('role_id'));

        $mockRLocatorFactory->expects($this->once())
            ->method('create')
            ->with(array('data' => array('roleId' => 'role_id')))
            ->will($this->returnValue('role_locator'));

        $this->_mockAuthorization = $this->getMockBuilder('Magento_Authorization')
            ->setMethods(array('isAllowed'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockAclPolicy = $this->getMockBuilder('Mage_Webapi_Model_Authorization_Policy_Acl')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAuthFactory = $this->getMockBuilder('Magento_Authorization_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAuthFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_mockAuthorization));

        $this->_user = new Mage_Webhook_Model_User(
            $mockUserFactory,
            $mockRLocatorFactory,
            $mockAclPolicy,
            $mockAuthFactory,
            $webApiId
        );
    }

    public function testGetSharedSecret()
    {
        $sharedSecret = 'some random shared secret';

        $this->_mockAclUser->expects($this->once())
            ->method('getSecret')
            ->will($this->returnValue($sharedSecret));

        $this->assertSame($sharedSecret, $this->_user->getSharedSecret());
    }

    public function testHasPermission()
    {
        $allowedTopic = 'allowed topic';
        $notAllowedTopic = 'not allowed topic';

        $this->_mockAuthorization->expects($this->any())
            ->method('isAllowed')
            ->will(
                $this->returnValueMap(
                    array(
                         array($allowedTopic, null, true),
                         array($notAllowedTopic, null, false)
                    )
                )
            );

        $this->assertTrue($this->_user->hasPermission($allowedTopic));
        $this->assertFalse($this->_user->hasPermission($notAllowedTopic));
    }
}
