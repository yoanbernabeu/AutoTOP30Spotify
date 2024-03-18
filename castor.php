<?php

use Castor\Attribute\AsTask;

use function Castor\run;

#[AsTask(description: 'Welcome to Castor!')]
function start(): void
{
    run('symfony serve -d');
    run('symfony open:local');
}

#[AsTask(description: 'Stop the Symfony server')]
function stop(): void
{
    run('symfony server:stop');
}