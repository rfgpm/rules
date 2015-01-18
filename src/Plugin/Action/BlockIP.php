<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\BlockIP.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\ban\BanIpManagerInterface;
use Drupal\Component\Utility\String;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Engine\RulesActionBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the 'Block IP' action.
 *
 * @Action(
 *   id = "rules_block_ip",
 *   label = @Translation("Block IP"),
 *   category = @Translation("System"),
 *   context = {
 *     "ip" = @ContextDefinition("string",
 *       label = @Translation("IP Address")
 *     )
 *   }
 * )
 *
 * @todo: Add access callback information from Drupal 7.
 * @todo: We should maybe use a dedicated data type for the ip address, as we
 * do in Drupal 7.
 * @todo: This action depends on the ban module. We need to have a way to
 * specify this.
 */
class BlockIP extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The ban manager service used to ban the IP.
   *
   * @var \Drupal\ban\BanIpManagerInterface $banManger
   */
  protected $banManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ban.ip_manager')
    );
  }

  /**
   * Constructs the BlockIP object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ban\BanIpManagerInterface $banManager
   *   The ban manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BanIpManagerInterface $banManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->banManager = $banManager;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('Blocks an IP address');
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $ip = $this->getContextValue('ip');
    $this->banManager->banIp($ip);
  }
}
