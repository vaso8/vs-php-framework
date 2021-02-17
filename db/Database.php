<?php


namespace App\Core\DB;


class Database
{
    public \PDO $pdo;

    /**
     * Database constructor.
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigratins = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR . '/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigratins);

        foreach($toApplyMigrations as $migration) {
            if($migration === '.' || $migration === '..') {
                continue;
            }
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);

            $instance = new $className;

            $this->log('Applying Migration: ' . $migration);
            $instance->up();
            $this->log('Applied Migration: ' . $migration);

            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            echo 'Nothing To migrate!';
        }
        //var_dump($toApplyMigrations);

    }

    public function saveMigrations($migrations)
    {
        $values = implode(",",array_map(fn($m) => "('$m')", $migrations));
        $this->pdo->exec("INSERT INTO migrations (migration) VALUES $values");
    }

    public  function createMigrationsTable()
    {
        $statement = $this->pdo->prepare("CREATE TABLE  IF NOT EXISTS `phpfw`.`migrations` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `migration` VARCHAR(255) NULL,
          `created_at` TIMESTAMP(6) NULL,
          PRIMARY KEY (`id`));
        ");

        $statement->execute();
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    public function log($msg)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $msg . PHP_EOL;
    }
}