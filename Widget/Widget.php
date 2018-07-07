<?php

/**
 * This file is part of the pdAdmin pdWidget package.
 *
 * @package     pdWidget
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-widget
 */

namespace Pd\WidgetBundle\Widget;

use Pd\WidgetBundle\Builder\ItemInterface;
use Pd\WidgetBundle\Event\WidgetEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Widget.
 *
 * @author  Ramazan Apaydın <iletisim@ramazanapaydin.com>
 */
class Widget implements WidgetInterface
{
    /**
     * Widget Storage.
     *
     * @var array|ItemInterface[]
     */
    private $widgets = [];

    /**
     * @var AuthorizationCheckerInterface
     */
    private $security;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $checkRole;

    /**
     * @param AuthorizationCheckerInterface $security
     * @param EventDispatcherInterface      $eventDispatcher
     */
    public function __construct(AuthorizationCheckerInterface $security, EventDispatcherInterface $eventDispatcher)
    {
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get Widgets.
     *
     * @return array|ItemInterface[]
     */
    public function getWidgets($checkRole = true)
    {
        // Check Role
        $this->checkRole = $checkRole;

        // Dispatch Event
        if (!$this->widgets) {
            $this->eventDispatcher->dispatch(WidgetEvent::WIDGET_START, new WidgetEvent($this));
        }

        return $this->widgets;
    }

    /**
     * Add Widget.
     *
     * @param ItemInterface $item
     *
     * @return $this
     */
    public function addWidget(ItemInterface $item)
    {
        // Check Security
        if ($this->checkRole) {
            if ($item->getRole() && !$this->security->isGranted($item->getRole())) {
                return $this;
            }
        }

        // Add
        $this->widgets[$item->getId()] = $item;

        return $this;
    }

    /**
     * Remove Widget.
     *
     * @param string $widgetId
     *
     * @return $this
     */
    public function removeWidget(string $widgetId)
    {
        if (isset($this->widgets[$widgetId])) {
            unset($this->widgets[$widgetId]);
        }

        return $this;
    }
}
