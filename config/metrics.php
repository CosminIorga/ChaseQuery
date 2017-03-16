<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/03/17
 * Time: 11:29
 */

/**
 * Here resides all the metrics the system uses, such as:
 * extra-loads a partition or a table will add to a server
 */
return [
    'loads' => [
        /* Between 0.5 and 3.0 Default: 1 */
        'partition' => 1.0,
        /* Between 3.0 and 10. Default: 5 */
        'table' => 4.5
    ]
];