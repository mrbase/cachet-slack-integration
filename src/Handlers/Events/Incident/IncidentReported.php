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

use CachetHQ\Cachet\Events\Incident\IncidentWasReportedEvent;
use Maknz\Slack\Facades\Slack;
use Mrbase\CachetSlackIntegration\Utils;

/**
 * Class IncidentReported
 *
 * @package Mrbase\CachetSlackIntegration
 * @author  Ulrik Nielsen <me@ulrik.co>
 */
class IncidentReported
{
    /**
     * @param IncidentWasReportedEvent $event
     */
    public function handle(IncidentWasReportedEvent $event)
    {
        if (false === config('slack.endpoint', false)) {
            return;
        }

        $replacements = [
            'message' => $event->incident->message,
            'name'    => $event->incident->name,
        ];

        $attachment = [
            'fallback'   => trans('slack::messages.incident.created.fallback', $replacements),
            'color'      => 'danger',
            'title'      => trans('slack::messages.incident.created.title', $replacements),
            'title_link' => url('status-page'),
            'text'       => $event->incident->message,
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

        return Slack::attach($attachment)->send(trans('slack::messages.incident.created.header'));
    }
}
