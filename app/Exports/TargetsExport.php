<?php

namespace App\Exports;

use App\Models\SalesTarget;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TargetsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $scope;

    public function __construct($filters = [], $scope = null)
    {
        $this->filters = $filters;
        $this->scope = $scope;
    }

    public function query()
    {
        $query = SalesTarget::with([
            'region', 'channel', 'salesman', 'supplier', 'category'
        ]);

        // Apply scope
        if ($this->scope) {
            if (isset($this->scope['region_id'])) {
                $query->where('region_id', $this->scope['region_id']);
            }
            if (isset($this->scope['channel_id'])) {
                $query->where('channel_id', $this->scope['channel_id']);
            }
        }

        // Apply filters
        if (isset($this->filters['year'])) {
            $query->where('year', $this->filters['year']);
        }

        if (isset($this->filters['month'])) {
            $query->where('month', $this->filters['month']);
        }

        if (isset($this->filters['region_id'])) {
            $query->where('region_id', $this->filters['region_id']);
        }

        if (isset($this->filters['channel_id'])) {
            $query->where('channel_id', $this->filters['channel_id']);
        }

        if (isset($this->filters['supplier_id'])) {
            $query->where('supplier_id', $this->filters['supplier_id']);
        }

        if (isset($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['employee_code'])) {
            $query->byEmployeeCode($this->filters['employee_code']);
        }

        return $query->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->orderBy('region_id')
                    ->orderBy('channel_id');
    }

    public function headings(): array
    {
        return [
            'Year',
            'Month',
            'Region Code',
            'Region Name',
            'Channel Code',
            'Channel Name',
            'Employee Code',
            'Salesman Name',
            'Supplier Code',
            'Supplier Name',
            'Category Code',
            'Category Name',
            'Amount (USD)',
            'Notes',
            'Created At',
            'Updated At'
        ];
    }

    public function map($target): array
    {
        return [
            $target->year,
            $target->month,
            $target->region->region_code ?? '',
            $target->region->name ?? '',
            $target->channel->channel_code ?? '',
            $target->channel->name ?? '',
            $target->salesman->employee_code ?? '',
            $target->salesman->name ?? '',
            $target->supplier->supplier_code ?? '',
            $target->supplier->name ?? '',
            $target->category->category_code ?? '',
            $target->category->name ?? '',
            number_format($target->amount, 2),
            $target->notes ?? '',
            $target->created_at->format('Y-m-d H:i:s'),
            $target->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ]
        ];
    }
} 