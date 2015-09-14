# Real-Time Symfony App

An application used as part of a presentation for Symfony Live 2015: 

[Real-time Web Apps & Symfony. What are your options?](http://london2015.live.symfony.com/speakers#yui_3_17_2_1_1442233551897_235)

## Setup

### `app/config/parameters.yml`

Add Pusher configuration to your `parameters.yml` file:

```yml
parameters:
    ...
    pusher_app_id: YOUR_APP_ID
    pusher_app_key: YOUR_APP_KEY
    pusher_app_secret: YOUR_APP_SECRET
    ...
```
