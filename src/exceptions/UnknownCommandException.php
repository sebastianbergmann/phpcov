<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCOV;

use function sprintf;
use RuntimeException;

final class UnknownCommandException extends RuntimeException implements Exception
{
    public function __construct(string $command)
    {
        parent::__construct(
            sprintf(
                'Unknown command "%s"',
                $command
            )
        );
    }
}
