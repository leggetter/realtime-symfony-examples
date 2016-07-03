# Real-Time Symfony App

An application used as part of presentations at:

* Real-time Web Apps & Symfony. What are your options? - Symfony Live 2015
  * [Schedule](http://london2015.live.symfony.com/speakers#yui_3_17_2_1_1442233551897_235)
  * [Slides](http://leggetter.github.io/realtime-symfony/)
  * [Video](https://www.youtube.com/watch?v=LX2KoVK7mqA)
* Real-time Web Apps & PHP. What are your options? - CloudConf 2016
  * [Schedule](http://2016.cloudconf.it/schedule.html)
  * [Slides](http://leggetter.github.io/realtime-php/)
  * Video - coming soon

## Setup

### `app/config/pusher.yml`

Rename `app/config/pusher.yml.example` to `pusher.yml` and add your Pusher application credentials:

```yml
parameters:
    pusher.app_id: YOUR_APP_ID
    pusher.app_key: YOUR_APP_KEY
    pusher.app_secret: YOUR_APP_SECRET
```

### `app/config/nexmo.yml`

If you also want to send SMS when a message is posted you will need to sign up for a [Nexmo account](https://www.nexmo.com) and add your credentials to a `nexmo.yml` configuration file.

Rename `app/config/nexmo.yml.example` to `nexmo.yml` and add your Nexmo API credentials:

```yml
parameters:
    nexmo.api_key: YOUR_API_KEY
    nexmo.api_secret: YOUR_API_SECRET
    nexmo.from_name: Nexmo
```
