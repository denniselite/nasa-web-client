<?php
$db = require(__DIR__ . '/mongodb.php');
// test database! Important not to run tests on production or development databases
$db['dsn'] = 'mongodb://mongodb:27017/nasa';

return $db;