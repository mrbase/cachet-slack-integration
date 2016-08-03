# Cachet Slack integration

This package adds [Slack](https://slack.com) integration to your [Cachet](https://cachethq.io/) installation.

When set up it will send notifications to a slack channel when a incident is either added or updated - and when
components are added or updated.

## Install

    composer require mrbase/cachet-slack-integration

Add provider to your config/app.php providers

    'providers' => [
        ...
        Maknz\Slack\SlackServiceProvider::class,
        Mrbase\CachetSlackIntegration\ServiceProvider::class,
    ],

And to aliases:

    'aliases' => [
        ...
        'Slack' => Maknz\Slack\Facades\Slack::class,
    ],

Publish config and translations:

    php artisan vendor:publish

## Setup

Edit `config/slack.php` and replace the following with your own settings, update any other settings as toy see fit:

    'endpoint' => '',

The endpoint url is something like: https://hooks.slack.com/services/XXXX/XXXX/XXX

Remember to clear cache and config.

Done, Cachet will now send notifications to your Slack channel on incident events.


## Note

In the current version only one Slack account/channel is supported. I have plans for adding support for Slack subscriptions just as you subscribe to email notifications.


Sponsored by [Unity](http://unity3d.com)
