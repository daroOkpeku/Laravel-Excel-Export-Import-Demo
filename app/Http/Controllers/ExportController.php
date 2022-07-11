<?php

namespace App\Http\Controllers;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Collection;

class ExportController extends Controller
{
    public function array()
    {
        $handle = fopen(public_path('image/users.csv'), 'w');
        User::chunk(2000, function ($users) use ($handle) {
            // foreach ($users->toArray() as $user) {
            //     fputcsv($handle, $user);
            // }
            // collect
              collect($users->toArray())->map(function ($user) use ($handle) {
                fputcsv($handle, $user);
              });
        });

        fclose($handle);

        return Storage::disk('public')->download('export.csv');
    }

    public function excel()
    {
        return Excel::download(new UsersExport, 'users.csv');
    }

    public function spatie()
    {
        $rows = [];

        User::chunk(2000, function ($users) use (&$rows) {
            // foreach ($users->toArray() as $user) {
            //     $rows[] = $user;
            // }
            collect($users->toArray())->map(function ($user){
                $rows[] = $user;
              });
        });

        SimpleExcelWriter::streamDownload('users.csv')
            ->noHeaderRow()
            ->addRows($rows);
    }
}