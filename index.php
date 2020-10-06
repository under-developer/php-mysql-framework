<?php
require_once "func-bluid.php";


// Only create database(s)


$array = [
    "database" => [
        "creata_a_database",
        "database_new_2"
    ]
];


//create database(s) with table(s)

$array = [
    "database" => [
        
        "db_and_table" => [
            "my_table" => [
                "my_col1" => "INT PRIMARY KEY",
                "my_col2" => [
                    "type" => "TEXT",
                    "default" => "im 'a default value"
                ],
                "my_col3" => "varchar(255)"
            ]
        ],

        "db_and_table_2" => [
            "my_table" => [
                "my_col" => "varchar(255)"
            ]
        ],

    ]
];

//Create only table(s)

$array = [
    "table" => [
        "table_name" => [
            "column" => [
                "id" => "INT",
                "name" => [
                    "type"=> "text",
                    "default" => "My value is default"
                ]
            ]
        ],
        "table_name_2" => [
            "column" => [
                "id" => "INT"
            ]
        ]
    ]
];

//Create table(s) with selected database

$array = [
    "table" => [

        "table_name" => [
            "database" => "test123", //Database name , if it does not exist it is added to the default database
            "column" => [
                "id" => "INT",
                "name" => [
                    "type"=> "text",
                    "default" => "My value is default"
                ]
            ]
        ],
        "table_name_2" => [
            "column" => [
                "id" => "INT"
            ]
        ]
    ]
];

//This a test

$array = [
    "database" => [
        "test123456789" => [
            "test_table" => [
                "its_working" => "INT",
                "yea" => [
                    "type" => "TEXT"
                ],
                "i_have_default_value" => [
                    "type" => "TEXT",
                    "default" => "Im value"
                ]
            ]
        ]
    ],
    "table" => [
        "table_test" => [
            "database" => "test123456789",
            "column" => [
                "im_new" => "TEXT"
            ]
        ]
    ]
];

var_dump($mysql->create($array));
