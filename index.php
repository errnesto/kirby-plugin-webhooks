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

  $webhooks = site()->hooks()->toStructure()->values();
  $byTrigger = array_reduce($webhooks, $groupByTrigger, array());

  return array_map(function ($triggerHooks) {
    return function () use ($triggerHooks) {
      foreach ($triggerHooks as $webhook) {
        $url = $webhook['url'];
        $data = $webhook['payload'];

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => $data
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        }
    };
  }, $byTrigger);
}

Kirby::plugin('errnesto/wehooks', [
  'hooks' => getHooks()
]);
