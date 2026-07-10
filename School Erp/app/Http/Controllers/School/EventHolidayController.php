<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EventHolidayController extends Controller
{
    private function ensureEventsSeeded($schoolId)
    {
        // No auto-seeding
    }

    public function eventManagement(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureEventsSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:150',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'is_holiday' => 'nullable|boolean',
            ]);

            Event::create([
                'school_id' => $schoolId,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_holiday' => $request->has('is_holiday') ? true : false,
            ]);

            return back()->with('success', 'Event / Holiday created successfully!');
        }

        $events = Event::where('school_id', $schoolId)->orderBy('start_date', 'asc')->get();
        return view('school.events.index', compact('events'));
    }

    /**
     * Download the CSV import template for events.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Event Title*', 'Description', 'Start Date* (yyyy-mm-dd)', 'End Date* (yyyy-mm-dd)', 'Official School Holiday* (Yes/No)'
        ];
        
        $exampleRows = [
            ['Independence Day Celebration', 'Flag hoisting ceremony and cultural events.', '2026-08-15', '2026-08-15', 'Yes'],
            ['Summer Vacation', 'School closed for summer break.', '2026-05-15', '2026-06-15', 'Yes'],
            ['Annual Science Exhibition', 'Students will display science projects.', '2026-11-20', '2026-11-21', 'No']
        ];

        return response()->streamDownload(function () use ($headers, $exampleRows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            foreach ($exampleRows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 'event_import_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="event_import_template.csv"',
        ]);
    }

    /**
     * Process bulk event uploads via CSV or Excel.
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|max:10240',
        ]);

        $schoolId = auth()->user()->school_id;
        $file = $request->file('import_file');

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to read file: ' . $e->getMessage());
        }

        if (empty($rows) || count($rows) <= 1) {
            return back()->with('error', 'The uploaded spreadsheet is empty.');
        }

        $headerRow = $rows[0];
        $normalizeHeader = function($name) {
            $name = strtolower(trim((string)$name));
            $name = preg_replace('/\s*\(.*?\)\s*/', '', $name); // Remove (brackets)
            $name = str_replace('*', '', $name);                // Remove *
            return preg_replace('/[\s_-]+/', '', $name);        // Strip spaces/symbols
        };

        // Map column names to index
        $headerMap = [];
        foreach ($headerRow as $index => $rawHeader) {
            if ($rawHeader !== null && $rawHeader !== '') {
                $normalized = $normalizeHeader($rawHeader);
                $headerMap[$normalized] = $index;
            }
        }

        // Validate basic headers exist
        if (!isset($headerMap['eventtitle']) || !isset($headerMap['startdate']) || !isset($headerMap['enddate'])) {
            return back()->with('error', 'Invalid template. Headers must contain Event Title, Start Date, and End Date.');
        }

        $val = function($row, $headerName) use ($headerMap) {
            if (isset($headerMap[$headerName]) && isset($row[$headerMap[$headerName]])) {
                return trim((string)$row[$headerMap[$headerName]]);
            }
            return '';
        };

        $parseDate = function($dateStr) {
            if (empty($dateStr)) return null;
            if (is_numeric($dateStr)) {
                try {
                    return Carbon::instance(Date::excelToDateTimeObject($dateStr))->toDateString();
                } catch (\Exception $e) {
                    return null;
                }
            }
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateStr)) {
                $parts = explode('/', $dateStr);
                return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
            }
            try {
                return Carbon::parse($dateStr)->toDateString();
            } catch (\Exception $e) {
                return null;
            }
        };

        $imported = 0;
        $skipped = 0;
        $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip empty rows
            if (empty($row) || (count($row) === 1 && $row[0] === null)) {
                continue;
            }

            $title = $val($row, 'eventtitle');
            if (empty($title)) {
                $skipped++;
                continue;
            }

            $description = $val($row, 'description');
            $startDateStr = $val($row, 'startdate');
            $endDateStr = $val($row, 'enddate');
            $isHolidayStr = strtolower($val($row, 'officialschoolholiday'));

            $startDate = $parseDate($startDateStr);
            $endDate = $parseDate($endDateStr);

            if (!$startDate || !$endDate) {
                $skipped++;
                $errors[] = "Row " . ($i + 1) . ": Invalid dates ('$startDateStr', '$endDateStr').";
                continue;
            }

            $isHoliday = in_array($isHolidayStr, ['yes', 'y', '1', 'true', 'holiday']);

            Event::create([
                'school_id' => $schoolId,
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_holiday' => $isHoliday,
            ]);

            $imported++;
        }

        $msg = "Bulk import completed! Successfully imported {$imported} events.";
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} rows due to invalid data.";
        }
        if (!empty($errors)) {
            $msg .= " Errors: " . implode(" | ", array_slice($errors, 0, 3));
        }

        return back()->with('success', $msg);
    }

    /**
     * Update an existing event.
     */
    public function updateEvent(Request $request, Event $event)
    {
        $schoolId = auth()->user()->school_id;
        if ($event->school_id !== $schoolId) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_holiday' => 'nullable|boolean',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_holiday' => $request->has('is_holiday') ? true : false,
        ]);

        return back()->with('success', 'Event updated successfully!');
    }

    /**
     * Delete an existing event.
     */
    public function deleteEvent(Event $event)
    {
        $schoolId = auth()->user()->school_id;
        if ($event->school_id !== $schoolId) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();

        return back()->with('success', 'Event deleted successfully!');
    }
}

