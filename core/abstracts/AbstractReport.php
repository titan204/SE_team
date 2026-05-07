<?php

abstract class AbstractReport extends AbstractModel implements ReportGeneratableInterface
{
    private string $reportScope = '';
    private array $reportInputs = [];

    public function getReportScope()
    {
        return $this->reportScope;
    }

    public function setReportScope($scope)
    {
        $this->reportScope = trim((string) $scope);
        return $this;
    }

    public function describeReportInputs(): array
    {
        return $this->reportInputs;
    }

    protected function setReportInputs(array $inputs): void
    {
        $this->reportInputs = $inputs;
    }
}

