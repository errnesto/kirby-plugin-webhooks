<?php

function getHooks () {
  $groupByTrigger = function($hooks, $webhook) {
    $currentTriggers = $webhook->triggers()->split();

    return array_reduce($currentTriggers, function ($hooks, $trigger) use ($webhook) {
      if (!$hooks[$trigger]) $hooks[$trigger] = array();
      $hooks[$trigger][] = array(
        'trigger' => $trigger,
        'data' => $webhook->toArray()
      );

      return $hooks;
    }, $hooks);
  };

  $webhooks = site()->webhooks()->toStructure()->values();
  $byTrigger = array_reduce($webhooks, $groupByTrigger, array());

  return array_map(function ($triggerHooks) {
    return function (...$params) use ($triggerHooks) {
      foreach ($triggerHooks as $webhook) {
        $getURL = option('errnesto.webhooks.getURL');
        $getHeader = option('errnesto.webhooks.getHeader');
        $getMethod = option('errnesto.webhooks.getMethod');
        $getPayload = option('errnesto.webhooks.getPayload');

        $options = array(
          'http' => array(
            'header'  => $getHeader($webhook['trigger'], $webhook['data'], ...$params),
            'method'  => $getMethod($webhook['trigger'], $webhook['data'],...$params),
            'content' => $getPayload($webhook['trigger'], $webhook['data'], ...$params)
          )
        );

        $url = $getURL($webhook['trigger'], $webhook['data'], ...$params);
        $context  = stream_context_create($options);
        file_get_contents($url, false, $context);
        }
    };
  }, $byTrigger);
}

Kirby::plugin('errnesto/webhooks', [
  'options' => [
    'getURL' => function ($trigger, $webhook, ...$params) {
      return $webhook['url'];
    },
    'getHeader' => function ($trigger, $webhook, ...$params) {
      return "Content-type: application/json\r\n";
    },
    'getMethod' => function ($trigger, $webhook, ...$params) {
      return "POST";
    },
    'getPayload' => function ($trigger, $webhook, ...$params) {
      return $webhook['payload'];
    }
  ],
  'hooks' => getHooks()
]);
