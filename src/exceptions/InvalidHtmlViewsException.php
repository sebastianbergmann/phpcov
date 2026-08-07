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

final class InvalidHtmlViewsException extends RuntimeException implements Exception
{
    public function __construct(string $value)
    {
        parent::__construct(
            sprintf(
                'Invalid value "%s" for "--html-views", expected a comma-separated list of "file" and "class"',
                $value,
            ),
        );
    }
}
