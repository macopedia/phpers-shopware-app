<?php declare(strict_types=1);

namespace App\Test;

use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait DatabaseTransactionBehaviour
{
    public static $lastTestCase;

    private static $nextNestTransactionsWithSavepoints = true;

    public function disableNestTransactionsWithSavepointsForNextTest(): void
    {
        self::$nextNestTransactionsWithSavepoints = false;
    }

    /**
     * @before
     */
    public function startTransactionBefore(): void
    {
        self::assertNull(
            static::$lastTestCase,
            'The previous test case\'s transaction was not closed properly.
            This may affect following Tests in an unpredictable manner!
            Previous Test case: ' . (new \ReflectionClass($this))->getName() . '::' . static::$lastTestCase
        );

        $this->getContainer()->get(Connection::class)
            ->setNestTransactionsWithSavepoints(self::$nextNestTransactionsWithSavepoints);

        $this->getContainer()
            ->get(Connection::class)
            ->beginTransaction();

        static::$lastTestCase = $this->getName();
    }

    /**
     * @after
     */
    public function stopTransactionAfter(): void
    {
        $connection = $this->getContainer()
            ->get(Connection::class);

        self::assertEquals(
            1,
            $connection->getTransactionNestingLevel(),
            'Too many Nesting Levels.
            Probably one transaction was not closed properly.
            This may affect following Tests in an unpredictable manner!
            Current nesting level: "' . $connection->getTransactionNestingLevel() . '".'
        );

        $connection->rollBack();

        self::$nextNestTransactionsWithSavepoints = true;

        if (static::$lastTestCase === $this->getName()) {
            static::$lastTestCase = null;
        }
    }

    abstract protected static function getContainer(): ContainerInterface;
}