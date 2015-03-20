<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\BlockIPTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

use Drupal\Tests\rules\Integration\RulesIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Action\BlockIP
 * @group rules_action
 */
class BlockIPTest extends RulesIntegrationTestBase {

  /**
   * The action to be tested.
   *
   * @var \Drupal\rules\Core\RulesActionInterface
   */
  protected $action;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|\Drupal\ban\BanIpManagerInterface
   */
  protected $banManager;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Request
   */
  protected $request;
  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    // We need the ban module.
    $this->enableModule('ban');
    $this->banManager = $this->getMock('Drupal\ban\BanIpManagerInterface');
    $this->container->set('ban.ip_manager', $this->banManager);

    // Mock Symfony HTTPFoundation
    $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
    $this->container->set('request', $this->request);

    $this->action = $this->actionManager->createInstance('rules_block_ip');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary() {
    $this->assertEquals('Blocks an IP address', $this->action->summary());
  }

  /**
   * Tests the action execution with Context IP.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithContextIP() {
    $ip = '192.155.122.111';
    $this->action->setContextValue('ip', $ip);

    $this->banManager->expects($this->once())
      ->method('banIp')
      ->with($ip);

    $this->action->execute();

  }

  /**
   * Tests the action execution without Context IP.
   *
   * Should fallback to the current ip.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithoutContextIP() {

    $this->action->setContextValue('ip', NULL);
    $ip = '12.12.12.12';

    $this->request->expects($this->once())
      ->method('getClientIP')
      ->will($this->returnValue($ip));

    $this->banManager->expects($this->once())
      ->method('banIp')
      ->will($this->returnValue($ip));

    $this->action->execute();

  }
}
