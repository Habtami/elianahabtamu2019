<?php

namespace App\Helpers;

use ZipArchive;

class ExcelExport
{
    protected $headers = [];

    protected $rows = [];

    protected $sheetName = 'Sheet1';

    public function __construct(array $headers = [], string $sheetName = 'Sheet1')
    {
        $this->headers = $headers;
        $this->sheetName = $sheetName;
    }

    public function addRow(array $row): void
    {
        $this->rows[] = $row;
    }

    public function addRows(array $rows): void
    {
        foreach ($rows as $row) {
            $this->rows[] = $row;
        }
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function download(string $filename): void
    {
        $content = $this->generateXlsx();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: public');
        header('Content-Length: '.strlen($content));
        echo $content;
        exit;
    }

    protected function generateXlsx(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'excel');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->getContentTypes());
        $zip->addFromString('_rels/.rels', $this->getRels());
        $zip->addFromString('xl/workbook.xml', $this->getWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->getWorkbookRels());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->getSheet1());
        $zip->addFromString('xl/sharedStrings.xml', $this->getSharedStrings());

        $zip->close();
        $content = file_get_contents($tmp);
        unlink($tmp);

        return $content;
    }

    protected function getContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>';
    }

    protected function getRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }

    protected function getWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets>
<sheet name="'.$this->sheetName.'" sheetId="1" r:id="rId1"/>
</sheets>
</workbook>';
    }

    protected function getWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
    }

    protected function getSharedStrings(): string
    {
        [, $strings] = $this->buildSharedStringIndex();

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $xml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">'."\n";
        foreach ($strings as $str) {
            $xml .= '<si><t>'.htmlspecialchars($str, ENT_XML1).'</t></si>';
        }
        $xml .= '</sst>';

        return $xml;
    }

    /**
     * Prefixed keys keep numeric strings like "1" and "01" from collapsing
     * into the same PHP array key, which would corrupt shared-string indices.
     */
    protected function buildSharedStringIndex(): array
    {
        $index = [];
        $strings = [];

        $add = function ($s) use (&$index, &$strings) {
            if ($s === '') {
                return;
            }
            $key = 's:'.$s;
            if (! isset($index[$key])) {
                $index[$key] = count($strings);
                $strings[] = $s;
            }
        };

        foreach ($this->convertCellsToStrings([$this->headers]) as $rowStrings) {
            foreach ($rowStrings as $s) {
                $add($s);
            }
        }

        foreach ($this->rows as $row) {
            foreach ($row as $cell) {
                $add($this->cellToString($cell));
            }
        }

        return [$index, $strings];
    }

    protected function getSheet1(): string
    {
        [$stringIndex] = $this->buildSharedStringIndex();

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'."\n";
        $xml .= '<sheetData>';

        $rowNum = 1;
        $headerCellsRow = $this->convertCellsToStrings([$this->headers])[0];
        $xml .= '<row r="'.$rowNum.'">';
        $colNum = 1;
        foreach ($headerCellsRow as $str) {
            $cellRef = $this->getCellReference($colNum, $rowNum);
            if ($str !== '') {
                $idx = $stringIndex['s:'.$str];
                $xml .= '<c r="'.$cellRef.'" t="s"><v>'.$idx.'</v></c>';
            } else {
                $xml .= '<c r="'.$cellRef.'"></c>';
            }
            $colNum++;
        }
        $xml .= '</row>';
        $rowNum++;

        foreach ($this->rows as $row) {
            $xml .= '<row r="'.$rowNum.'">';
            $colNum = 1;
            foreach ($row as $cell) {
                $cellRef = $this->getCellReference($colNum, $rowNum);
                $str = $this->cellToString($cell);
                if ($str !== '') {
                    $idx = $stringIndex['s:'.$str];
                    $xml .= '<c r="'.$cellRef.'" t="s"><v>'.$idx.'</v></c>';
                } else {
                    $xml .= '<c r="'.$cellRef.'"></c>';
                }
                $colNum++;
            }
            $xml .= '</row>';
            $rowNum++;
        }

        $xml .= '</sheetData>';
        $xml .= '</worksheet>';

        return $xml;
    }

    protected function convertCellsToStrings(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = array_map([$this, 'cellToString'], $row);
        }

        return $result;
    }

    protected function cellToString($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }
        if (is_numeric($value)) {
            return (string) $value;
        }

        return (string) $value;
    }

    protected function getCellReference(int $col, int $row): string
    {
        $colLetters = '';
        $colIndex = $col;
        while ($colIndex > 0) {
            $remainder = ($colIndex - 1) % 26;
            $colLetters = chr(65 + $remainder).$colLetters;
            $colIndex = intval(($colIndex - $remainder) / 26);
        }

        return $colLetters.$row;
    }
}
