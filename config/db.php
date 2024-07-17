<?php

return [
    'db' => [
        'class' => 'yii\db\connection',
        'attributes' => [
            PDO::ATTR_STRINGIFY_FETCHES => true,
        ],
        'username' => 'training',
        'password' => 'training123',
        'dsn' => 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=202.158.123.94)(PORT=1521))(CONNECT_DATA=(SID=linux64)(SERVER=DEDICATED)))'
    ],
];
