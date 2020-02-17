# Kirby Webhook Plugin

This is a simple plugin that lets you configure custom webhooks.

---

## Installation

### Download

Download and copy this repository to `/site/plugins/webhooks`.

### Git submodule

```
git submodule add https://github.com/errnesto/kirby-plugin-webhooks.git site/plugins/webhooks
```

### Composer

```
composer require errnesto/kirby-plugin-webhooks
```

## Setup

This plugin looks for webhook configurations in the kirby `site` object.
So to create a webhook you could just edit the `content/site.txt` file:

```
Webhooks:

-
  url: >
    https://webhook.site/74a7ed53-7091-4871-a1d7-749417b24269
  payload: '{"secret":"123"}'
  triggers: page.create:after, page.update:after

```

The config must be stored under the key `Webhooks` as yaml array.  
Every webhook needs the following options:

- `url`: The URL to post to when a kirby hook is triggered
- `payload`: The body of the post request
- `triggers`: A comma seperated list of [kirby hooks](https://getkirby.com/docs/reference/system/options/hooks)

A blueprint to edit the webhooks in the panel could look like this:

```yaml
webhooks:
  label: Webhooks
  type: structure
  fields:
    url:
      label: URL
      type: url
    payload:
      label: Payload
      type: text
    triggers:
      label: Triggers
      type: tags
```

## Options

You can customize the requests the plugin sends with your own functions.  
Every function gets the following parameters:

- `$trigger`: The name of the hook that caused the webhook to be called
- `$webhook`: The webhook read from the site object. (This will include any field you define)
- `...$params`: All the params from the [kirby hook](https://getkirby.com/docs/reference/system/options/hooks).

You can set all of the following options by returning them in your `/site/config/config.php`:

### `getURL()`

```php
// dafault:
return [
  'errnesto.webhooks.getURL' => function ($trigger, $webhook, ...$params) {
    return $webhook['url'];
  }
];
```

### `getHeader()`

```php
return [
  // default:
  'errnesto.webhooks.getHeader' => function ($trigger, $webhook, ...$params) {
    return "Content-type: application/json\r\n";
  }
];
```

### `getMethod()`

```php
return [
  // default:
  'errnesto.webhooks.getMethod' => function ($trigger, $webhook, ...$params) {
    return "POST";
  }
];
```

### `getPayload()`

```php
return [
  // default
  'errnesto.webhooks.getPayload' => function ($trigger, $webhook, ...$params) {
    return $webhook['payload'];
  },
];
```

## TODO

- [ ] Add custom panel view and store webhooks somewhere else?

## License

MIT
