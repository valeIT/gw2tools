<?php

/*
 * This file is part of the Arnapou GW2Tools package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Tools\Api;

use Arnapou\GW2Api\Exception\InvalidTokenException;
use Arnapou\Toolbox\Exception\Exception;
use Arnapou\Toolbox\Functions\Directory;

class Menu {

    /**
     *
     * @var string
     */
    protected $label;

    /**
     *
     * @var array
     */
    protected $items = [];

    /**
     * 
     * @param string $label
     */
    public function __construct($label) {
        $this->label = $label;
    }

    /**
     * 
     * @param string $page
     * @param string $label
     * @param string $uri
     * @return Menu
     */
    public function addItem($page, $label, $uri = null) {
        $this->items[] = [
            'separator' => false,
            'page'      => $page,
            'uri'       => empty($uri) ? $page . '/' : $uri,
            'label'     => $label,
            'right'     => $page,
        ];
        return $this;
    }

    /**
     * 
     * @return Menu
     */
    public function addSeparator() {
        $this->items[] = [
            'separator' => true,
        ];
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * 
     * @param User $user
     * @return array
     */
    public function getItems(User $user = null) {
        if ($user === null) {
            return $this->items;
        }
        $items = [];
        foreach ($this->items as $item) {
            if (empty($item['right']) || $user->hasRight($item['right'])) {
                $items[] = $item;
            }
        }
        return $this->trimSeparators($items);
    }

    /**
     * 
     * @param array $items
     * @return array
     */
    protected function trimSeparators($items) {
        if (empty($items)) {
            return [];
        }
        $n = count($items);

        $offsetStart = 0;
        for ($i = 0; $i < $n; $i++) {
            if (!$items[$i]['separator']) {
                break;
            }
            $offsetStart++;
        }

        $offsetEnd = $n - 1;
        for ($i = $n - 1; $i >= 0; $i--) {
            if (!$items[$i]['separator']) {
                break;
            }
            $offsetEnd--;
        }

        $nbItems = $offsetEnd - $offsetStart + 1;
        if ($nbItems <= 0) {
            return [];
        }
        return array_slice($items, $offsetStart, $nbItems);
    }

    /**
     * 
     * @param string $label
     * @return Menu
     */
    static public function create($label) {
        return new self($label);
    }

}