# Real-Time Symfony App

An application used as part of a presentation for Symfony Live 2015: 

[Real-time Web Apps & Symfony. What are your options?](http://london2015.live.symfony.com/speakers#yui_3_17_2_1_1442233551897_235)

## Setup

### `app/config/pusher.yml`

Rename `app/config/pusher.yml.example` to `pusher.yml` and add your Pusher application credentails:

```yml
parameters:
    pusher.app_id: YOUR_APP_ID
    pusher.app_key: YOUR_APP_KEY
    pusher.app_secret: YOUR_APP_SECRET
```
