<?php
/**
 * This file is part of the CachetSlackIntegration package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mrbase\CachetSlackIntegration;

use CachetHQ\Cachet\Models\Component;

/**
 * Class Utils
 *
 * @package Mrbase\CachetSlackIntegration
 * @author  Ulrik Nielsen <me@ulrik.co>
 */
class Utils
{
    /**
     * @var array
     */
    private static $changes = [];

    /**
     * @param string $model
     * @param array  $data
     */
    public static function registerChanges($model, array $data)
    {
        // For some reason, the changed/saving event is called twice on the model, we only need the first.
        if (!empty(self::$changes[$model])) {
            return;
        }

        self::$changes[$model] = $data;
    }

    /**
     * @param string $model
     *
     * @return array
     */
    public static function getChanges($model)
    {
        if (isset(self::$changes[$model])) {
            return self::$changes[$model];
        }

        return [];
    }

    /**
     * @param string $componentId
     *
     * @return string
     */
    public static function getComponentStatus($componentId = '')
    {
        if ('' == $componentId) {
            return 'n/a';
        }

        $statuses  = trans('cachet.components.status');
        $component = Component::find($componentId);

        if ($component instanceof Component) {
            return $component->name.': *'.$statuses[$component->status].'*';
        }

        return 'n/a';
    }
}
