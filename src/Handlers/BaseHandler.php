<?php

namespace Mrbase\CachetSlackIntegration\Handlers;

use CachetHQ\Cachet\Models\Component;
use Maknz\Slack\Facades\Slack;

/**
 * Class BaseHandler
 *
 * @package Mrbase\CachetSlackIntegration
 */
abstract class BaseHandler
{
    /**
     * @return mixed
     */
    abstract public function send();

    /**
     * Map status ids to a slack color.
     *
     * @param int $status
     *
     * @return string
     */
    protected function statusToColor($status)
    {
        $colormap = [
            0 => 'good',    // 'Scheduled'
            1 => 'danger',  // 'Investigating'
            2 => 'warning', // 'Identified'
            3 => 'warning', // 'Watching'
            4 => 'good',    // 'Fixed'
        ];

        if (isset($colormap[$status])) {
            return $colormap[$status];
        }

        return '';
    }

    /**
     * Find the status on a component.
     *
     * @param string $componentId
     *
     * @return string
     */
    protected function getComponentStatus($componentId = '')
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

    /**
     * @param int $id
     *
     * @return string
     */
    protected function translateIncidentStatus($id)
    {
        return trans('cachet.incidents.status')[$id];
    }

    /**
     * @param int $id
     *
     * @return string
     */
    protected function translateComponentStatus($id)
    {
        return trans('cachet.components.status')[$id];
    }

    /**
     * Send Slack message as attachment.
     *
     * @param array  $attachment
     * @param string $title
     *
     * @return mixed
     */
    protected function sendAttachment($attachment, $title)
    {
        return Slack::attach($attachment)->send($title);
    }

    /**
     * Send plain Slack message.
     *
     * @param string $message
     * @return mixed
     */
    protected function sendMessage($message)
    {
        return Slack::send($message);
    }
}
