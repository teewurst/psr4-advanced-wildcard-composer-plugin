#!/usr/bin/env php
<?php
/**
 * Validates that code coverage meets the required threshold.
 * Exits with 0 if coverage is sufficient, 1 otherwise.
 */

$coverageFile = __DIR__ . '/../coverage.xml';
$threshold = (int) (getenv('COVERAGE_THRESHOLD') ?: 100);

if (!file_exists($coverageFile)) {
    echo "ERROR: coverage.xml not found. Run 'composer coverage' first.\n";
    exit(1);
}

$coverage = @simplexml_load_file($coverageFile);
if ($coverage === false) {
    echo "ERROR: Could not parse coverage.xml\n";
    exit(1);
}

$projectMetrics = $coverage->project->metrics ?? null;

if (!$projectMetrics) {
    echo "ERROR: No project metrics found in coverage.xml\n";
    exit(1);
}

$statements = (int) $projectMetrics['statements'];
$coveredStatements = (int) $projectMetrics['coveredstatements'];
$methods = (int) $projectMetrics['methods'];
$coveredMethods = (int) $projectMetrics['coveredmethods'];

$stmtCoverage = $statements > 0 ? ($coveredStatements / $statements) * 100 : 100;
$methodCoverage = $methods > 0 ? ($coveredMethods / $methods) * 100 : 100;

printf(
    "Coverage: %.1f%% statements (%d/%d), %.1f%% methods (%d/%d)\n",
    $stmtCoverage,
    $coveredStatements,
    $statements,
    $methodCoverage,
    $coveredMethods,
    $methods
);

if ($stmtCoverage < $threshold || $methodCoverage < $threshold) {
    echo "\n";
    echo "❌ FAIL: Coverage below {$threshold}% threshold. Write more tests!\n";
    exit(1);
}

echo "\n✅ PASS: 100% code coverage maintained. Nice work!\n";
exit(0);
