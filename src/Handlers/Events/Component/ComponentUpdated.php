<?php
/**
 * This file is part of the CachetSlackIntegration package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unity\CachetSlackIntegration\Handlers\Events\Component;

use CachetHQ\Cachet\Events\Component\ComponentWasUpdatedEvent;
use Maknz\Slack\Facades\Slack;
use Unity\CachetSlackIntegration\Utils;

/**
 * Class ComponentUpdated
 *
 * @package Unity\CachetSlackIntegration
 * @author  Ulrik Nielsen <me@ulrik.co>
 */
class ComponentUpdated
{
    /**
     * @param ComponentWasUpdatedEvent $event
     */
    public function handle(ComponentWasUpdatedEvent $event)
    {
        if (false === config('slack.endpoint', false)) {
            return;
        }

        $changes   = Utils::getChanges('component');
        $newStatus = $event->component->status;
        $oldStatus = isset($changes['status'])
            ? $changes['status']
            : $newStatus;

        if ($oldStatus == $newStatus) {
            return;
        }

        $statuses = trans('cachet.components.status');
        $message  = trans('slack::messages.component.status_update', [
            'name'       => $event->component->name,
            'old_status' => $statuses[$oldStatus],
            'new_status' => $statuses[$newStatus],
        ]);

        Slack::send($message);
    }
}
