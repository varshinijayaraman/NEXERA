<?php
declare(strict_types=1);

namespace Nexera\Models;

use Nexera\Config;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Config\getPDO();
    }
}


