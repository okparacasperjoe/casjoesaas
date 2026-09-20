<?php

namespace App\Modules\CasjoeERP\Helpers;

class SpreadsheetReader
{
    /**
     * Parse spreadsheet file into 2D array of rows
     *
     * @param string $filePath Absolute path on disk
     * @param string $originalName Original filename
     * @return array<int, array<int, string>>
     */
    public static function read(string $filePath, string $originalName = ''): array
    {
        $ext = strtolower(pathinfo($originalName ?: $filePath, PATHINFO_EXTENSION));

        if ($ext === 'xlsx') {
            return self::readXlsx($filePath);
        } elseif ($ext === 'xls') {
            return self::readXls($filePath);
        } else {
            return self::readCsv($filePath);
        }
    }

    /**
     * Read modern .xlsx (OpenXML Spreadsheet)
     */
    public static function readXlsx(string $filePath): array
    {
        if (!class_exists('\ZipArchive')) {
            return self::readCsv($filePath);
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return self::readCsv($filePath);
        }

        // 1. Read shared strings table
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    $text = '';
                    if (isset($si->t)) {
                        $text = (string)$si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $run) {
                            $text .= (string)$run->t;
                        }
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        // 2. Locate worksheet
        $sheetXmlContent = false;
        $candidates = ['xl/worksheets/sheet1.xml', 'xl/worksheets/sheet.xml'];
        foreach ($candidates as $c) {
            if ($zip->locateName($c) !== false) {
                $sheetXmlContent = $zip->getFromName($c);
                break;
            }
        }

        // If not found in standard candidate locations, check any worksheet in archive
        if ($sheetXmlContent === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (strpos($name, 'xl/worksheets/') === 0 && substr($name, -4) === '.xml') {
                    $sheetXmlContent = $zip->getFromName($name);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXmlContent === false) {
            return self::readCsv($filePath);
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $rowNode) {
            $rowCells = [];
            $maxColIdx = -1;

            foreach ($rowNode->c as $cNode) {
                $ref = (string)$cNode['r'];
                $type = (string)$cNode['t'];

                // Calculate 0-based column index
                $colIdx = 0;
                if (!empty($ref) && preg_match('/^([A-Z]+)[0-9]+$/', $ref, $m)) {
                    $colIdx = self::colLetterToIndex($m[1]);
                } else {
                    $colIdx = $maxColIdx + 1;
                }

                $value = '';
                if ($type === 's') {
                    // Shared string index
                    $idx = (int)$cNode->v;
                    $value = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr' && isset($cNode->is->t)) {
                    $value = (string)$cNode->is->t;
                } elseif (isset($cNode->v)) {
                    $value = (string)$cNode->v;
                }

                $rowCells[$colIdx] = trim($value);
                if ($colIdx > $maxColIdx) {
                    $maxColIdx = $colIdx;
                }
            }

            // Normalize row with empty strings for sparse columns
            $normalizedRow = [];
            for ($i = 0; $i <= $maxColIdx; $i++) {
                $normalizedRow[$i] = $rowCells[$i] ?? '';
            }

            if (!empty(array_filter($normalizedRow, fn($v) => $v !== ''))) {
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    /**
     * Read .xls (XML Spreadsheet 2003, HTML Table, or CSV fallback)
     */
    public static function readXls(string $filePath): array
    {
        $content = @file_get_contents($filePath);
        if ($content === false || strlen($content) === 0) {
            return [];
        }

        // 1. Check for XML Spreadsheet 2003 format
        if (strpos($content, '<Workbook') !== false || strpos($content, 'urn:schemas-microsoft-com:office:spreadsheet') !== false) {
            $rows = self::parseXmlSpreadsheet($content);
            if (!empty($rows)) {
                return $rows;
            }
        }

        // 2. Check for HTML Table export
        if (stripos($content, '<table') !== false && stripos($content, '<tr') !== false) {
            $rows = self::parseHtmlTable($content);
            if (!empty($rows)) {
                return $rows;
            }
        }

        // 3. Fallback to CSV
        return self::readCsv($filePath);
    }

    /**
     * Parse XML Spreadsheet 2003
     */
    private static function parseXmlSpreadsheet(string $xmlContent): array
    {
        $cleanXml = preg_replace('/(<\/?)(ss:|x:|html:)/i', '$1', $xmlContent);
        $xml = @simplexml_load_string($cleanXml);
        if (!$xml) {
            return [];
        }

        $rows = [];
        $worksheets = $xml->xpath('//Worksheet');
        if (empty($worksheets)) {
            $worksheets = [$xml];
        }

        foreach ($worksheets as $ws) {
            $tableRows = $ws->xpath('.//Row');
            foreach ($tableRows as $rowNode) {
                $rowCells = [];
                $cells = $rowNode->xpath('./Cell');
                foreach ($cells as $cell) {
                    $data = $cell->xpath('./Data');
                    $val = !empty($data) ? (string)$data[0] : '';
                    $rowCells[] = trim($val);
                }
                if (!empty(array_filter($rowCells, fn($v) => $v !== ''))) {
                    $rows[] = $rowCells;
                }
            }
            if (!empty($rows)) {
                break;
            }
        }

        return $rows;
    }

    /**
     * Parse HTML table disguised as .xls
     */
    private static function parseHtmlTable(string $html): array
    {
        $rows = [];
        if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $html, $rowMatches)) {
            foreach ($rowMatches[1] as $rowHtml) {
                if (preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $rowHtml, $cellMatches)) {
                    $rowCells = array_map(function($c) {
                        return trim(html_entity_decode(strip_tags($c), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    }, $cellMatches[1]);

                    if (!empty(array_filter($rowCells, fn($v) => $v !== ''))) {
                        $rows[] = $rowCells;
                    }
                }
            }
        }
        return $rows;
    }

    /**
     * Read CSV / TXT / Tab-delimited files
     */
    public static function readCsv(string $filePath): array
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        // Auto-detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $cnt = substr_count($firstLine, $d);
            if ($cnt > $maxCount) {
                $maxCount = $cnt;
                $bestDelimiter = $d;
            }
        }

        $rows = [];
        while (($row = fgetcsv($handle, 4000, $bestDelimiter, '"', '\\')) !== false) {
            if (empty($rows) && isset($row[0])) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            }
            $cleanRow = array_map('trim', $row);
            if (!empty(array_filter($cleanRow, fn($v) => $v !== ''))) {
                $rows[] = $cleanRow;
            }
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Convert Excel column letter (e.g. A, B, Z, AA, AB) to 0-based column index
     */
    public static function colLetterToIndex(string $letters): int
    {
        $letters = strtoupper(trim($letters));
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - 64);
        }
        return max(0, $idx - 1);
    }
}
