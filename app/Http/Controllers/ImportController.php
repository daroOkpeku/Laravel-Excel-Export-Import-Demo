<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportController extends Controller
{
    public function array(Request $request)
    {
        $users = array_map('str_getcsv', file($request->file));
        $names = [];
 
     

        // foreach ($users as $user) {
        //     $name = (string) Str::of($user[1])->before(' ');

        //     if (! array_key_exists($name, $names)) {
        //         $names[$name] = 0;
        //     }

        //     $names[$name]++;
        // }
     $data =     collect($users)->map(function ($user) use($names){
               $name = (string) Str::of($user[1])->before(' ');
            if (! array_key_exists($name, $names)) {
                // dd($names[$name]);
                $names[$name] = 0;
               
            }

          $names[$name]++;
          arsort($names);
          dump(array_slice($names, 0, 10));
          });


    }

    public function excel(Request $request)
    {
        $users = (new UsersImport)->toArray($request->file);
        $names = [];
        $data =     collect($users)->map(function ($user) use($names){
            collect($user)->map(function ($used) use($names){

                $name = (string) Str::of($used[1])->before(' ');
                if (! array_key_exists($name, $names)) {
                    // dd($names[$name]);
                    $names[$name] = 0;
                   
                }
    
              $names[$name]++;
              arsort($names);
              dump(array_slice($names, 0, 10)); 
            }); 
        
        });

        // foreach ($users[0] as $user) {
        //     $name = (string) Str::of($user[1])->before(' ');

        //     if (! array_key_exists($name, $names)) {
        //         $names[$name] = 0;
        //     }

        //     $names[$name]++;
        // }

        // arsort($names);

        // dump(array_slice($names, 0, 10));
    }

    public function spatie(Request $request)
    {
        SimpleExcelReader::create($request->file, 'csv')
            ->noHeaderRow()
            ->getRows()
            ->map(fn ($user) => (string) Str::of($user[1])->before(' '))
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->dump();
    }
}
