# Kirby Webhook Plugin

This is a simple plugin that lets you configure custom webhooks.

---

## Usage

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
A single webhook has the following options:

- `url`: The URL to post to when a kiry hook is triggered
- `payload`: The body of the post request (for now only json is supported)
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

## TODO

- [ ] Add config option to provide a custom payload function
- [ ] Add extra panel view?

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

_Additional instructions on how to configure the plugin (e.g. blueprint setup, config options, etc.)_

## Options

_Document the options and APIs that this plugin offers_

## License

MIT
