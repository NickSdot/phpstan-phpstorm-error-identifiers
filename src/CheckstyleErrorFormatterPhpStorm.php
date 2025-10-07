<?php

declare(strict_types=1);

namespace NickSdot\PhpStanPhpStormErrorIdentifiers;

use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\Output;
use PHPStan\File\RelativePathHelper;

use function count;
use function htmlspecialchars;
use function sprintf;

/** @api */
final readonly class CheckstyleErrorFormatterPhpStorm implements ErrorFormatter
{
    public function __construct(private RelativePathHelper $relativePathHelper) {}

    public function formatErrors(
        AnalysisResult $analysisResult,
        Output $output,
    ): int {
        $output->writeRaw('<?xml version="1.0" encoding="UTF-8"?>');
        $output->writeLineFormatted('');
        $output->writeRaw('<checkstyle>');
        $output->writeLineFormatted('');

        foreach ($this->groupByFile($analysisResult) as $relativeFilePath => $errors) {
            $output->writeRaw(sprintf(
                    '<file name="%s">',
                    $this->escape($relativeFilePath),
            ));
            $output->writeLineFormatted('');

            foreach ($errors as $error) {

                // Note:
                // The "source" attribute is ignored; also we need better formatting for own solutions.
                // https://youtrack.jetbrains.com/issue/WI-81974/PHPStan-Show-source-of-the-message-too
                // https://youtrack.jetbrains.com/issue/WI-78524/PHPStan-Make-it-possible-to-use-more-detailed-error-reporting-formats
                $identifierFix = null !== $error->getIdentifier()
                    ? "    // @phpstan-ignore {$error->getIdentifier()}" :
                    '';

                $output->writeRaw(sprintf(
                    '<error line="%d" column="1" severity="error" message="%s" />',
                        $this->escape((string) $error->getLine()),
                    $this->escape($error->getMessage() . $identifierFix),
                ));
                $output->writeLineFormatted('');
            }
            $output->writeRaw('</file>');
            $output->writeLineFormatted('');
        }

        $notFileSpecificErrors = $analysisResult->getNotFileSpecificErrors();

        if (count($notFileSpecificErrors) > 0) {
            $output->writeRaw('<file>');
            $output->writeLineFormatted('');

            foreach ($notFileSpecificErrors as $error) {
                $output->writeRaw(sprintf('  <error severity="error" message="%s" />', $this->escape($error)));
                $output->writeLineFormatted('');
            }

            $output->writeRaw('</file>');
            $output->writeLineFormatted('');
        }

        if ($analysisResult->hasWarnings()) {
            $output->writeRaw('<file>');
            $output->writeLineFormatted('');

            foreach ($analysisResult->getWarnings() as $warning) {
                $output->writeRaw(sprintf('  <error severity="warning" message="%s" />', $this->escape($warning)));
                $output->writeLineFormatted('');
            }

            $output->writeRaw('</file>');
            $output->writeLineFormatted('');
        }

        $output->writeRaw('</checkstyle>');
        $output->writeLineFormatted('');

        return $analysisResult->hasErrors() ? 1 : 0;
    }

    /** Escapes values for using in XML */
    private function escape(string $string): string
    {
        return htmlspecialchars($string, \ENT_XML1 | \ENT_COMPAT, 'UTF-8');
    }

    /**
     * Group errors by file
     *
     * @return Error Array that have as key the relative path of file
     * and as value an array with occurred errors.
     */
    private function groupByFile(AnalysisResult $analysisResult): array
    {
        $files = [];

        /** @var Error $fileSpecificError */
        foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
            $absolutePath = $fileSpecificError->getFilePath();
            if (null !== $fileSpecificError->getTraitFilePath()) {
                $absolutePath = $fileSpecificError->getTraitFilePath();
            }
            $relativeFilePath = $this->relativePathHelper->getRelativePath(
                $absolutePath,
            );

            $files[$relativeFilePath][] = $fileSpecificError;
        }

        return $files;
    }

}
