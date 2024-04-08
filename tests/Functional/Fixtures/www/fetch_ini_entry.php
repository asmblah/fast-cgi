<?php

$entry = $_GET['entry'] ?? null;

if ($entry === null) {
    throw new RuntimeException('Missing "entry" querystring argument');
}

print 'INI entry value: ' . get_cfg_var($entry);
