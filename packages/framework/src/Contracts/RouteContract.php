<?php

namespace Hyde\Framework\Contracts;

use Hyde\Framework\Concerns\HydePage;
use Illuminate\Support\Collection;

/**
 * @deprecated Will be merged into Route.php
 * This contract defines the methods that a Route object must implement.
 */
interface RouteContract
{
    public function __construct(HydePage $sourceModel);

    public function getPageType(): string;

    public function getSourceModel(): HydePage;

    public function getRouteKey(): string;

    public function getSourceFilePath(): string;

    public function getOutputFilePath(): string;

    public function getQualifiedUrl(): string;

    public function getLink(): string;

    public function __toString(): string;

    public static function get(string $routeKey): \Hyde\Framework\Models\Route;

    public static function getFromKey(string $routeKey): \Hyde\Framework\Models\Route;

    public static function getFromSource(string $sourceFilePath): \Hyde\Framework\Models\Route;

    public static function getFromModel(HydePage $page): \Hyde\Framework\Models\Route;

    public static function all(): Collection;

    public static function current(): \Hyde\Framework\Models\Route;

    public static function home(): \Hyde\Framework\Models\Route;

    public static function exists(string $routeKey): bool;
}
