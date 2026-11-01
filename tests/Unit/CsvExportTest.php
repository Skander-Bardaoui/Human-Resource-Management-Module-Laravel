<?php

use App\Support\CsvExport;

it('neutralises spreadsheet formulas in text cells', function () {
    expect(CsvExport::text('=SUM(A1:A3)'))->toBe("'=SUM(A1:A3)")
        ->and(CsvExport::text('+216 98 000 000'))->toBe("'+216 98 000 000")
        ->and(CsvExport::text('-danger'))->toBe("'-danger")
        ->and(CsvExport::text('@command'))->toBe("'@command")
        ->and(CsvExport::text("\ttab"))->toBe("'\ttab")
        ->and(CsvExport::text("\rretour"))->toBe("'\rretour")
        ->and(CsvExport::text('normal'))->toBe('normal')
        ->and(CsvExport::text(null))->toBe('');
});

it('strips null bytes from text cells', function () {
    expect(CsvExport::text("AB\0CD"))->toBe('ABCD');
});
