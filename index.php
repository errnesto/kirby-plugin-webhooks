<?php

function getHooks () {
  $groupByTrigger = function($hooks, $webhook) {
    $currentTriggers = $webhook->triggers()->split();

    return array_reduce($currentTriggers, function ($hooks, $trigger) use ($webhook) {
      if (!$hooks[$trigger]) $hooks[$trigger] = array();
      $hooks[$trigger][] = $webhook->toArray();

      return $hooks;
    }, $hooks);
  };

  $webhooks = site()->webhooks()->toStructure()->values();
  $byTrigger = array_reduce($webhooks, $groupByTrigger, array());

  return array_map(function ($triggerHooks) {
    return function (...$params) use ($triggerHooks) {
      foreach ($triggerHooks as $webhook) {
        $getHeader = option('errnesto.webhooks.getHeader');
        $getMethod = option('errnesto.webhooks.getMethod');
        $getPayload = option('errnesto.webhooks.getPayload');

        $options = array(
            'http' => array(
                'header'  => $getHeader($webhook, ...$params),
                'method'  => $getMethod($webhook, ...$params),
                'content' => $getPayload($webhook, ...$params)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($webhook['url'], false, $context);
        }
    };
  }, $byTrigger);
}

Kirby::plugin('errnesto/webhooks', [
  'options' => [
    'getHeader' => function ($webhook, ...$params) {
      return "Content-type: application/json\r\n";
    },
    'getMethod' => function ($webhook, ...$params) {
      return "POST";
    },
    'getPayload' => function ($webhook, ...$params) {
      return $webhook['payload'];
    }
  ],
  'hooks' => getHooks()
]);
