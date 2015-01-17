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
   * @var \Drupal\rules\Engine\RulesActionInterface
   */
  protected $action;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|\Drupal\ban\BanIpManagerInterface
   */
  protected $banManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    // We need the ban module.
    $this->enableModule('ban');
    $this->banManager = $this->getMock('Drupal\ban\BanIpManagerInterface');
    $this->container->set('ban.ip_manager', $this->banManager);

    $this->action = $this->actionManager->createInstance('rules_block_ip');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary()
   */
  public function testSummary() {
    $this->assertEquals('Blocks an IP address', $this->action->summary());
  }

  /**
   * Tests the action execution.
   *
   * @covers ::execute()
   */
  public function testActionExecution() {
    $ip = '192.155.122.111';
    $this->action->setContextValue('ip', $ip);

    $this->banManager->expects($this->once())
      ->method('banIp')
      ->with($ip);

    $this->action->execute();
  }
}
