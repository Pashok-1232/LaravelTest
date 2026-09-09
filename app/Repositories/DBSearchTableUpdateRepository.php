<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DBSearchTableUpdateRepository
{
    public function insertQuery(array $data): bool
    {
        /*DB::transaction(function () {
            DB::table('users')->update(['votes' => 1]);
            DB::table('posts')->delete();
        });*/
        DB::beginTransaction();

        try
        {
            $chunks = array_chunk($data, 500);
            
            foreach ($chunks as $chunk)
            {
                DB::table('books_searches')->insert($chunk); // batch!
            }

            DB::commit();
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            dd($e->getTraceAsString(), $e->getMessage());
            return false;
        }
    }
}
?>