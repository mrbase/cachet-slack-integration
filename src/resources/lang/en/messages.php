<?php
/**
 * This file is part of the CachetSlackIntegration package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'component' => [
        'status_update' => 'Component *:name* changed status to *:status*.'
    ],

    'incident' => [
        'field_labels' => [
            'status'    => 'Status',
            'component' => 'Component',
        ],
        'created' => [
            'fallback' => 'New incident: #:id :message',
            'header'   => 'New incident reported.',
            'title'    => ':name',
        ],

        'updated' => [
            'fallback' => 'Incident: #:id :name - :state',
            'header'   => 'Incident: #:id :name - :state',
            'title'    => '#:id - :name',
            'text'     => 'Incident changed status from *:old_status* to *:new_status*:'
        ],
    ],
];
