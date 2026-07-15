<?php

namespace App\Http\Controllers\Concerns;

trait InteractsWithFormSubmissions
{
    protected function findProcessedFormSubmission(string $scope, ?string $token)
    {
        if (!$token) {
            return null;
        }

        $processedSubmissions = session($this->submissionSessionKey($scope), []);

        return $processedSubmissions[$token] ?? null;
    }

    protected function rememberProcessedFormSubmission(string $scope, string $token, $value, int $maxEntries = 50): void
    {
        $processedSubmissions = session($this->submissionSessionKey($scope), []);
        $processedSubmissions[$token] = $value;

        if (count($processedSubmissions) > $maxEntries) {
            $processedSubmissions = array_slice($processedSubmissions, -$maxEntries, null, true);
        }

        session([$this->submissionSessionKey($scope) => $processedSubmissions]);
    }

    protected function forgetProcessedFormSubmission(string $scope, ?string $token): void
    {
        if (!$token) {
            return;
        }

        $processedSubmissions = session($this->submissionSessionKey($scope), []);

        if (!array_key_exists($token, $processedSubmissions)) {
            return;
        }

        unset($processedSubmissions[$token]);
        session([$this->submissionSessionKey($scope) => $processedSubmissions]);
    }

    protected function submissionSessionKey(string $scope): string
    {
        return 'processed_form_submissions.' . $scope;
    }
}
