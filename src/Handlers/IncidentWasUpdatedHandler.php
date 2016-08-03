<?php

namespace Mrbase\CachetSlackIntegration\Handlers;

/**
 * Class IncidentWasUpdatedHandler
 *
 * @package Mrbase\CachetSlackIntegration
 */
class IncidentWasUpdatedHandler extends BaseHandler
{
    /**
     * @var array
     */
    private $attachment = [];

    /**
     * @var array
     */
    private $replacements = [];

    /**
     * IncidentWasUpdatedHandler constructor.
     *
     * @param int    $id
     * @param int    $status
     * @param string $name
     * @param string $message
     * @param int    $componentId
     * @param array  $changes
     */
    public function __construct($id, $status, $name, $message, $componentId, array $changes = [])
    {
        $oldStatus = isset($changes['status'])
            ? $changes['status']
            : $status;

        $newStatus = $status;
        $statuses  = trans('cachet.incidents.status');
        $closed    = max(array_keys($statuses));

        $state = $newStatus == $closed
            ? 'closed'
            : 'updated';

        $this->replacements = [
            'id'         => $id,
            'message'    => $message,
            'name'       => $name,
            'new_status' => $statuses[$newStatus],
            'old_status' => $statuses[$oldStatus],
            'state'      => $state,
        ];

        $message = isset($changes['message'])
            ? $changes['message']
            : trans('slack::messages.incident.updated.text', $this->replacements);

        $this->attachment = [
            'fallback'   => trans('slack::messages.incident.updated.fallback', $this->replacements),
            'color'      => $this->statusToColor($newStatus),
            'title'      => trans('slack::messages.incident.updated.title', $this->replacements),
            'title_link' => route('status-page'),
            'text'       => $message,
            'fields'     => [
                [
                    'title' => trans('slack::messages.incident.field_labels.status'),
                    'value' => $this->translateIncidentStatus($status),
                    'short' => true,
                ],
                [
                    'title' => trans('slack::messages.incident.field_labels.component'),
                    'value' => $this->getComponentStatus($componentId),
                    'short' => true,
                ],
            ],
            'mrkdwn_in' => ['pretext', 'text', 'fields']
        ];
    }

    /**
     * Send the slack message.
     *
     * @return mixed
     */
    public function send()
    {
        return $this->sendAttachment($this->attachment, trans('slack::messages.incident.updated.header', $this->replacements));
    }
}
