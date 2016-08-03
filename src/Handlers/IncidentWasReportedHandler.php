<?php

namespace Mrbase\CachetSlackIntegration\Handlers;

/**
 * Class IncidentWasReportedHandler
 *
 * @package Mrbase\CachetSlackIntegration
 */
class IncidentWasReportedHandler extends BaseHandler
{
    /**
     * @var array
     */
    private $attachment = [];

    /**
     * IncidentWasReportedHandler constructor.
     *
     * @param int      $status
     * @param string   $name
     * @param string   $message
     * @param int|null $componentId
     */
    public function __construct($status, $name, $message, $componentId)
    {
        $replacements = [
            'message' => $message,
            'name'    => $name,
        ];

        $this->attachment = [
            'fallback'   => trans('slack::messages.incident.created.fallback', $replacements),
            'color'      => $this->statusToColor($status),
            'title'      => trans('slack::messages.incident.created.title', $replacements),
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
        return $this->sendAttachment($this->attachment, trans('slack::messages.incident.created.header'));
    }
}
