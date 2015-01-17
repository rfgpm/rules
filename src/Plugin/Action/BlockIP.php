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
 */
class BlockIP extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\ban\BanIpManagerInterface $banManger
   */
  protected $banManager;

  /**
   * @param \Drupal\ban\BanIpManagerInterface $banManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BanIpManagerInterface $banManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->banManager = $banManager;
  }

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
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('Blocks an IP address');
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $ip = String::checkPlain($this->getContextValue('ip'));
    $this->banManager->banIp($ip);
  }
}
