<?php
/**
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Log_Model_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shellMock;

    /**
     * @var Mage_Log_Model_Shell
     */
    protected $_model;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Mage_Log_Model_Shell_Command_Factory', array(), array(), '', false);
        $filesystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_model = $this->getMock('Mage_Log_Model_Shell',
            array('_applyPhpVariables'),
            array($this->_factoryMock, $filesystemMock, 'entryPoint.php')
        );
    }

    public function testRunWithShowHelp()
    {
        $this->expectOutputRegex('/Usage\:  php -f entryPoint\.php/');
        $this->_model->setRawArgs(array('h'));
        $this->_factoryMock->expects($this->never())->method('createCleanCommand');
        $this->_factoryMock->expects($this->never())->method('createStatusCommand');
        $this->_model->run();
    }

    public function testRunWithCleanCommand()
    {
        $this->expectOutputRegex('/clean command message/');
        $this->_model->setRawArgs(array('clean', '--days', 10));
        $commandMock = $this->getMock('Mage_Log_Model_Shell_CommandInterface');
        $this->_factoryMock->expects($this->once())
            ->method('createCleanCommand')
            ->with(10)
            ->will($this->returnValue($commandMock));
        $commandMock->expects($this->once())->method('execute')->will($this->returnValue('clean command message'));
        $this->_factoryMock->expects($this->never())->method('createStatusCommand');
        $this->_model->run();
    }

    public function testRunWithStatusCommand()
    {
        $this->expectOutputRegex('/status command message/');
        $this->_model->setRawArgs(array('status'));
        $commandMock = $this->getMock('Mage_Log_Model_Shell_CommandInterface');
        $this->_factoryMock->expects($this->once())
            ->method('createStatusCommand')
            ->will($this->returnValue($commandMock));
        $commandMock->expects($this->once())->method('execute')->will($this->returnValue('status command message'));
        $this->_factoryMock->expects($this->never())->method('createCleanCommand');
        $this->_model->run();
    }

    public function testRunWithoutCommand()
    {
        $this->expectOutputRegex('/Usage\:  php -f entryPoint\.php/');
        $this->_factoryMock->expects($this->never())->method('createStatusCommand');
        $this->_factoryMock->expects($this->never())->method('createCleanCommand');
        $this->_model->run();
    }
}