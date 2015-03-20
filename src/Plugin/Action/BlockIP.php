<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\BlockIP.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\ban\BanIpManagerInterface;
use Drupal\Component\Utility\String;
use Drupal\Core\Annotation\Action;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Annotation\ContextDefinition;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\RulesActionBase;
use Symfony\Component\HttpFoundation\Request;
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
   * The corresponding Request
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ban.ip_manager'),
      $container->get('request')
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
   * @param \Symfony\Component\HttpFoundation\Request
   *   The Symfony HTTP Foundation
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BanIpManagerInterface $banManager, Request $request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->banManager = $banManager;
    $this->request = $request;
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

    if (!isset($ip)) {
      $ip = $this->request->getClientIp();
    }

    $this->banManager->banIp($ip);
  }
}
