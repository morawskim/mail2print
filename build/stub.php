#!/usr/bin/env php
<?php

Phar::mapPhar();

include "phar://" . __FILE__ . "/src/application.php";

__HALT_COMPILER();