<?php

interface ReportGeneratableInterface
{
    public function getReportScope();

    public function setReportScope($scope);

    public function describeReportInputs(): array;
}

