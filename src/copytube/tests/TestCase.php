<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @package Tests
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
