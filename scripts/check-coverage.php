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
    echo "❌ FAIL: Coverage below {$threshold}% threshold. Write more tests!\n\n";
    echo "--- Uncovered code (add tests for these) ---\n\n";

    $projectRoot = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
    foreach ($coverage->xpath('//file') as $file) {
        $filepath = (string) $file['name'];
        $normalized = str_replace('\\', '/', $filepath);
        $relativePath = $filepath;
        if (strpos($normalized, '/src/') !== false) {
            $relativePath = 'src/' . substr($normalized, strrpos($normalized, '/src/') + 5);
        } elseif (strpos($normalized, 'src/') === 0) {
            $relativePath = $normalized;
        } elseif (preg_match('#[^/]+/src/(.+)$#', $normalized, $m)) {
            $relativePath = 'src/' . $m[1];
        } else {
            $relativePath = basename($normalized);
        }

        $uncoveredLines = [];
        foreach ($file->line as $line) {
            $count = (int) ($line['count'] ?? -1);
            $type = (string) ($line['type'] ?? '');
            if (($type === 'stmt' || $type === 'cond') && $count === 0) {
                $uncoveredLines[] = (int) $line['num'];
            }
        }

        if (!empty($uncoveredLines) && strpos($relativePath, 'src/') === 0) {
            sort($uncoveredLines);
            $linesSummary = count($uncoveredLines) <= 10
                ? implode(', ', $uncoveredLines)
                : implode(', ', array_slice($uncoveredLines, 0, 8)) . ', ... (+' . (count($uncoveredLines) - 8) . ' more)';
            echo "  {$relativePath}: lines {$linesSummary}\n";
        }
    }

    echo "\nTip: Run 'composer coverage' locally and open coverage report, or add unit tests for the lines above.\n";
    exit(1);
}

echo "\n✅ PASS: 100% code coverage maintained. Nice work!\n";
exit(0);
