<?php
/**
 * This file is part of the CachetSlackIntegration package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mrbase\CachetSlackIntegration\Handlers\Events\Incident;

use CachetHQ\Cachet\Events\Incident\IncidentWasUpdatedEvent;
use Maknz\Slack\Facades\Slack;
use Mrbase\CachetSlackIntegration\Utils;

/**
 * Class IncidentUpdated
 *
 * @package Mrbase\CachetSlackIntegration
 * @author  Ulrik Nielsen <me@ulrik.co>
 */
class IncidentUpdated
{
    /**
     * @param IncidentWasUpdatedEvent $event
     */
    public function handle(IncidentWasUpdatedEvent $event)
    {
        if (false === config('slack.endpoint', false)) {
            return;
        }

        $changes = Utils::getChanges('incident');

        $oldStatus = isset($changes['status'])
            ? $changes['status']
            : $event->incident->status;

        $newStatus = $event->incident->status;
        $statuses  = trans('cachet.incidents.status');
        $closed    = max(array_keys($statuses));

        $color = 'danger';
        $state = 'updated';
        if ($newStatus == $closed) {
            $color = 'good';
            $state = 'closed';
        } elseif (in_array($newStatus, [2, 3])) {
            $color = 'warning';
        }

        $replacements = [
            'id'         => $event->incident->id,
            'message'    => $event->incident->message,
            'name'       => $event->incident->name,
            'new_status' => $statuses[$newStatus],
            'old_status' => $statuses[$oldStatus],
            'state'      => $state,
        ];

        $message = isset($changes['message'])
            ? $changes['message']
            : trans('slack::messages.incident.updated.text', $replacements);

        $attachment = [
            'fallback'   => trans('slack::messages.incident.updated.fallback', $replacements),
            'color'      => $color,
            'title'      => trans('slack::messages.incident.updated.title', $replacements),
            'title_link' => url('status-page'),
            'text'       => $message,
            'fields'     => [
                [
                    'title' => trans('slack::messages.incident.field_labels.status'),
                    'value' => $event->incident->humanStatus,
                    'short' => true,
                ],
                [
                    'title' => trans('slack::messages.incident.field_labels.component'),
                    'value' => Utils::getComponentStatus($event->incident['component_id']),
                    'short' => true,
                ],
            ],
            'mrkdwn_in' => ['pretext', 'text']
        ];

        return Slack::attach($attachment)->send(trans('slack::messages.incident.updated.header', $replacements));
    }
}
